{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    {icon class="manage" controller=ecomconfig action=options text="Manage Product Options"|gettext}{br}
    <blockquote>
        {'By selecting the checkbox in front of an option in an option group (the LABEL column), that option group and option will be added to the checkout process for this product.'|gettext}{br}
        <ul>
            <li><strong>{"Required"|gettext}</strong> - {'By default, the user is NOT required to make a selection.  However, selecting the Required checkbox will force the user to make a selection from that option group.'|gettext}</li>
            <li><strong>{"Select Single"|gettext}</strong> - {'Presents the option group as a dropdown field where the user may select one and only option.'|gettext}</li>
            <li><strong>{"Select Multiple"|gettext}</strong> - {'Presents the options as a checkbox group where the user may select multiple options'|gettext}</li>
            <li><strong>{"Default"|gettext}</strong> - {'Selecting the Default radio button for an option causes that option to become selected by default.'|gettext}</li>
        </ul>
        {'You may also enter any cost adjustments (up/down, dollars/percentage) for that option.  Click on the \'More\' link to enter the option\s weight.'|gettext}{br}
    </blockquote>
	{include file="`$smarty.const.BASE`framework/modules/ecommerce/products/views/product/options_partial.tpl"}
{else}
	<h2>{'Options'|gettext} {'are inherited from this product\'s parent.'|gettext}</h2>
{/if}