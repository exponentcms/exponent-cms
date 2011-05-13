{*
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @copyright  2004-2011 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 *}

{permissions}
{if $permissions.manage == 1}
    <div class="module ecomconfig manage-discounts">
        <div class="form_header">
                <h1>Manage Discounts</h1>
                <p>Here you can configure discounts to be used in your store.</p><p></p>
        </div>
        
        {*icon class=edit action=edit_optiongroup_master record=$group}
        {icon class=delete action=delete_optiongroup_master record=$group onclick="return confirm('This option group is being used by `$group->timesImplemented` products. Deleting this option group will also delete all of the options related to it. Are you sure you want to delete this option group?');"*}
       
        {icon class=add controller="ecomconfig" action="edit_discount" title="Create a New Store Discount" alt="Create a New Store Discount"}
            
        {if $discounts|@count > 0}
            <h2>Modify existing group discount</h2>
            <table class="exp-skin-table">
                <thead>
                <tr>
                    <th>Enabled</th>
                    <th>Name</th>
                    <th>Coupon Code</th>
                    <th>Valid Until</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                {foreach from=$discounts item=discount}
                    <tr class="{cycle values=even,odd}"">
                        {form action=update_discount}
                            {control type="hidden" name="id" value=$discount->id}
                            <td style="text-align:center;">
                            {if $discount->enabled}
                                <a href="{link action=activate_discount id=$discount->id enabled=0}">{img src=`$smarty.const.ICON_RELATIVE`toggle_on.png}</a>
                            {else}
                                <a href="{link action=activate_discount id=$discount->id enabled=1}">{img src=`$smarty.const.ICON_RELATIVE`toggle_off.png}</a>
                            {/if}
                            </td>
                            <td>{$discount->title}</td>  
                            <td>{$discount->coupon_code}</td>  
                            {if $discount->never_expires}
                                <td>{gettext str="Never Expires"}</td>
                            {else}
                                <td>{$discount->enddate|date_format:"%m/%d/%y"} - {$discount->enddate_time|expdate:"g:i a"}</td>  
                            {/if}
                            <td>
                                {icon class=edit action=edit_discount record=$discount}
                                {*icon class=delete action=delete_discount record=$group onclick="return confirm('This option group is being used by `$group->timesImplemented` products. Deleting this option group will also delete all of the options related to it. Are you sure you want to delete this option group?');"*}
                            </td>
                            <!--td>{control type="test" name="title" label=" " value=$discount->title}</td>  
                            <td>{control type="dropdown" name="discount_type" label=" {gettext str="test"}" items=$discount->discount_types value=$discount->discount_type}</td>
                            <td>{control type=text name=discount_amount label=" " size=2 value=$discount->discount_amount}</td>
                            <td>{control type=dropdown name=apply_when items=$apply_rules label=" " value=$discount->discount->apply}</td>
                            <td>{control type=buttongroup submit='Update Discount'|gettext}</td-->
                        {/form} 
                    </tr>
                {/foreach}
            </tbody>
            </table>
        {else}
            <div>You do not have any discounts currently.</div>
        {/if}
    </div>
{/if}
{/permissions}