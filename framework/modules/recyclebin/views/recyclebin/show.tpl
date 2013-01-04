{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
</head>
<body>
    <div class="recyclebin orphan-content">
        <h1 class="main">{'Recycle Bin'|gettext} - {$module|getcontrollername|capitalize} {'items'|gettext}</h1>
        {foreach from=$items item=item}
            <div class="rb-item">
                <a class="usecontent" href="#" onclick="window.opener.EXPONENT.useRecycled('{$item->source}');window.close();">
                    {'Restore this content'|gettext}
                </a>
                {icon action=delete id=$item->id mod=$module src=$item->source onclick="return confirm('Are you sure you want to delete this recyclebin item?');window.close();"}
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
