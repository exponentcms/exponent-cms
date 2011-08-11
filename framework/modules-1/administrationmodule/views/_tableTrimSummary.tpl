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
<div class="form_title">{'Trim Database'|gettext}</div>
<div class="form_header">'Exponent is removing tables that are no longer used from its database.  Shown below is a summay of the actions that occured.'|gettext}</div>
<table cellpadding="2" cellspacing="0" width="100%" border="0">
{foreach from=$dropped_tables item=table}
<tr class="row {cycle values='odd,even'}_row"><td>
{$table}
</td><td>
<div style="color: red; font-weight: bold">{'Dropped'|gettext}</div>
</td></tr>
{foreachelse}
<b>{'No unused tables were found.'|gettext}</b>
{/foreach}
</table>
{if $real_dropped != 0}
<hr size="1">
{'Dropped a total of {$dropped} tables.'|gettext|sprintf:$dropped}<br />
{math assign=diff equation="x-y" x=$dropped y=$real_dropped}
{if $diff != 0}
{$diff} {if $diff == 1}{'empty table was re-created.'|gettext}{/if}{if $diff != 1}{'empty tables were re-created.'|gettext}{/if}<br />
{/if}
{/if}