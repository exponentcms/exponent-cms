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
{if $success == 1}
<br /><br />{$_TR.configuration_saved} <br /><a class="mngmntlink" href="{$backlink}">{$_TR.continue}</a><hr size="1" />
{else}
<div class="error">{$_TR.errors}:</div>
<div style="padding-left: 15px;">
{$errors}
<br /><br />{$_TR.not_saved}<br /><a class="mngmntlink" href="{$smarty.server.HTTP_REFERER}">{$_TR.reconfigure}</a><br /><br />
</div>
{/if}
