var g_span = document.getElementById("iconSPAN");
var g_hidden = document.getElementById("iconHIDDEN");

function setIcon(src) {
	if (g_span.childNodes.length) {
		g_span.removeChild(g_span.childNodes[0])
	}
	var img = g_span.appendChild(document.createElement("img"));
	img.setAttribute("src",src);
	
	g_hidden.setAttribute("value",src);
}