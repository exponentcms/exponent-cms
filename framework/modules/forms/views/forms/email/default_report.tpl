{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
 * Written and Designed by James Hunt
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

{if $is_email == 1}
    <style type="text/css">
        {$css}
    </style>
{else}
    {css unique="default-report" corecss="tables,button"}

    {/css}
{/if}

<div class="module forms show report">
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
        <tr>
            <th colspan="2">
                <h2>{$title}</h2>
            </th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$fields key=fieldname item=value}
            <tr class="{cycle values="even,odd"}">
                <td>
                    {$captions.$fieldname}
                </td>
                <td>
                    {if $fieldname|lower == 'email' && stripos($value, '<a ') === false}
                        <a href="mailto:{$value}">{$value}</a>
                    {elseif $fieldname|lower == 'image'}
                        {$matches = array()}
                        {$tmp = preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $value, $matches)}
                        {$filename1 = $matches.2.0}
                        {$filename2 = str_replace(URL_BASE, '/', $filename1)}
                        {$base = str_replace(PATH_RELATIVE, '', BASE)}
                        {$fileinfo = expFile::getImageInfo($base|cat:$filename2)}
                        {if $fileinfo.is_image == 1}
                            {img src=$filename1 w=64 fulllink=1}
                        {else}
                            {$value}
                        {/if}
                    {else}
                        {$value}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {if !empty($referrer)}
        <p>{'Referrer'|gettext}: {$referrer}</p>
    {/if}
    {if !$is_email}
        {*<a class="{button_style}" href="{$backlink}">{'Back'|gettext}</a>*}
        {icon button=true link=$backlink text='Back'|gettext}
    {/if}
</div>