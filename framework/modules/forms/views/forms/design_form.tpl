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

{css unique="design-form" lesscss="`$asset_path`less/designer.less" corecss="button"}

{/css}

{if !bs()}
{css unique="design-form2"}
    .formmoduleedit.item .item-actions a {
        height: 16px;
    }
{/css}
{/if}

{if $style && !bs3()}
    {css unique="formmod2" corecss="forms2col"}

    {/css}
{else}
    {css unique="formmod2"}
        .stepy-step label {
        	margin         : 0;
        }
    {/css}
{/if}

<div class="info-header">
    <div class="related-actions">
        {help text="Get Help with"|gettext|cat:" "|cat:("Designing Forms"|gettext) module="design-forms"}
    </div>
    <h2>{"Forms Designer"|gettext}</h2>
</div>
<div class="module forms design-form">
    <div class="form_title">
        {if $edit_mode != 1}
            <div class="module-actions">
                {icon class=selectnone id='toggle_grid' action=scriptaction text='Toggle Designer Grid'|gettext title='Provides more accurate form display'|gettext}
                {ddrerank module="forms_control" model="forms_control" where="forms_id=`$form->id`" sortfield="caption" label="Form Controls"|gettext}
            </div>
        {/if}
    </div>
    {if $edit_mode != 1}
    <div class="form-wrapper">
    {/if}
        {$form_html}
    {if $edit_mode != 1}
    </div>
    {/if}
    {if $edit_mode != 1}
        <table cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td style="border:none;">
                    <form role="form" method="post" action="{$smarty.const.PATH_RELATIVE}index.php"{if !bs3()} class="exp-skin"{/if}>
                        <input type="hidden" name="controller" value="forms"/>
                        <input type="hidden" name="action" value="edit_control"/>
                        <input type="hidden" name="forms_id" value="{$form->id}"/>
                        <div class="row">
                            <div class="col-md-3">{'Add a'|gettext} </div>
                            <div class="col-md-8">
                                <select class="form-control" name="control_type" onchange="this.form.submit()">
                                    {foreach from=$types key=value item=caption}
                                        <option value="{$value}">{$caption}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </form>
                </td>
            </tr>
        </table>
    {*<p><a class="{button_style}"*}
    {*href="JavaScript: pickSource();">{'Append fields from existing form'|gettext}</a></p>*}

    {*script unique="viewform"}
    function pickSource() {ldelim}
    window.open('{$pickerurl}','sourcePicker','title=no,toolbar=no,width=800,height=600,scrollbars=yes');
    {rdelim}
    {/script*}
    {*{if !empty($forms_list)}{control type="dropdown" name="forms_id" label="Append fields from an existing form"|gettext items=$forms_list}{/if}*}
        <blockquote>
            {'Use the drop down to add fields to this form.'|gettext}
            <em>{'The first/top-most page break control will always be pushed to the top!'|gettext}</em>
        </blockquote>
        <p{if bs3()} class="exp-skin"{/if}>
            {*<a class="{button_style}" href="{$backlink}">{'Done'|gettext}</a>*}
            {icon button=true class=reply link=$backlink text='Done'|gettext}
        </p>
    {/if}
</div>

{script unique="design-form" jquery=1}
{literal}
    $(document).ready(function(){
        // turn form grid on/off
        $('#toggle_grid').on('click', function(evt) {
            $('.forms.design-form .formmoduleedit.item').toggleClass('clean');
            $('.forms.design-form .form-wrapper').toggleClass('clean');
            $('#toggle_grid').toggleClass('active');
        });
    });
{/literal}
{/script}
