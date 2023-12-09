/**
 * JS für Spielplan-Erstellung
 */

define([ 'jquery' ], function(jQuery) {
	var T3SMatchCreator = {
		init: function() {
			const self = this;
	
			const cb = document.querySelector('input[name="option_leadingZero"]');
			cb.addEventListener('click', function(e) {
				self.prependZero(this);
			});
	
			console.info('MatchCreator initialized.');
		},
		prependZero: function(item) {
			var checked = item.checked;
			jQuery('.roundname').each(function(idx, el) {
				var v = jQuery(el).val();
				if (!checked) {
					v = v.match(/^0\d/) ? v.substr(1) : v;
				}
				else {
					v = v.match(/^\d\./) ? '0'+v : v;
				}
				jQuery(el).val(v);
			});
		},
	};
	T3SMatchCreator.init();
});
