{*permissions level=$smarty.const.UILEVEL_PERMISSIONS}
{if $permissions.configure == 1 || $permissions.administrate == 1}
<div class="modulepermissions">
	{if $permissions.administrate == 1}
        <a href="{link action=userperms _common=1 int=$int}"><img class="mngmnt_icon" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm}" alt="{$_TR.alt_userperm}" /></a>
        <a href="{link action=groupperms _common=1 int=$int}"><img class="mngmnt_icon" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm}" alt="{$_TR.alt_groupperm}" /></a>
	{/if}

	{if $permissions.configure == 1}
        <a href="{link action=configure _common=1 int=$int}"><img class="mngmnt_icon" src="{$smarty.const.ICON_RELATIVE}configure.png" title="{$_TR.alt_configure}" alt="{$_TR.alt_configure}" /></a>
	{/if}
</div>
{/if}
{/permissions*}
