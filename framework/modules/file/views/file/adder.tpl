{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

<!DOCTYPE HTML>
<html>
<head>
    {*<meta http-equiv="content-type" content="text/html; charset=utf-8" />*}
    <meta charset="{$smarty.const.LANG_CHARSET}">
    <title>{'Add Existing Files'|gettext}  |  Exponent CMS</title>
    <meta name="Generator" content="Exponent Content Management System - v{expVersion::getVersion(true)}"/>
    {css unique="deleter" corecss="msgq,button,tables,common,admin-global" link="`$asset_path`css/filemanager.css"}

    {/css}
    <script type="text/javascript" src="{$smarty.const.YUI3_RELATIVE}yui/yui-min.js"></script>
    <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>
</head>
<body{if !bs3()} class="exp-skin"{/if}>
<div id="exp-adder">
    <h1>{"Add Existing Files"|gettext}</h1>
    <div id="actionbar">
        <a id="backlink" class="back awesome small green" href="{link action=picker ajax_action=1 update=$smarty.get.update filter=$smarty.get.filter}{if $smarty.const.SEF_URLS}?{else}&{/if}CKEditor={$smarty.get.CKEditor}&CKEditorFuncNum={$smarty.get.CKEditorFuncNum}&langCode={$smarty.get.langCode}"><span>{'Back to Manager'|gettext}</span></a>
    </div>
	<div class="info-header clearfix">
		<div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Adding Files"|gettext) module="add-files"}
		</div>
        <blockquote>{"Select the following files found on the server to add them to the File Manager."|gettext}</blockquote>
	</div>
    {messagequeue}

    <div id="filelist">
        {if $files|@count!=0}
            {form action=addit}
                {control type=hidden name=update value=$smarty.get.update}
                {*{control type=hidden name=ck value=$smarty.get.ck}*}
                <table id="ffilenames" class="exp-skin-table">
                    <thead>
                       <tr>
                           <th><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" onChange="selectAll(this.checked)"></th>
                           <th>{'Filename'|gettext}</th>
                           <th>{'Folder'|gettext}</th>
                       </tr>
                    </thead>
                    <tbody>
                        {foreach from=$files item=file key=src}
                            <tr class="{cycle values="even,odd"}">
                                <td style="width:20px;">
                                    {control type="checkbox" name="addit[]" value=$src}
                                </td>
                                <td>
                                    {$file}
                                </td>
                                <td>
                                    {$src}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                {control type=buttongroup submit="Add Selected Files"|gettext}
            {/form}
        {else}
            {'There don\'t appear to be any files on the server which aren\'t already in the File Manager'|gettext}
        {/if}
    </div>
</div>

{script unique="adder"}
{literal}
    function selectAll(val) {
        var checks = document.getElementsByName("addit[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/literal}
{/script}
</body>
</html>
