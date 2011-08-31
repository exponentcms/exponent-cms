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
<div class="form_title">{'Edit User'|gettext} "{$user->firstname} {$user->lastname} ({$user->username})"</div>
<div class="form_caption">{'What would you like to do?'|gettext}</div>
{* Lock / Unlock Account *}
{if $user->is_locked}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_lockuser id=$user->id value=0}">{'Unlock Account'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{'This account is locked.  The user will not be able to log in until you unlock it.'|gettext}
</div>
{else}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_lockuser id=$user->id value=1}">{'Lock Account'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{'To prevent this user from logging in, you can lock the account.'|gettext}
</div>
{/if}

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link module=userprofilemodule action=edit id=$user->id}">{'Edit Profile Information'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{'To change this user\'s real name, and other information stored in their profile, click the above link.'}
</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_clearpass id=$user->id}">{'Clear Password'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{'If this user is unable to remember their password, and cannot use the password retrieval system, you can clear the account password here.  (This will reset it to nothing)'|gettext}
</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink administration_mngmntlink" href="{link action=umgr_membership id=$user->id}">{'Manage Groups'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">
{'Assign this user to one or more (or zero) user groups, to ease permission management.'|gettext}
</div>

