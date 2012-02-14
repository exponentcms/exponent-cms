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

{css unique="install-buttons" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

{css unique="install" corecss="tables"}

{/css}
<div class="exporter extension-finalsummary">
	<h1>{'New Extension Installation Summary'|gettext}</h1>
	{if $nofiles == 1}
		<h3>{'No files to copy.  If you hit refresh, this is normal.'|gettext}</h3>
	{else}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="exp-skin-table">
			<thead>
				<tr>
					<th class="header administration_header">{'File'|gettext}</th>
					<th class="header administration_header">{'Status'|gettext}</th>
				</tr>
			</thead>
			{foreach from=$success item=status key=file}
				<tr class="{cycle values="odd,even"}">
					<td>{$file}</td>
					<td>
						{if $status == 1}
							<span style="color: green">{'Copied'|gettext}</span>
						{else}
							<span style="color: red">{'Failed'|gettext}</span>
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
        <div class="form_header">
            <h2>{'Database Tables Upgraded'|gettext}</h2>
        </div>
        <table cellpadding="2" cellspacing="0" width="100%" border="0" class="exp-skin-table">
         <thead>
             <tr>
                <th>{'Table Name'|gettext}</th>
                <th>{'Status'|gettext}</th>
             </tr>
         </thead>
         <tbody>
              {foreach from=$tables key=table item=statusnum}
                  {if $statusnum != $smarty.const.TMP_TABLE_EXISTED}
                     <tr class="{cycle values='odd,even'}">
                        <td>
                             {$table}
                        </td>
                        <td>
                             {if $statusnum == $smarty.const.TMP_TABLE_INSTALLED}
                            <div style="color: green; font-weight: bold">
                                {'Succeeded'|gettext}
                            </div>
                             {elseif $statusnum == $smarty.const.TMP_TABLE_FAILED}
                            <div style="color: red; font-weight: bold">
                                {'Failed'|gettext}
                            </div>
                             {elseif $statusnum == $smarty.const.TMP_TABLE_ALTERED}
                            <div style="color: green; font-weight: bold">
                                {'Altered Existing'|gettext}
                            </div>
                             {elseif $statusnum == $smarty.const.TABLE_ALTER_FAILED}
                            <div style="color: red; font-weight: bold">
                                {'Altering Failed'|gettext}
                            </div>
                             {/if}
                        </td>
                     </tr>
                  {/if}
              {/foreach}
          </tbody>
        </table>
		<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{$redirect}">{'Back'|gettext}</a>
	{/if}
</div>