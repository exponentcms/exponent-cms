if (document.body.appendChild) {
    g_noSourceControl = (document.getElementById("r_existing_source") == null);
    
    if (!g_noSourceControl) {
        g_radio_existing  = document.getElementById("r_existing_source");
        g_radio_new       = document.getElementById("r_new_source");
        g_existing_link   = document.getElementById("existing_source_link"); 
        
        g_radio_all = new Array(g_radio_existing, g_radio_new);
    }
    
    g_ctl_module = document.getElementById("i_mod");
    g_ctl_views = document.getElementById("view");
    module = null;
    
    
    function activate(type) {
        // if (g_noSourceControl) return;
        // 
        // var elem = document.getElementById("noSourceMessageTD");
        // clearList(elem);
        // if (!module.var_supportsSources) {
        //     disableAll();
        //     elem.appendChild(document.createTextNode("This module does not"));
        //     elem.appendChild(document.createElement("br"));
        //     elem.appendChild(document.createTextNode("support Sources."));
        //     return;
        // }
        // switch (type) {
        //     case "Existing":
        //         disableAll();
        //         g_radio_existing.disabled = false;
        //         g_radio_existing.checked = true;
        //         g_existing_link.setAttribute("onclick","pickSource(); return false;");
        //         //g_existing_link.onclick = function() { pickSource(); return false; }
        //         
        //         g_radio_new.disabled = false;
        //         showPreviewCall();
        //         break;
        //     case "New":
        //         disableAll();
        //         g_radio_new.disabled = false;
        //         g_radio_new.checked = true;
        //         g_existing_link.setAttribute("onclick","pickSource(); return false;");
        //         //g_existing_link.onclick = function() { pickSource(); return false; }
        //         
        //         g_radio_existing.disabled = false;
        //         break;
        //     case null:
        //         g_radio_new.disabled = false;
        //         g_radio_existing.disabled = false;
        //         g_existing_link.setAttribute("onclick","pickSource(); return false;");
        //         //g_existing_link.onclick = function() { alert("This module does not support Sources"); return false; }
        //         break;
        // }
        // sourceInit = true;
        // showPreviewCall();
    }
    
    function disableAll() {
        if (g_noSourceControl) return;
        for (i in g_radio_all) {
            g_radio_all[i].disabled = true;
        }
        g_existing_link.setAttribute("onclick",'alert("This module does not support Sources"); return false;');
    }
    
    // Clears out all of the options in a select box.
    function clearList(list) {
        while (list.childNodes.length) {
            list.removeChild(list.childNodes.item(0));
        }
    }
    

    function showForm() {
        var selectedmod = document.getElementById('i_mod');
        var type = selectedmod.value.substring(selectedmod.value.length-6, selectedmod.value.length);
        var moddiv = document.getElementById('modfrm');
        var ctldiv = document.getElementById('ctlfrm');
        if (type == 'module') {
            moddiv.style.display='block';
            ctldiv.style.display='none';
            writeViews();
        } else {
            moddiv.style.display='none';
            ctldiv.style.display='block';
            writeActions();
        }
    }
    
    function writeActions() {
        var ctl = document.getElementById('i_mod');
        var actselect = document.getElementById('act');
        var uri = EXPONENT.URL_FULL+'index.php';
        YAHOO.util.Connect.asyncRequest('POST', uri, 
            {success: function(o) {
                var opts = YAHOO.lang.JSON.parse(o.responseText);
                var action;
                clearList(actselect);
                for(action in opts) {
                    el = document.createElement('option');
                    el.appendChild(document.createTextNode(opts[action]));
                    el.setAttribute('value', action);
                    actselect.appendChild(el);
                }
                writeCtlViews();
            }}, 'module=containermodule&action=getaction&ajax_action=1&mod=' + ctl.value

        );
    }

    function writeCtlViews() {
        var mod = document.getElementById('i_mod');
        var ctl = document.getElementById('act');
        var viewselect = document.getElementById('ctlview');
        if (ctl.options.length <= 0) {
            clearList(viewselect);
            return false;
        }
        var actname = ctl.options[ctl.selectedIndex].innerHTML
                var uri = EXPONENT.URL_FULL+'index.php'
                YAHOO.util.Connect.asyncRequest('POST', uri,
                        {success: function(o) {
                            clearList(viewselect);
                            ////console.debug(o.responseText);
                            var opts = YAHOO.lang.JSON.parse(o.responseText);
                            var action;
                            for(view in opts) {
                                    el = document.createElement('option');
                                    el.appendChild(document.createTextNode(opts[view]));
                                    el.setAttribute('value', view);
                                    viewselect.appendChild(el);
                            }
                        }}, 'module=containermodule&action=getactionviews&ajax_action=1&mod=' + mod.value + '&act=' + ctl.value + '&actname=' + actname

                );
    }

    function writeViews() {
        clearList(g_ctl_views);
        module = currentModuleObject();
        for (key in module.var_views) {
            view = module.var_views[key];
            el = document.createElement("option");
            var txt=document.createTextNode(view);
            
            el.appendChild(txt);
            el.setAttribute("value",view);
            if (module.var_defaultView == view) {
                el.setAttribute("selected","selected");
            }
            g_ctl_views.appendChild(el);
        }
        sourcePicked("","");
        //if (!sourceInit) activate("New");
        activate(null);
    }
    
    function currentModule() {
        if (g_ctl_module.options) {
            return g_ctl_module.options[g_ctl_module.selectedIndex].value;
        } else {
            return g_ctl_module.value;
        }
    }
    
    function currentModuleObject() {
        var mod = currentModule();
        for (key in modnames) {
            if (modnames[key] == mod) return modules[key];
        }
        return null;
    }
    
    function pickSource() {
        activate("Existing");
        var mod = currentModule();
        //var url = PATH_RELATIVE+"source_selector.php?showmodules="+mod+"&dest="+escape("modules/containermodule/picked_source.php?dummy")+"&vmod=containermodule&vview=_sourcePicker";
        //window.open(url,'sourcePicker','title=no,toolbar=no,width=640,height=480,scrollbars=yes');
        openSelector(mod,"modules/containermodule/picked_source.php?dummy","containermodule","_sourcePicker");
    }
    
    // function sourcePicked(src,desc) {
    //     sourceSelected("existing_source",true,src,desc);
    //     //showPreviewCall();
    // }
}
