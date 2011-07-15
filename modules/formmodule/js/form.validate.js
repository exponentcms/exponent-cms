function validate(frm) {
	//alert ("Name array length: " + reqFieldNameArray.length);
	var e;
	var defaultMsg = "You must specify a value for the following field:";
	for (var n = 0; n < reqFieldTypeArray.length; n++){
		e = document.getElementById(reqFieldNameArray[n]);
		switch (reqFieldTypeArray[n]){			
			case 'checkboxcontrol':
				//if this is a required field, assuming it MUST be checked (ie - license agreement)
				if (!e.checked) return showMessage(reqFieldTitleArray[n] + " is a required field.  You must select this field before submitting this form.", e); 					
				break;
			case 'radiogroupcontrol':
				//If this is required, we're just looking for some sort of value selected.
				if (e == '') return showMessage("You must choose one of the options for the following field " + reqFieldTitleArray[n], e); 					
				break;
			case 'textcontrol':
			case 'texteditorcontrol':
			case 'uploadcontrol':
			default:
				//alert(e.value.length);
				if (e.value.length == 0) return showMessage(defaultMsg + reqFieldTitleArray[n], e); 					
				break;		
		}
		//reqFieldNameArray[n];		
		//reqFieldTitleArray[n];
	}
	alert ('all good');
	return confirm('Are you sure you want to submit your form?');
}

function showMessage(msg, field){
	alert (msg);//
	//need to do an eval to get object 	
	field.focus();
	return false;
}

//var reqFieldNameArray = new Arrary('field1', 'field2');
//var reqFieldTitleArray = new Arrary('Field 1', 'Field 2');
//var reqFieldTypeArray = new Arrary('txt', 'cb')