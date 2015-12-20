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

{if $record->parent_id == 0}
    {control type="hidden" name="tab_loaded[options]" value=1}
    {if count($record->childProduct)}
        <h4><em>({'Child products inherit these settings.'|gettext})</em></h4>
    {/if}
	<h2>{'Add options to your product.'|gettext}</h2>
    {icon class="manage" controller=ecomconfig action=options text="Manage Product Options"|gettext}{br}
    {control type="checkbox" name="options[show_options]" id=show_options label="Display options on product page"|gettext value=1 checked=$record->show_options postfalse=1 description='Options will be displayed on the product page instead of a secondary page when adding item to cart'|gettext}
    {control type="checkbox" name="options[segregate_options]" id=segregate_options label="Segregate required options?"|gettext value=1 checked=$record->segregate_options postfalse=1 description='Should we group the required and non-required options separately'|gettext}
    <blockquote>
        {'By selecting the checkbox in front of an option in an option group (the LABEL column), that option group and option will be added to the checkout process for this product.'|gettext}{br}
        <ul>
            <li><strong>{"Required"|gettext}</strong> - {'By default, the user is NOT required to make a selection.  However, selecting the Required checkbox will force the user to make a selection from that option group.'|gettext}</li>
            <li><strong>{"Select Single"|gettext}</strong> - {'Presents the option group as a dropdown field where the user may select one and only option.'|gettext}</li>
            <li><strong>{"Select Multiple"|gettext}</strong> - {'Presents the options as a checkbox group where the user may select multiple options'|gettext}</li>
            <li><strong>{"Option Available"|gettext}</strong> - {'Presents selected option with product.'|gettext}</li>
            <li><strong>{"User Input"|gettext}</strong> - {'Selecting the User Input checkbox will only display user input entry if that option is chosen.'|gettext}</li>
            <li><strong>{"Default"|gettext}</strong> - {'Selecting the Default radio button for an option causes that option to become selected by default.'|gettext}</li>
        </ul>
        {'You may also enter any cost adjustments (up/down, dollars/percentage) for that option.'|gettext}{br}
        {'Click on the \'More\' link to enter the option\'s weight.'|gettext}{br}
    </blockquote>
    {if $permissions.manage}
        {ddrerank items=$record->optiongroup only="product_id=`$record->id`" model=optiongroup label=$record->title|cat:' '|cat:'Options'|gettext}
    {/if}
	{include file="`$smarty.const.BASE`framework/modules/ecommerce/products/views/product/options_partial.tpl"}
{else}
	<h4><em>({'Options'|gettext} {'are inherited from this product\'s parent.'|gettext})</em></h4>
{/if}