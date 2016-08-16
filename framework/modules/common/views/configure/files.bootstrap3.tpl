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

{script unique="fileviewconfig" jquery=1}
{literal}
$(document).ready(function() {
    $('#filedisplay').on('change',function(e){
        e.preventDefault();
        if (e.target.value == ""){
            $('#ff-options').css("display", "none");
            $('#fileViewConfig').css("display", "none");
        } else {
            $('#ff-options').css("display", "block");
            $('#fileViewConfig').css("display", "block");
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load File Config'},
                url: EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_view_config&ajax_action=1",
                data: "view="+e.target.value,
                success: function(o, ioId){
                    if(o){
                        $('#fileViewConfig').html(o);
                        $('#fileViewConfig script').each(function (k, n) {
                            if (!$(n).attr('src')) {
                                eval($(n).html);
                            } else {
                                var url = $(n).attr('src');
                                $.getScript(url);
                            }
                        });
                        $('#fileViewConfig link').each(function (k, n) {
                            $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                        });
                    } else {
                        $('#fileViewConfig .loadingdiv').remove();
                        $('#ff-options').css("display", "none");
                    }
                }
            });
            $('#fileViewConfig').html($('{/literal}{loading}{literal}'));
        }
    });
    {/literal}
    {if $presaved}
        $('#ff-options').css("display", "block");
    {/if}
    {literal}
});
{/literal}
{/script}
