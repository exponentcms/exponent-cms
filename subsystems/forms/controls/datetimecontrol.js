function exponent_forms_disable_datetime(id,frm,disable) {
	var elem = document.getElementById("__"+id);
	var status = elem.value;
	
	var ctl_elems = new Array();

	if (status[0] == "1") {
		ctl_elems.push(document.getElementById(id+"_month"));
		ctl_elems.push(document.getElementById(id+"_day"));
		ctl_elems.push(document.getElementById(id+"_year"));
	}
	if (status[1] == "1") {
		ctl_elems.push(document.getElementById(id+"_hour"));
		ctl_elems.push(document.getElementById(id+"_minute"));
		ctl_elems.push(document.getElementById(id+"_ampm"));
	}
	
	for (var i = 0; i < ctl_elems.length; i++) {
		if (disable) ctl_elems[i].setAttribute("disabled","disabled");
		else ctl_elems[i].removeAttribute("disabled");
	}
}