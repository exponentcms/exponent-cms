{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
		    {help text="Get Help with"|gettext|cat:" "|cat:("File Display Settings"|gettext) module="files"}
		</div>
        <h2>{"File Display Settings"|gettext}</h2>
	</div>
</div>
{control id="filedisplay" type='filedisplay-types' name=filedisplay label="Display Files as"|gettext value=$config.filedisplay}
<div id="ff-options" style="display:none">
    {group label="File Display Box"|gettext}
        {control type="dropdown" name="ffloat" label="Placement in Relation to Content"|gettext items="Above,Left,Right,Below"|gettxtlist value=$config.ffloat}
        {control type="text" label="Width of Box"|gettext name="fwidth" value=$config.fwidth size=5 description="empty = auto width, single thumbnail column"|gettext}
        {control type="text" label="Width of Margin"|gettext name="fmargin" value=$config.fmargin size=5 description="Placed between display box and content"|gettext}
    {/group}
</div>
<div id="fileViewConfig">
    {if $config.filedisplay != ""}
        {$presaved=1}
        {$themefileview="`$smarty.const.THEME_ABSOLUTE`modules/common/views/file/configure/`$config.filedisplay`.tpl"}
        {if file_exists($themefileview)}
            {include file=$themefileview}
        {else}
            {include file="`$smarty.const.BASE`framework/modules/common/views/file/configure/`$config.filedisplay`.tpl"}
        {/if}
    {else}
        <p></p>
    {/if}
</div>

{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}

{script unique="fileviewconfig" yui3mods="node,io"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
	var sUrl = EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_view_config&ajax_action=1";

	var handleSuccess = function(ioId, o){
        if(o.responseText){
            Y.one('#fileViewConfig').setContent(o.responseText);
                Y.one('#fileViewConfig').all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    Y.Get.script(url);
                };
            });
            Y.one('#fileViewConfig').all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
            });
        } else {
            Y.one('#fileViewConfig .loadingdiv').remove();
            Y.one('#ff-options').setStyle("display", "none");
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "example");
	};

    Y.one('#filedisplay').on('change',function(e){
        e.halt();
        if (e.target.get('value')==""){
            Y.one('#ff-options').setStyle("display", "none");
            Y.one('#fileViewConfig').setStyle("display", "none");
        } else {
            var cfg = {
                method: "POST",
                headers: { 'X-Transaction': 'Load File Config'},
                arguments : { 'X-Transaction': 'Load File Config'},
                data : "view="+e.target.get('value'),
                on: {
                    success: handleSuccess,
                    failure: handleFailure
                }
            };
            Y.one('#ff-options').setStyle("display", "block");
            Y.one('#fileViewConfig').setStyle("display", "block");
            var request = Y.io(sUrl, cfg);
            Y.one('#fileViewConfig').setContent(Y.Node.create('{/literal}{loading}{literal}'));
        }
    });
    {/literal}
    {if $presaved}
        Y.one('#ff-options').setStyle("display", "block");
    {/if}
    {literal}
});
{/literal}
{/script}
