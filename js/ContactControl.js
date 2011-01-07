function activateContactControl(toActivate,name) {
	var all = new Array(
		document.getElementById(name+"_users"),
		document.getElementById(name+"_email")
	);
	
	var radios = new Array(
		document.getElementById("r_"+name+"_users"),
		document.getElementById("r_"+name+"_email")
	);
	
	for (i = 0; i < all.length; i++) {
		if (i != toActivate) { // deactivate
			if (i == 2) { // special case for stuff
				
			} else {
				all[i].setAttribute("disabled","disabled");
				radios[i].removeAttribute("checked");
			}
		} else {
			all[i].removeAttribute("disabled");
			radios[i].setAttribute("checked","checked");
		}
	}
}