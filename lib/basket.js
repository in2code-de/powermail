// JavaScript Document
		function getRequestObject(){
			var ro;
			// Mozilla kompatibel???
			if(window.XMLHttpRequest){
				try {
					ro = new XMLHttpRequest();
				}
				catch(e) {
					ro = null;
				}
			}
			// Und nun für den IE
			else {
				try {
					ro = new ActiveXObject("Msxml2.Xmlhttp");
				}
				catch(e) {
					try {
						ro = new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch(e){
						ro = null;
					}
				}
			}
			
			if(ro == null){
				alert ('Ihr Browser unterstützt leider kein AJAX');
			}
			return ro;
		}

		
		
		function init(field,id,url,params){
			get_options(field,id,url,params);
		}
		
		function get_options(field,id,url,params) {
			var doit = 1;
			var value = document.getElementById(id).value;

			receiver = getRequestObject();
			receiver.onreadystatechange = statehandler;
			receiver.open('post', url, true);
			receiver.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			receiver.send(params+'&'+field+'='+value);
		}
		
		function statehandler() {
			var select_letzte_id = 0;
			if(receiver.readyState == 4) {
			}
		}
		
		
		request_object = getRequestObject();

function checkForm(form,fields) {
	
}
