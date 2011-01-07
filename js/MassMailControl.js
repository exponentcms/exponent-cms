function activateMassMailControl(toActivate,name) {
	var all = new Array(
//		document.getElementById(name+"_users"),
		null,
		document.getElementById(name+"_email")
	);
	
	var radios = new Array(
		document.getElementById("r_"+name+"_users"),
		document.getElementById("r_"+name+"_email")
	);
	
	for (i = 0; i < radios.length; i++) {
		if (i != toActivate) { // deactivate
			if (i == 2) { // special case for stuff
				
			} else {
				if (all[i]) all[i].setAttribute("disabled","disabled");
				radios[i].removeAttribute("checked");
			}
		} else {
			if (all[i]) all[i].removeAttribute("disabled");
			radios[i].setAttribute("checked","checked");
		}
	}
}