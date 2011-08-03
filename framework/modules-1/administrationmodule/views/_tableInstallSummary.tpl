{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{css unique="installtablesummary" corecss="tables"}

{/css}

 <div class="form_header">
 	<h1>{$_TR.form_title}</h1>
 	<p>
 		{$_TR.form_header}
 	</p>
 </div>
 <table cellpadding="2" cellspacing="0" width="100%" border="0" class="exp-skin-table">
 <thead>
 <tr>
 	<th>
 		{$_TR.table_name}
 	</th>
 	<th>
 		{$_TR.status}
 	</th>
 </tr>
 </thead>
 <tbody>
  {foreach from=$status key=table item=statusnum}
 <tr class="{cycle values='odd,even'}">
 	<td>
 		 {$table}
 	</td>
 	<td>
 		 {if $statusnum == $smarty.const.TMP_TABLE_EXISTED}
 		<div style="color: blue; font-weight: bold">
 			{$_TR.table_exists}
 		</div>
 		 {elseif $statusnum == $smarty.const.TMP_TABLE_INSTALLED}
 		<div style="color: green; font-weight: bold">
 			{$_TR.succeeded}
 		</div>
 		 {elseif $statusnum == $smarty.const.TMP_TABLE_FAILED}
 		<div style="color: red; font-weight: bold">
 			{$_TR.failed}
 		</div>
 		 {elseif $statusnum == $smarty.const.TMP_TABLE_ALTERED}
 		<div style="color: green; font-weight: bold">
 			{$_TR.altered_existing}
 		</div>
 		 {elseif $statusnum == $smarty.const.TABLE_ALTER_FAILED}
 		<div style="color: red; font-weight: bold">
 			{$_TR.alter_failed}
 		</div>
 		 {/if}
 	</td>
 </tr>
  {/foreach}
  <tbody>
 </table>