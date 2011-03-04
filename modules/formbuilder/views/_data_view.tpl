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
{paginate objects=$items paginateName="dataView" modulePrefix="data" rowsPerPage=20}

function links(object) {literal}{{/literal}
	out = '<a href="{link action=view_record module=formbuilder form_id=$f->id}{if $smarty.const.SEF_URLS == 1}{else}&{/if}id{if $smarty.const.SEF_URLS == 1}/{else}={/if}' + object.var_id + '"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}view.png" title="{$_TR.alt_view}" alt="{$_TR.alt_view}" /></a>'; 
	out += '{if $permissions.editdata == 1}<a href="{link action=edit_record module=formbuilder form_id=$f->id}{if $smarty.const.SEF_URLS == 1}{else}&{/if}id{if $smarty.const.SEF_URLS == 1}/{else}={/if}' + object.var_id + '"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>{/if}'; 
	out += '{if $permissions.deletedata == 1}<a href="{link action=delete_record module=formbuilder form_id=$f->id}{if $smarty.const.SEF_URLS == 1}{else}&{/if}id{if $smarty.const.SEF_URLS == 1}/{else}={/if}' + object.var_id + '" onclick="return confirm(\'{$_TR.delete_confirm}\');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>{/if}'; 
	
	return out;
{literal}}{/literal}

{$sortfuncs}

{$columdef}

{/paginate}
<table cellspacing="0" cellpadding="2" style="border:none;" width="100%">
<tr><h2>{$title}</h2></tr>
	<tbody id="dataTable">
	</tbody>
</table>
<br />
<table width="100%">
<tr><td align="left" valign="bottom">
<script language="JavaScript">document.write(paginate.drawPageStats(""));</script>
</td><td align="right" valign="bottom">
<script language="Javascript">document.write(paginate.drawPageTextPicker(3));</script>
</td></tr>
</table>
<script language="JavaScript">
	paginate.drawTable();
</script>
<a href="{$backlink}">{$_TR.back}</a>
