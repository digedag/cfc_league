define([ 'jquery', 'jeditable'], function($, jeditable) {
	var InstantMessage = {
		previewField : null
	};
	
	InstantMessage.init = function(ajaxUrl) {
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
	};

	// To let the module be a dependency of another module, we return our object
	return InstantMessage;
});
