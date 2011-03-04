{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

 <h2>{$title}</h2><br />
 <table border="1" cellspacing="0" cellpadding="4">
 {foreach from=$fields key=fieldname item=value}
	<tr>
		<td>{$captions[$fieldname]}</td>
		<td>{$value}</td>
	</tr>
 {/foreach}
 </table>
 {if $is_email == 0}
  {br}
	<a href="{$backlink}">{$_TR.back}</a>
 {/if}