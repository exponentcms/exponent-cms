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

{if $record->parent_id == 0}
	<h2>{'Add options to your product.'|gettext}</h2>
	{control type="hidden" name="tab_loaded[options]" value=1} 
	{'By simply selecting the checkbox in front of an option in an option group (the LABEL column), that option group and option will be added to the checkout process for this product.'|gettext}{br}
	{'By default, the user is NOT required to make a selection.  However, if you select the Required checkbox, the user will be forced to make a selection from that option group.'|gettext} {br}
	{'Select Single presents the option group as a dropdown field where they may select one and only option.'|gettext}{br}
	{'Select Multiple presents the options as a checkbox group where the user may select multiple options'|gettext}.{br}
	{'Selecting the Default radio button for an option will cause that option to be selected by default.'|gettext} {br}{br}
	{include file="`$smarty.const.BASE`framework/modules/ecommerce/products/views/product/options_partial.tpl"}
{else}
	<h2>{'Options are inherited from this product\'s parent.'|gettext}</h2>
{/if}