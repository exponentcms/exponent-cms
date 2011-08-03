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
<div class="form_title">{$_TR.form_title} "{$user->firstname} {$user->lastname} ({$user->username})"</div>
<div class="form_caption">{$_TR.form_header}</div>
{* Lock / Unlock Account *}
{if $user->is_locked}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_lockuser id=$user->id value=0}">{$_TR.unlock_account}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{$_TR.unlock_description}
</div>
{else}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_lockuser id=$user->id value=1}">{$_TR.lock_account}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{$_TR.lock_description}
</div>
{/if}

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link module=userprofilemodule action=edit id=$user->id}">{$_TR.edit_user}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{$_TR.edit_description}
</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_clearpass id=$user->id}">{$_TR.clear_password}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{$_TR.clear_description}
</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_membership id=$user->id}">{$_TR.manage_groups}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{$_TR.groups_description}
</div>

