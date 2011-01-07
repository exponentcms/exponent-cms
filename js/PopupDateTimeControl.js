function popupdatetimecontrol_enable(frm,name) {
	var trigger = document.getElementById(name+"_trigger");
	var span = document.getElementById(name+"_span");
	
	if (frm[name+"_disabled"].checked == true) {
		if (trigger.style) {
			trigger.style.visibility = "hidden";
		} else {
			trigger.setAttribute("style","visibility: hidden;");
		}
		
		if (span.style) {
			//span.class = "datefield_disabled";
		} else {
			span.setAttribute("class","datefield_disabled");
		}
		span.removeChild(span.firstChild);
		span.appendChild(document.createTextNode("<No Date Selected>"));
	} else {
		if (trigger.style) {
			trigger.style.visibility = "visible";
			trigger.style.cursor = "pointer";
		} else {
			trigger.setAttribute("style","visibility: visible; cursor: pointer;");
		}
		
		if (span.style) {
			//span.class = "datefield";
		} else {
			span.setAttribute("class","datefield");
		}
	}
}