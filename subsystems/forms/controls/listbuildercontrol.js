var newList = new Array();

function addSelectedItem(name) {
	var key;
	var srccontrol = document.getElementById("source_"+name);
	
	//if (!newList[name]) {
	if (srccontrol.type == 'select-one') {
		key = moveItem(name,"source_","dest_");
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

function removeSelectedItem(name) {
	var key;
	var ptChoices = document.getElementById("dest_" + name);
	if (ptChoices.selectedIndex >= 0) {
		var srccontrol = document.getElementById("source_"+name);
		if (srccontrol.type == 'select-one') {		
		//if (!newList[name]) {
			key = moveItem(name,"dest_","source_");
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

function moveItem(name,from,to) {
	var g_src = document.getElementById(from+name);
	var g_dst = document.getElementById(to+name);
	
	if (g_src.selectedIndex < 0) return;
	
	var key = g_src.options[g_src.selectedIndex].value;
	var value = g_src.options[g_src.selectedIndex].text;
	
	g_dst.options[g_dst.options.length] = new Option(value,key,false,true);
	
	g_src.options[g_src.selectedIndex] = null;
	
	return key;
}
