{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("File Display Settings"|gettext) module="files"}
		</div>
        <h2>{"File Display Settings"|gettext}</h2>
	</div>
</div>
{control id="filedisplay" type='filedisplay-types' name=filedisplay label="Display Files as"|gettext value=$config.filedisplay}
<div id="ff-options" style="display:none">
    {control type="dropdown" name="ffloat" label="File Display Box Float"|gettext items="No Float,Left,Right"|gettxtlist values="No Float,Left,Right" value=$config.ffloat}
    {control type="text" label="Width of File Display Box"|gettext name="fwidth" value=$config.fwidth size=5}
    {control type="text" label="Width of Margin"|gettext name="fmargin" value=$config.fmargin size=5}
    <hr />
</div>
<div id="fileViewConfig">
    {if $config.filedisplay != ""}
        {$presaved=1}
        {$themefileview="`$smarty.const.BASE`themes/`$smarty.const.DISPLAY_THEME`/modules/common/views/file/configure/`$config.filedisplay`.tpl"}
        {if file_exists($themefileview)}
            {include file=$themefileview}
        {else}
            {include file="`$smarty.const.BASE`framework/modules/common/views/file/configure/`$config.filedisplay`.tpl"}
        {/if}
    {else}
        <p></p>
    {/if}
</div>

{script unique="fileviewconfig" yui3mods="1"}
{literal}

YUI(EXPONENT.YUI3_CONFIG).use('node','io', function(Y) {
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load File Config'},
    			arguments : { 'X-Transaction': 'Load File Config'}
    		};
    		
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=file&action=get_view_config&ajax_action=1";

	var handleSuccess = function(ioId, o){
		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "example");
        
        if(o.responseText){
            Y.one('#fileViewConfig').setContent(o.responseText);
            Y.one('#ff-options').setStyle("display","block");
        } else {
            Y.one('#fileViewConfig .loadingdiv').remove();
            Y.one('#ff-options').setStyle("display","none");
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "example");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    Y.one('#filedisplay').on('change',function(e){
        cfg.data = "view="+e.target.get('value');
        var request = Y.io(sUrl, cfg);
        Y.one('#fileViewConfig').setContent(Y.Node.create('<div class="loadingdiv" style="width:40%">{/literal}{"Loading Form"|gettext}{literal}</div>'));
        if (e.target.get('value')==""){
            Y.one('#ff-options').setStyle("display","none");
        }
    });
    {/literal}
    {if $presaved}
        Y.one('#ff-options').setStyle("display","block");
    {/if}
    {literal}
});
{/literal}
{/script}
