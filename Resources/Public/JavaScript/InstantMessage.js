define([ 'TYPO3/CMS/CfcLeague/jeditable.min', 'jquery' ], function(jeditable, $) {
    var InstantMessage = {
        previewField : null
    };

    InstantMessage.init = function(initMsg) {
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
            indicator: 'Speichern ....'
        });
        console.info(initMsg);
    };

    // To let the module be a dependency of another module, we return our object
    return InstantMessage;
});
