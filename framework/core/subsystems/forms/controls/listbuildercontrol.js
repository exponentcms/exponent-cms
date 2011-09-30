var newList = new Array();

function addSelectedItem(name, process) {
	var key;
	var srccontrol = document.getElementById("source_"+name);
	
	//if (!newList[name]) {
	if (srccontrol.type == 'select-one') {
		if (process == 'copy') {
			key = moveItem(name,"source_","dest_", "copyonly");
		} else {
			key = moveItem(name,"source_","dest_");
		}
	} else {
		var sText = document.getElementById("source_" + name).value;
		var ptChoices = document.getElementById("dest_" + name);
		if (sText != "") {
			sText = sText.replace(/^\s+|\s+$/g, '');
			var newopt = document.createElement("OPTION")
			newopt.text = sText;
			newopt.value = sText;
			ptChoices.options.add(newopt);
		}
		key = sText;
	}
	var dataElem = document.getElementById(name);
	var arr = new Array();
	if (dataElem.value != "") arr = dataElem.value.split("|!|");
	arr.push(key);
	dataElem.value = arr.join("|!|");
}

function removeSelectedItem(name, process) {
	var key;
	var ptChoices = document.getElementById("dest_" + name);
	if (ptChoices.selectedIndex >= 0) {
		var srccontrol = document.getElementById("source_"+name);
		if (srccontrol.type == 'select-one') {		
		//if (!newList[name]) {
			if (process == 'copy') {
				key = moveItem(name,"dest_","source_", "copyonly");
			} else {
				key = moveItem(name,"dest_","source_");
			}
		} else {
			key = ptChoices.options[ptChoices.selectedIndex].text;
			ptChoices.remove(ptChoices.selectedIndex);
		}

		var dataElem = document.getElementById(name);
		var arr = dataElem.value.split("|!|");
		for (i = 0; i < arr.length; i++) {
			if (arr[i] == key) {
				arr.splice(i,1);
				break;
			}
		}
		dataElem.value = arr.join("|!|");
	}
}

function moveItem(name,from,to, process) {
	var g_src = document.getElementById(from+name);
	var g_dst = document.getElementById(to+name);
	var i=0;

	
	if (g_src.selectedIndex < 0) return;
	
	var key = g_src.options[g_src.selectedIndex].value;
	var value = g_src.options[g_src.selectedIndex].text;
	
	if(process == "copyonly" && from == "dest_") {
		g_src.options[g_src.selectedIndex] = null;
		return key;
	}
	
	for (i = 0;i < g_dst.length; i++) {
		// alert(g_dst.options[i].value);
		// alert(value);
		if (g_dst.options[i].value == key) {
			return;
		}
	}
	
	g_dst.options[g_dst.options.length] = new Option(value,key,false,true);
	
	if (process != "copyonly") {
		g_src.options[g_src.selectedIndex] = null;
	}
	return key;
}
