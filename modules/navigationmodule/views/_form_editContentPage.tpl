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
{css unique="newpage" corecss="forms"}

{/css}

<div class="navigationmodule form-editContentPage"> 
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help Editing Content Pages" module="edit-page"}
        </div>
		<h1>{if $is_edit == 1}{$_TR.form_title_edit}{else}{$_TR.form_title_new}{/if}</h1>
	</div>
    <p>{if $is_edit == 1}{$_TR.form_header_edit}{else}{$_TR.form_header_new}{/if}</p>

    {$form_html}
</div>
