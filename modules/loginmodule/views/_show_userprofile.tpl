{*
 *
 * Copyright (c) 2004-2005 James Hunt and the OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * Exponent is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU
 * General Public License along with Exponent; if
 * not, write to:
 *
 * Free Software Foundation, Inc.,
 * 59 Temple Place,
 * Suite 330,
 * Boston, MA 02111-1307  USA
 *
 * $Id: Default.tpl,v 1.7 2005/04/08 15:45:48 filetreefrog Exp $
 *}

<h2>User Profile for {$profile->firstname} {$profile->lastname}</h2>
<table width="550" cellpadding="3" cellspacing="0" border="0">
<tr>
  <td style="border-bottom: 1px solid black;">
    {if $profile->image_url != ""}
      <img src="{$profile->image_url}" border="0">
    {else}
      <img src="{$smarty.const.THEME_RELATIVE}images/not_available.jpeg" border="0">
    {/if}
  </td>
  <td style="border-bottom: 1px solid black;"><h2>{$profile->username}</h2></td>
</tr>
<tr>
  <td style="padding-top: 5px">Email Address</td>
  <td style="padding-top: 5px">{$profile->email}</td>
</tr>
<tr>
  <td>ICQ Number</td>
  <td>{$profile->icq_num}</td>
</tr>
<tr>
  <td>AIM Address</td>
  <td>{$profile->aim_addy}</td>
</tr>
<tr>
  <td>MSN Messenger</td>
  <td>{$profile->msn_addy}</td>
</tr>
<tr>
  <td>Yahoo Messenger</td>
  <td>{$profile->yahoo_addy}</td>
</tr>
<tr>
  <td>Website</td>
  <td>{$profile->website}</td>
</tr>
<tr>
  <td>Location</td>
  <td>{$profile->location}</td>
</tr>
<tr>
  <td>Occupation</td>
  <td>{$profile->occupation}</td>
</tr>
<tr>
  <td>Interests</td>
  <td>{$profile->interests}</td>
</tr>
</table>


