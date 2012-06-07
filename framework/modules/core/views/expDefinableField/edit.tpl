{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
{css unique="definable-field-edit" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/forms.css"}

{/css}
<div class="module expDefinableField edit">
	<div class="form_header">
		<h1>{if $is_edit == 1}{'Edit Definable Field'|gettext}{else}{'Create a New Definable Field'|gettext}{/if} - {$types}</h1>
      
    </div>
	  {$form_html}
</div>

