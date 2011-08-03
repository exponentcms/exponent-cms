{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module formbuilder edit-control">
	<div class="form_title">
		<h1>{if $is_edit == 1}{$_TR.form_title_edit}{else}{$_TR.form_title_new}{/if} - {$type}</h1>
	</div>
	{$form_html}
	{if $is_edit != 1 && $type != "htmlcontrol"}{br}<i><b>** {$_TR.reset_report} **</b></i>{/if}
</div>

