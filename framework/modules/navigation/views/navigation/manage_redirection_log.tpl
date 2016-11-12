{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="redirect-log" corecss="button,tables"}

{/css}

<div class="module navigation manage-redirection-log">
    <h2>{'Page Redirection Log'|gettext}</h2>
    {if $page->total_records}{icon class="delete" action=delete_redirection_log text='Delete Page Redirection Log'|gettext onclick="return confirm('"|cat:("Delete the entire log?"|gettext)|cat:"');"}{/if}
    {$page->links}
    <div class="exp-ecom-table">
        {$page->table}
    </div>
	{$page->links}
</div>