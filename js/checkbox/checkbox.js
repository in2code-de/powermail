// Javascript for updating checkboxes

function checkbox(id) {
	var checkid = 'check_'+id;
	var valueid = "value_"+id;
	var checked = document.getElementsByName(checkid)[0].checked;
	var cvalue = document.getElementsByName(valueid)[0].value;

	if(checked == false) {
		document.getElementById(id).value = '';
	}
	else {
		document.getElementById(id).value = cvalue;
	}
}
