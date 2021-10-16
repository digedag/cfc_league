/**
 * JS für Spielplan-Erstellung
 */

define([ 'jquery' ], function(jQuery) {
	var T3SMatchCreator = {
		init: function(info) {
			window.t3sMatchCreator = this;
			console.info({hello: info});
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
	return T3SMatchCreator;
});
