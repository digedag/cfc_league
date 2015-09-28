
var T3SMatchCreator = Class.create({
	// Leading zero at match days
	prependZero: function(item) {
		var checked = item.checked;
		// Über alle Spieltage iterieren
		var fields = Ext.query(".roundname");
		Ext.select('.roundname').each(function(el){
			var v = el.getValue();
			if (!checked) {
				if(v.match(/^0\d/))
					v = v.substr(1);
			}
			else {
				if(v.match(/^\d\./))
					v = '0'+v;
			}
			el.dom.value = v;
		});

//		matchid = $('editform').t3matchid.value;
//		var params = form.serialize();
//		return params;
//		alert("--"+fields);
	},
});

var t3sMatchCreator = new T3SMatchCreator();
