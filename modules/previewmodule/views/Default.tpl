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

{if $editMode == 1}
	<a class="preview" href="{link action=preview}">{$_TR.preview}</a>
{/if}
{if $previewMode == 1}
	<a class="edit" href="{link action=normal}">{$_TR.edit_mode}</a>
{/if}	



