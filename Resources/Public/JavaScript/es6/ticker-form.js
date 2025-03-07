var TickerForm = {
    previewField: null,
    clickedButton: null,

    init: function (info) {
        window.TickerForm = this;

        // Event Listener für das Formular hinzufügen
        const form = document.forms[0];
        if (form) {
            form.addEventListener("submit", function () {
                if (TickerForm.clickedButton) {
                    let input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "btnClicked";
                    input.value = TickerForm.clickedButton;
                    form.appendChild(input);
                }
            });

            // Button-Klicks erfassen
            const buttons = form.querySelectorAll("button[type='submit']");
            buttons.forEach((button) => {
                button.addEventListener("click", function () {
                    TickerForm.clickedButton = this.name;
                });
            });
        }

        // Event-Listener für Ticker-Felder hinzufügen
        const tickerFields = document.getElementsByClassName("tickerField");
        for (let i = 0; i < tickerFields.length; i++) {
            tickerFields[i].addEventListener("input", function (evt) {
                TickerForm.setMatchMinute(evt.target);
            });
        }

        this.ticker();
    },

    pause: function () {
        const form = document.forms[0];
        if (!form) return;

        form.watch_localtime.value = Date.now();
        setTimeout(() => {
            TickerForm.pause();
        }, 1000);
    },

    toTime: function (tstamp) {
        return new Date(tstamp).toLocaleString() + " (" + tstamp + ")";
    },

    ticker: function () {
        const form = document.forms[0];
        if (!form) return;

        form.watch_localtime.value = Date.now();

        const paused = parseInt(form.watch_pausetime.value);
        const start = parseInt(form.watch_starttime.value);
        if (start > 0) {
            let offset = this.trim(form.watch_offset.value);
            offset = parseInt(isNaN(offset) || offset === "" ? 0 : offset);

            const diff = new Date((paused > 0 ? paused : Date.now()) - start);
            const std = diff.getHours();
            const min = diff.getMinutes() + ((std - 1) * 60) + offset;
            const sec = diff.getSeconds();

            form.watch_minute.value = min + 1;
            form.watch.value = (min > 9 ? min : "0" + min) + ":" + (sec > 9 ? sec : "0" + sec);
        }

        setTimeout(() => {
            paused === 0 ? TickerForm.ticker() : TickerForm.pause();
        }, 1000);
    },

    trim: function (str) {
        return str ? str.replace(/\s+/, "") : "";
    },

    setMatchMinute: function (elem) {
        console.info("Klick", { elem: elem });
        const form = elem.form;
        if (!form) return;

        const min = form.watch_minute.value;
        if (min == 0) {
            return;
        }

        const match = elem.name.match(/NEW(\d+)/);
        if (!match) return;
        const line = match[1];

        const elements = form.querySelectorAll("input[name^='data[tx_cfcleague_match_notes][NEW']");
        elements.forEach((input) => {
            if (input.name === `data[tx_cfcleague_match_notes][NEW${line}][minute]` && input.value === "") {
                input.value = min;
                const dataElement = form.querySelector(`[data-formengine-input-name='${input.name}']`);
                if (dataElement) {
                    dataElement.value = min;
                }
            }
        });
    }
};

TickerForm.init("Ticker");
