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
{assign var="pgname" value="userperms"}
{if $user_form == 1}{assign var="pgname" value="groupperms"}{/if}
{paginate objects=$users paginateName=$pgname modulePrefix="administration" rowsPerPage=20}

{if $user_form == 1}paginate.noRecords = "{$_TR.no_users}";
{else}paginate.noRecords = "{$_TR.no_groups}";
{/if}

{literal}

function box_checked(ID,perm,cbox) {
	paginate.allData[ID]['var_perms_'+perm] = (cbox.checked ? 1 : 0);
}

function serializeData() {
	elem = document.getElementById("permdata");
	arr = new Array();
	for (i = 0; i < paginate.allData.length; i++) {
		uid = paginate.allData[i].var_id;
		parr = new Array();
		{/literal}{foreach from=$perms item=name key=perm}{literal}
		if (paginate.allData[i]['var_perms_{/literal}{$perm}{literal}'] == 1) {
			parr.push('{/literal}{$perm}{literal}');
		}
		{/literal}{/foreach}{literal}
		str = uid;
		if (parr.length > 0) str = uid+":"+parr.join(":");
		arr.push(str);
	}
	//elem.setAttribute("value",arr.join(";"));
	elem.value = arr.join(";");
}

{/literal}
{* Time to generate the perm checkboxes *}
{foreach from=$perms key=perm item=name}
	function perms_{$perm}(object){literal} {{/literal}
		var html = '<div align="center"><input type="checkbox" name="p['+object['id']+'][{$perm}]" ';
		if (object['var_perms_{$perm}'] > 0) html += 'checked ';
		if (object['var_perms_{$perm}'] == 2) html += 'disabled ';
		else html += 'onclick="box_checked('+object['__ID']+',\'{$perm}\',this);" ';
		html += '/></div>';
		return html;
	{literal}}{/literal}
{/foreach}

{if $user_form == 1}{literal}
function realName(object) {
	return object.var_firstname + ' ' + object.var_lastname + ' (' + object.var_username + ')';
}
function sortRealname(a,b) {
	return (a.var_firstname.toLowerCase() + ", " + a.var_lastname.toLowerCase() > b.var_firstname.toLowerCase() + ", " + b.var_lastname.toLowerCase() ? -1 : 1);
}
{/literal}{/if}

paginate.columns = new Array(
	{if $user_form == 1}new cColumn("{$_TR.user}","",realName,sortRealname),
	{else}new cColumn("{$_TR.group}","name",null,null),
	{/if}
{foreach from=$perms key=perm item=name name=p}
	new cColumn("{$name}","",perms_{$perm},null){if $smarty.foreach.p.last == false},{/if}
{/foreach}
);
{/paginate}

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tbody id="dataTable">
	
	</tbody>
</table>
<script language="JavaScript">
	document.write(paginate.drawPageTextPicker(3));
	paginate.drawTable();
</script>
<br />
<form method="post">
<input type="hidden" name="module" value="{$__loc->mod}" />
<input type="hidden" name="src" value="{$__loc->src}" />
<input type="hidden" name="int" value="{$__loc->int}" />
{if $user_form == 1}<input type="hidden" name="action" value="saveuserperms" />
{else}<input type="hidden" name="action" value="savegroupperms" />
{/if}
<input type="hidden" name="_common" value="1" />
<input type="hidden" id="permdata" name="permdata" value="" />
<input type="submit" value="{$_TR.save}" onclick="serializeData(); return true;"{if $have_users == 0} disabled{/if} />
<input type="button" value="{$_TR.cancel}" onclick="document.location.href = '{$smarty.server.HTTP_REFERER}';" />
</form>