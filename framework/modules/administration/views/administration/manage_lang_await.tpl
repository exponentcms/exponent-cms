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

{css unique="manage-lang-await" corecss="tables"}

{/css}

 <div class="form_header">
 	<h1>{$await|count} {'Phrases Still Awaiting Translation in'|gettext} {$smarty.const.LANG}</h1>
 </div>
 <table cellpadding="2" cellspacing="0" width="100%" border="0" class="exp-skin-table">
	 <thead>
		 <tr>
			<th>{'Phrase'|gettext}</th>
		 </tr>
	 </thead>
	 <tbody>
		  {foreach from=$await key=table item=phrase}
			 <tr class="{cycle values='odd,even'}">
				<td>
					 {$phrase}
				</td>
			 </tr>
		  {/foreach}
	  <tbody>
 </table>