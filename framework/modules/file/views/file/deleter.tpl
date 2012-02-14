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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{'Delete Missing Files'|gettext}  |  Exponent CMS</title>
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/core/assets/css/msgq.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/core/assets/css/button.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/core/assets/css/tables.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/core/assets/css/common.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/core/assets/css/admin-global.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.URL_FULL}framework/modules/file/assets/css/filemanager.css" />

    <script type="text/javascript" src="{$smarty.const.URL_FULL}exponent.js.php"></script>
    <script type="text/javascript" src="{$smarty.const.YUI3_PATH}yui/yui-min.js"></script>
</head>
<body class="exp-skin">
<div id="exp-deleter">
    <h1>{"Delete Missing Files"|gettext}</h1>
    <div id="actionbar">
        <a id="backlink" class="back awesome small green" href="{link action=picker ajax_action=1 ck=$smarty.get.ck update=$smarty.get.update fck=$smarty.get.fck}{if $smarty.const.SEF_URLS}?{else}&{/if}CKEditor={$smarty.get.CKEditor}&CKEditorFuncNum={$smarty.get.CKEditorFuncNum}&langCode={$smarty.get.langCode}"><span>{'Back to Manager'|gettext}</span></a>
    </div>
	<div class="info-header clearfix">
		<div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Deleting Files"|gettext) module="delete-files"}
		</div>
        <p>{"Select the following non-existent files to remove them from the File Manager."|gettext}</p>
	</div>
    {messagequeue}

    <div id="filelist">
    {form action=deleteit}
      <table id="filenames" class="exp-skin-table">
        <thead>
    	   <tr>
                <th><a href="#" onclick="files_selectUnselectAll(true); return false;">{'All'|gettext}</a>&nbsp;|&nbsp;<a href="#" onclick="files_selectUnselectAll(false); return false;">{'None'|gettext}</a></th>
                <th>{'Filename'|gettext}</th>
                <th>{'Folder'|gettext}</th>
           </tr>
    	</thead>
    	<tbody>
{foreach from=$files item=file key=src}
        <tr class="{cycle values="even,odd"}">
            <td width="20">
                {control type="checkbox" name="deleteit[]" value=$src}
            </td>
            <td>
                {$file->filename}
            </td>
            <td>
                {$file->directory}
            </td>
        </tr>
{foreachelse}
        <tr><td colspan=3>{'There don\'t appear to be any missing files'|gettext}</td></tr>
{/foreach}
        </tbody>
     </table>
    {control type=buttongroup submit="Delete Selected Files"|gettext}
    {/form}
    </div>
</div>
<script type="text/javascript">
function files_selectUnselectAll(setChecked) {
	var elems = document.getElementsByTagName("input");
	for (var key = 0; key < elems.length; key++) {
		if (elems[key].type == "checkbox" && elems[key].name.substr(0,9) == "deleteit[") {
			elems[key].checked = setChecked;
		}
	}
}
</script>
</body>
</html>
