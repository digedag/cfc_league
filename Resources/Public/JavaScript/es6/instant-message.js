import $ from 'jquery';
import * as jeditable from '@digedag/cfc_league/jeditable.js';

var InstantMessage = {
    previewField : null
};

InstantMessage.init = function() {
    $('#instant').editable(TYPO3.settings.ajaxUrls['t3sports_ticker'], {
            placeholder: 'Klicken Sie hier, um eine Sofortmeldung abzusetzen.',
            onblur: 'ignore',
            cancel: 'cancel',
            submit: 'ok',
            event: 'click',
            submitdata: function(){
                return {
                    t3time: $('#editform').find('input[name=watch_minute]').val(),
                    t3match: $('#editform').find('input[name=t3matchid]').val()
                }
            },
            indicator: 'Speichern ....',
            callback: function(value, settings) {
                // Check if the response contains an error message
                if (value && value.error) {
                    console.error('Fehler beim Speichern der Sofortmeldung:', value.error);
                } else {
                    console.info('Sofortmeldung erfolgreich gespeichert.');
                    $('#instant').css('background-color', 'yellow'); // Reset background color
                }
            },
            onerror: function(settings, original, xhr) {
                console.error('Fehler beim Senden der Sofortmeldung:', xhr.statusText);
                $('#instant').css('background-color', '#ffcccc'); // Set background color to red
                $('#instant').text('Es ist ein Fehler aufgetreten: ' + xhr.statusText);
            }
    });
    console.info('Instant message initialized.');
};

InstantMessage.init();
