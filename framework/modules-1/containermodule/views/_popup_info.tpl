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

<html>
    <head>
        <title>{'Module Information'|gettext}</title>
        <link rel="stylesheet" href="{$smarty.const.THEME_RELATIVE}style.css" />
    </head>
    <body>
        {br}{br}
        <div align="center" style="font-weight: bold">{if $name == ''}{'Unknown Module'|gettext}{else}{$name}{/if}</div>
        {br}
        <div style="border-top: 3px dashed lightgrey; padding: 3px;">
            <table cellpadding="0" cellspacing="0" border="0">
                {if $is_orphan}
                    <tr>
                        <td>{'Archived Module'|gettext}</td>
                    </tr>
                {else}
                    <tr>
                        <td>{'View'|gettext}:&#160;</td>
                        <td>{$container->view}</td>
                    </tr>
                    <tr>
                        <td>{'Title'|gettext}:&#160;</td>
                        <td>{if $container->title == ""}<em>&lt;{'none'|gettext}&gt;</em>{else}{$container->title}{/if}</td>
                    </tr>
                {/if}
            </table>
        </div>
        <div style="border-top: 3px dashed lightgrey; padding: 3px;">{if $name == ''}<em>{'Module Not Found in the System'|gettext}</em>{elseif $info == ''}<em>{'No Description Provided'|gettext}</em>{else}{$info|nl2br}{/if}</div>
    </body>
</html>