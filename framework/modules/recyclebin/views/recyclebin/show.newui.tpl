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

<html>
<head>
    <title>{'Restore from Recycle Bin'|gettext}</title>
    {css unique="show" corecss="admin-global" link="`$asset_path`css/recyclebin.css"}

    {/css}
    {css unique="newui" link="`$smarty.const.PATH_RELATIVE`external/bootstrap3/css/newui.css"}

    {/css}
    {css unique="fontawesome" link="`$smarty.const.PATH_RELATIVE`external/font-awesome4/css/font-awesome.css"}

    {/css}
    <!--[if lt IE 9]>
        <script src="{$smarty.const.JQUERY_SCRIPT}"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
        <script src="{$smarty.const.JQUERY2_SCRIPT}"></script>
    <!--<![endif]-->
</head>
<body>
    <div class="recyclebin orphan-content">
        <h1 class="main">{'Recycle Bin'|gettext} - {$module|getcontrollername|capitalize} {'items'|gettext}</h1>
        {foreach from=$items item=item}
            <div class="rb-item">
                {*<a class="usecontent" href="#" onclick="window.opener.EXPONENT.useRecycled('{$item->source}');window.close();">*}
                    {*{'Restore this content'|gettext}*}
                {*</a>*}
                <div class="module-actions" style="display: inline-flex">
                    {icon action=scriptaction class=recycle color=green onclick="window.opener.EXPONENT.useRecycled('{$item->source}');window.close();" text='Restore this content'|gettext}
                    {icon class=delete action=remove mod=$item->module src=$item->source text='Remove this'|gettext|cat:' '|cat:$item->module|getcontrollername|capitalize|cat:' '|cat:'Module from Recycle Bin'|gettext onclick="return confirm('Are you sure you want to permanently delete this module and all it\'s items from the recyclebin?');window.close();"}
                </div>
                <div class="recycledcontent">
                    {$item->html}
                </div>     
            </div>
        {foreachelse}
            <div class="rb-item">
                {'There\'s nothing for this module in the Recycle Bin'|gettext}.
            </div>
        {/foreach}
    </div>
</body>
</html>
