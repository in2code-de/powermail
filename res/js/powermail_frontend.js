// Function to update checkboxes
function insertCheckboxValueToHiddenField(id) {
	var checkid = 'check_' + id;
	var valueid = 'value_' + id;
	var checked = document.getElementsByName(checkid)[0].checked;
	var cvalue = document.getElementsByName(valueid)[0].value;

	if(checked == false) {
		document.getElementById(id).value = '';
	} else {
		document.getElementById(id).value = cvalue;
	}
}

// Maxlength for textareas
function checkTextArea(obj, maxLength) {
	var textArea = obj;
	var length = textArea.value.length;
	
	if (length > maxLength) {
		textArea.value = textArea.value.substr(0, maxLength);
	}
}

(function($) {
	// add tabs to fieldsets for multiple page
	$('ul.powermail_multiplejs_tabs').tabs('div.fieldsets > fieldset');
})(jQuery);