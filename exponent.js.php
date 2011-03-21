<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Copyright 2006 Maxim Mueller
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

//Initialize exponent Framework
include_once("exponent.php");
?>
// exponent Javascript Support Systems

EXPONENT = {};

// for back-compat
eXp = EXPONENT;

// map certian php CONSTANTS to JS vars

EXPONENT.LANG = "<?php echo LANG; ?>";
EXPONENT.PATH_RELATIVE = "<?php echo PATH_RELATIVE; ?>";
EXPONENT.URL_FULL = "<?php echo URL_FULL; ?>";
EXPONENT.BASE = "<?php echo BASE; ?>";
EXPONENT.THEME_RELATIVE = "<?php echo THEME_RELATIVE; ?>";
EXPONENT.ICON_RELATIVE = "<?php echo ICON_RELATIVE; ?>";
EXPONENT.JS_FULL = '<?php echo JS_FULL; ?>';
EXPONENT.YUI2_PATH = '<?php echo YUI2_PATH; ?>';
EXPONENT.YUI3_PATH = '<?php echo YUI3_PATH; ?>';
EXPONENT.YUI2_URL = '<?php echo YUI2_URL; ?>';
EXPONENT.YUI3_URL = '<?php echo YUI3_URL; ?>';

<?php 
if (MINIFY==1) { 
?>
EXPONENT.YUI3_CONFIG = {
    combine:1,
    comboBase:EXPONENT.PATH_RELATIVE+'external/minify/min/?b='+EXPONENT.PATH_RELATIVE.substr(1)+'external/lissa&f=',
    filter: {
        'searchExp': "&3", 
        'replaceStr': ",3"
    }
};
<?php 
} else {
?>
EXPONENT.YUI3_CONFIG = {};
<?php 
}
?>




// YUI 2 ajax helper method. This is much easier in YUI 3. Should also migrate.

EXPONENT.AjaxEvent = function() {
    var obj;
    var data = "";
    
    var gatherURLInfo = function (obj){
        json = obj.json ? "&json=1" : "";
        if (obj.form){
            data = YAHOO.util.Connect.setForm(obj.form);
            //slap a date in there so IE doesn't cache
            var dt = new Date().valueOf();
            var sUri = EXPONENT.URL_FULL + "index.php?ajax_action=1" + json + "&yaetime=" + dt;
            return sUri;
        } else if (!obj.action || (!obj.controller && !obj.module)) {
            alert("If you don't pass the ID of a form, you need to specify both a module/controller AND and a cresponding action.");
        } else {
            //slap a date in there so IE doesn't cache
            var dt = new Date().valueOf();
            var modcontrol = (obj.controller) ? "&controller="+obj.controller : "&module="+obj.module;
            var sUri = EXPONENT.URL_FULL + "index.php?ajax_action=1" + modcontrol + "&action=" + obj.action + json + "&yaetime=" + dt + obj.params;
            return sUri;
        }
    }

    return {
        json:0,
        subscribe: function(fn, oScope) {
            if (!this.oEvent) {
                this.oEvent = new YAHOO.util.CustomEvent("ajaxevent", this, false, YAHOO.util.CustomEvent.FLAT);
            }
            if (oScope) {
                this.oEvent.subscribe(fn, oScope, true);
            } else {
                this.oEvent.subscribe(fn);
            }
        },
        fetch: function(obj) {
            if (typeof obj == "undefined" || !obj){
                alert('EXPONENT.ajax requires a single object parameter.');
                return false;
            } else {
                if (typeof(obj.json)!=="undefined"){
                    this.json = obj.json;
                } else {
                    this.json = false;
                }
                var sUri = gatherURLInfo(obj);
                //console.debug(sUri);
                YAHOO.util.Connect.asyncRequest("POST", sUri, {
                success: function (o) {
                    //if we're just sending a request and not needing to do 
                    //anything on the completion, we can skip firing the custtom event
                    if (typeof(this.oEvent)!=="undefined") {
                       
                        //otherwise, we check if we've got SJON coming back to parse
                        if (this.json!==false) {
                            //if so, parse it
                            var oParse = YAHOO.lang.JSON.parse(o.responseText);
                        } else {
                            //if not, it's probably HTML we're going to update a view with
                            var oParse = o.responseText;
                        }
                        //fire off the custome event to do some more stuff with
                        this.oEvent.fire(oParse);
                    }
                },
                    scope: this
                },obj.data);
            }
        }
    }
}

// Form helper functions. We should migrate this.

EXPONENT.forms = {
        
    getSelectedRadio: function (formId, inputId){
        var oForm = this.grabForm(formId);
        for (var i=0; i<oForm.elements.length; i++){
            oElement = oForm.elements[i];
            oValue = oElement.value;

            switch(oElement.type)
            {
                case 'radio':
                    if(oElement.checked && oElement.name==inputId){
                        return oValue;
                    }
                    break;
            }
        }
        return "no selected radios found";
    },
    setSelectedRadio: function (formId, inputId, rValue){
        var oForm = this.grabForm(formId);
        for (var i=0; i<oForm.elements.length; i++){
            oElement = oForm.elements[i];
            oValue = oElement.value;

            switch(oElement.type)
            {
                case 'radio':
                    if(oElement.name==inputId && oElement.value==rValue){
                        oElement.checked = true;
                        return "Radio "+oElement.name+" set to value "+oElement.value;
                    }
                    break;
            }
        }
        return "No value matching the one provided was found in this radio group";
    },
    getSelectValue: function (selectid) {
        var selectmenu = YAHOO.util.Dom.get(selectid);
        return selectmenu.options[selectmenu.selectedIndex].value;
    },
    setSelectValue: function (selectid,setVal) {
        var selectmenu = YAHOO.util.Dom.get(selectid);
        return selectmenu.value = setVal;
    },
    addSelectOption: function (selectid,oVal,text) {
        var selectmenu = YAHOO.util.Dom.get(selectid);
        selectmenu.options[selectmenu.length] = new Option(text, oVal);
        return oVal;
    },
    grabForm: function (formId){
        var oForm;
        if(typeof formId == 'string'){
            // Determine if the argument is a form id or a form name.
            // Note form name usage is deprecated, but supported
            // here for backward compatibility.
            oForm = (document.getElementById(formId) || document.forms[formId]);
        }
        else if(typeof formId == 'object'){
            // Treat argument as an HTML form object.
            oForm = formId;
        }
        else{
            return;
        }
        return oForm;
    }
};


