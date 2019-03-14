{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

{css unique="install-tables" corecss="tables"}

{/css}

 <div class="form_header">
 	<h2>{'Installing and Upgrading Database Tables'|gettext}</h2>
 	<blockquote>
 		{'Exponent is currently updating existing tables in its database, and installing new tables that it needs.  Shown below is a summary of the actions that occurred.'|gettext}
 	</blockquote>
 </div>
 <table cellpadding="2" cellspacing="0" width="100%" border="0" class="exp-skin-table">
	 <thead>
		 <tr>
			<th>{'Table Name'|gettext}</th>
			<th>{'Status'|gettext}</th>
		 </tr>
	 </thead>
	 <tbody>
		  {foreach from=$status key=table item=statusnum}
			 <tr class="{cycle values='odd,even'}">
				<td>
					 {$table}
				</td>
				<td>
					 {if $statusnum == $smarty.const.DATABASE_TABLE_EXISTED}
					<div style="color: blue; font-weight: bold">
						{'Exists'|gettext}
					</div>
					 {elseif $statusnum == $smarty.const.DATABASE_TABLE_INSTALLED}
					<div style="color: green; font-weight: bold">
						{'Succeeded'|gettext}
					</div>
					 {elseif $statusnum == $smarty.const.DATABASE_TABLE_FAILED}
					<div style="color: red; font-weight: bold">
						{'Failed'|gettext}
					</div>
					 {elseif $statusnum == $smarty.const.DATABASE_TABLE_ALTERED}
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
		  {/foreach}
	  </tbody>
 </table>