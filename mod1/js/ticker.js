/***************************************************************
*  Copyright notice
*
*  (c) 2009 René Nitzsche <rene@system25.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * class to handle the shortcut menu
 *
 * $Id$
 */
var T3SInstantMessager = Class.create({

	/**
	 * registers for resize event listener and executes on DOM ready
	 */
	initialize: function() {
		Event.observe(window, 'load', function(){
			this.initControls();
		}.bindAsEventListener(this));
	},

	/**
	 * initializes the controls to follow, edit, and delete shortcuts
	 *
	 */
	initControls: function() {

			// map InPlaceEditor to edit icons
			new Ajax.InPlaceEditor('instant', '../../../../typo3/ajax.php?ajaxID=T3sports::saveTickerMessage', {
				highlightcolor      : '#f9f9f9',
				highlightendcolor   : '#f9f9f9',
				onFormCustomization : this.addFields,
				callback            : this.onSend,
				textBetweenControls : ' ',
				size: '60',
				cancelControl       : 'button',
				clickToEditText     : '',
				htmlResponse        : true
			});
	},

	onSend: function(form, nameInputFieldValue) {
		// Die aktuelle Spielzeit ermitteln
		matchid = $('editform').t3matchid.value;
		minute = $('editform').watch_minute.value;
		form.t3time.value = minute;
		form.t3match.value = matchid;
		var params = form.serialize();
		return params;
	},

	/**
	 * adds a hidden field for Minute and match uid
	 */
	addFields: function(inPlaceEditor, inPlaceEditorForm) {
		inPlaceEditor._controls.editor.value = '';
		field = createHiddenField('t3time');
		inPlaceEditor._form.appendChild(field);
		inPlaceEditor._form.appendChild(createHiddenField('t3match'));
//		inPlaceEditor._form.appendChild(document.createElement('br'));
	},
});

function createHiddenField (name) {
	var field = $(document.createElement('input'));
	field.name = name;
	field.id = name;
	field.type = 'hidden';
	return field;
}

var T3sportsTicker = new T3SInstantMessager();

