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

{css unique="form-records" corecss="tables"}

{/css}

<div class="module forms confirm-data">
    <h1>{'Please confirm your submission'|gettext}</h1>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>{'Field'|gettext}</th>
                <th>{'Your Response'|gettext}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $responses as $fieldname=>$response}
            <tr class="{cycle values="odd,even"}">
                <td>
                    <strong>{$captions.$fieldname}: </strong>
                </td>
                <td>
                    {if $fieldname|lower == 'image'} {* fixme do we get a complete pathname here??? *}
                        {$matches = array()}
                        {$tmp = preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $response, $matches)}
                        {$filename1 = $matches.2.0}
                        {$filename2 = str_replace(URL_BASE, '/', $filename1)}
                        {$base = str_replace(PATH_RELATIVE, '', BASE)}
                        {$fileinfo = expFile::getImageInfo($base|cat:$filename2)}
                        {if $fileinfo.is_image == 1}
                            {img src=$filename1 w=64}
                        {else}
                            {$response}
                        {/if}
                    {else}
                        {$response}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {form action=submit_data}
        {foreach $postdata as $name=>$data}
            {control type=hidden name=$name value=$data}
        {/foreach}
        {control type=antispam}
        {control type=buttongroup submit="Submit Form"|gettext cancel="Change Responses"|gettext}
    {/form}
</div>
