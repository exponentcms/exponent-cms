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
 
{css unique="group" corecss="tables"}

{/css}

<h1>{$moduletitle|default:"Manage Group Membership"}</h1>	

{paginate objects=$users}
	{literal}

	function serializeData() {
		elem = document.getElementById("membdata");
		arr = new Array();
		for (i = 0; i < paginate.allData.length; i++) {
			if (paginate.allData[i].var_is_member == 1) {
				str = paginate.allData[i].var_id;
				if (paginate.allData[i].var_is_admin == 1) {
					str += ':1';
				} else {
					str += ':0';
				}
				arr.push(str);
			}
		}
		elem.value = arr.join(",");
	}

	function makeMember(id,checked) {
		paginate.allData[id].var_is_member = (checked ? 1 : 0);
		if (!checked && paginate.allData[id].var_is_admin == 1) {
			makeAdmin(id,false);
			paginate.drawTable();
		}
	}

	function makeAdmin(id,checked) {
		paginate.allData[id].var_is_admin = (checked ? 1 : 0);
		if (checked && paginate.allData[id].var_is_member == 0) {
			makeMember(id,true);
			paginate.drawTable();
		}
	}

	function changeAll(checked) {
		for (i = 0; i < paginate.allData.length; i++) {
			{/literal}
			{if $perm_level != 2}
			if (paginate.allData[i].var_is_admin == 1) continue;
			{/if}
			{literal}
			paginate.allData[i].var_is_member = ( checked ? 1 : 0 );
		}
		paginate.drawTable();
	}

	function isMember(object) {
		html = '<input type="checkbox" ';
		if (object.var_is_member == 1) {
			html += 'checked ';
		}
		{/literal}
		{if $perm_level != 2}
		if (object.var_is_admin == 1) html += 'disabled ';
		{/if}
		{literal}

		html += 'onclick="makeMember('+object.__ID+',this.checked)" />';
		return html;
	}

	function isAdmin(object) {
		html = '<input type="checkbox" ';
		if (object.var_is_admin == 1) {
			html += 'checked ';
		}
		{/literal}
		{if $perm_level != 2}
		html += 'disabled ';
		{/if}
		{literal}
		html += 'onclick="makeAdmin('+object.__ID+',this.checked)" />';
		return html;
	}

	function sortMember(a,b) {
		return (a.var_is_member > b.var_is_member ? -1 : 1);
	}

	function sortAdmin(a,b) {
		return (a.var_is_admin > b.var_is_admin ? -1 : 1);
	}

	paginate.columns = new Array(
		new cColumn("{/literal}{$_TR.user}{literal}","username",null,null),
		new cColumn("{/literal}{$_TR.is_member}{literal}","",isMember,sortMember),
		new cColumn("{/literal}{$_TR.group_admin}{literal}","",isAdmin,sortAdmin)
	);

	{/literal}
{/paginate}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="exp-skin-table">
	<tbody id="dataTable">

	</tbody>
</table>

<table width="100%" class="exp-skin-table">
    <tbody>
    <tr>
        <td align="left" valign="bottom">
            <script language="JavaScript">document.write(paginate.drawPageStats(""));</script>
        </td>
        <td align="right" valign="bottom">
            <script language="Javascript">document.write(paginate.drawPageTextPicker(3));</script>
        </td>
    </tr>
    </tbody>
</table>
<script language="JavaScript">
	paginate.drawTable();
</script>

<form method="post">
<input type="hidden" name="module" value="administrationmodule" />
<input type="hidden" name="action" value="gmgr_savemembers" />
<input type="hidden" name="id" value="{$group->id}"/>
<input type="hidden" id="membdata" name="membdata" value="" />
<input type="submit" value="{$_TR.save}" onclick="serializeData(); return true;" />
<input type="button" value="{$_TR.cancel}" onclick="document.location.href = '{$__redirect}';" />
</form>
<br />

<a class="mngmntlink administration_mngmntlink" href="#" onclick="changeAll(true); return false;">{$_TR.select_all}</a>
&nbsp;&nbsp;|&nbsp;&nbsp;
<a class="mngmntlink administration_mngmntlink" href="#" onclick="changeAll(false); return false;">{$_TR.unselect_all}</a>
