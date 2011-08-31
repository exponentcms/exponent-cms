// Namespacing


var Event = YAHOO.util.Event;
var Connection = YAHOO.util.Connect;
var Dom = YAHOO.util.Dom;

//Generic AJAX function
function doIt(url, dest, method, form) {
	if ( method == null ) {
		method = "GET";
	}
	if (form != null) {
		Connection.setForm(Dom.get(form));
	}
   //Send this puppy via asyncRequest
    Connection.asyncRequest(method, url, {
    	success : function(o){
			Dom.get(dest).style.display = 'block';
			Dom.get(dest).innerHTML = o.responseText;
	},
		failure : function(o){
        //	alert('Error handling request: '+o.responseText);
	},
		timeout : 5000
    });
	return false;
}
