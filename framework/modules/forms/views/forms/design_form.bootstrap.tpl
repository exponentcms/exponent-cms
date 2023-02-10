{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{if $config.style}
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
    {*<div class="form_title">*}
        {*{if $edit_mode != 1}*}
            <div class="module-actions">
                {*{ddrerank module="forms_control" model="forms_control" where="forms_id=`$form->id`" sortfield="caption" label="Form Controls"|gettext}*}
                {icon id='toggle_grid' action=scriptaction text='Turn Off Designer Grid'|gettext}
                {*{icon id='toggle_style' action=scriptaction text='Style'|gettext}*}
            </div>
        {*{/if}*}
    {*</div>*}
    {if $edit_mode != 1}
    <div class="form-wrapper">
    {/if}
        {$form_html}
    {if $edit_mode != 1}
    </div>
    {/if}
    {if $edit_mode != 1}
        {*<table cellpadding="5" cellspacing="0" border="0">*}
            {*<tr>*}
                {*<td style="border:none;">*}
                    {*<form role="form" method="post" action="{$smarty.const.PATH_RELATIVE}index.php"{if !bs3()} class="exp-skin"{/if}>*}
                        {*<input type="hidden" name="controller" value="forms"/>*}
                        {*<input type="hidden" name="action" value="edit_control"/>*}
                        {*<input type="hidden" name="forms_id" value="{$form->id}"/>*}
                        {*<div class="row">*}
                            {*<div class="col-md-3">{'Add a'|gettext} </div>*}
                            {*<div class="col-md-8">*}
                                {*<select class="form-control" name="control_type" onchange="this.form.submit()">*}
                                    {*{foreach from=$types key=value item=caption}*}
                                        {*<option value="{$value}">{$caption}</option>*}
                                    {*{/foreach}*}
                                {*</select>*}
                            {*</div>*}
                        {*</div>*}
                    {*</form>*}
                {*</td>*}
            {*</tr>*}
        {*</table>*}
    {*<p><a class="{button_style}"*}
    {*href="JavaScript: pickSource();">{'Append fields from existing form'|gettext}</a></p>*}

    {*script unique="viewform"}
    function pickSource() {ldelim}
    window.open('{$pickerurl}','sourcePicker','title=no,toolbar=no,width=800,height=600,scrollbars=yes');
    {rdelim}
    {/script*}
    {*{if !empty($forms_list)}{control type="dropdown" name="forms_id" label="Append fields from an existing form"|gettext items=$forms_list}{/if}*}
        {*<blockquote>*}
            {*{'Use the drop down to add fields to this form.'|gettext}*}
            {*<em>{'The first/top-most page break control will always be pushed to the top!'|gettext}</em>*}
        {*</blockquote>*}
        <p{if newui()} class="exp-skin"{/if}>
            {*<a class="{button_style}" href="{$backlink}">{'Done'|gettext}</a>*}
            {br}{icon button=true class=reply link=$backlink text='Exit Forms Designer'|gettext}
        </p>
    {/if}
</div>
<div id="trash" class="trash">
    <strong>{'Trash Can'|gettext}</strong><br><br>
    {img class="img-center" src="`$smarty.const.PATH_RELATIVE`framework/modules/recyclebin/assets/images/trashcan_full_large.png"}
</div>
<ul id="controls" class="controls">
    <strong>{'Available Form Controls'|gettext}</strong>
    {foreach from=$types key=value item=caption}
        <li class="item" type="{$value}">
            {$caption}
        </li>
    {/foreach}
</ul>

{script unique="design-form" jquery="Sortable,jquery-confirm"}
{literal}
    $(document).ready(function(){
        // Helper function to get parameters from the url
        function getUrlParam(paramName, pathname) {
            var pathArray = pathname.split( '/' );
            if (EXPONENT.SEF_URLS && pathArray.indexOf(paramName) != -1) {
                var parm = pathArray.indexOf(paramName);
                if (parm > 0) return pathArray[parm+1];
            } else {
                var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
                var match = window.location.search.match(reParam) ;
                return (match && match.length > 1) ? match[1] : '' ;
            }
        }

        // turn form grid on/off
        $('#toggle_grid').on('click', function(evt) {
            $('.forms.design-form .formmoduleedit.item').toggleClass('clean');
            $('.forms.design-form .form-wrapper').toggleClass('clean');
            $('#toggle_grid').toggleClass('active');
        });

        // toggle form style
        // $('#toggle_style').on('click', function(evt) {
        //     $('#fakeform').toggleClass('form-horizontal');
        //     $('#fakeform .control-label').toggleClass('col-sm-2');
        //     $('#toggle_style').toggleClass('active');
        // });

        // we need to catch 'edit' button clicks
        $('#fakeform').on('click', '.edit', function(evt) {
            evt.preventDefault();
            var ctl = evt.target;
            if (ctl.tagName != 'A')
                ctl = ctl.parentNode;
            var id = getUrlParam('id', ctl.href);
            $.confirm({
                title: '{/literal}{'Edit Control'|gettext}{literal}',
                content: function() {
                    var self = this;
                    return $.ajax({
                        url: EXPONENT.PATH_RELATIVE + "index.php?controller=forms&action=edit_control&ajax_action=1&forms_id={/literal}{$form->id}{literal}&id=" + id,
                    }).done(function (response) {
                        self.setContent(response);
                    });
                },
                confirmButton: '{/literal}{'Save'|gettext}{literal}',
                cancelButton: '{/literal}{'Cancel'|gettext}{literal}',
                confirm: function() {
                    if (typeof CKEDITOR !== "undefined") {
                        for ( instance in CKEDITOR.instances )
                            CKEDITOR.instances[instance].updateElement();
                    }
                    $.ajax({
                        type: "POST",
                        headers: { 'X-Transaction': 'Delete Form Control'},
                        url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=save_control&ajax_action=1',
                        dataType: 'json',
                        data: $(".forms.edit.edit-control form").serialize(),
                        success:function(msg) {
                            $.ajax({
                                type: "POST",
                                headers: { 'X-Transaction': 'Build Form Control'},
                                url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=build_control&ajax_action=1',
                                data: 'id=' + msg,
                                success:function(msg) {
                                    // get the (fake) control html and display it to the page
                                    $(ctl).closest('.item').replaceWith(msg);  //  update control in the displayed form
                                    $('#fakeform .delete').attr('onClick', '');  // remove delete button non-ajax onClick action
                                }
                            });
                        }
                    });
                }
            });
        });

        // we need to catch 'delete' button clicks
        $('#fakeform').on('click', '.delete', function(evt) {
            evt.preventDefault();
            var ctl = evt.target;
            if (ctl.tagName != 'A')
                ctl = ctl.parentNode;
            var id = getUrlParam('id', ctl.href);
            // need to display a confirm dialog before deleting
            $.confirm({
                title: '{/literal}{'Delete Form Control'|gettext}{literal}',
                content: '{/literal}{'Delete this control?'|gettext}{literal}',
                confirmButton: '{/literal}{'Yes'|gettext}{literal}',
                cancelButton: '{/literal}{'No'|gettext}{literal}',
                // we then need to remove the control from the database
                confirm: function() {
                    $.ajax({
                        type: "POST",
                        headers: { 'X-Transaction': 'Delete Form Control'},
                        url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=delete_control&ajax_action=1',
                        data: 'id=' + id,
                        success:function(msg) {
                            $(ctl).closest('.item').remove();  //  remove control from the displayed form
                        }
                    });
                }
            });
        });

        // we don't want the non-ajax onClick action taking place first
        $('#fakeform .delete').attr('onClick', '');

        // form
        Sortable.create(fakeform, {
            group: {
                name: 'form',
                put: ['controls']
            },
            animation: 250,
            draggable: ".item",
            onAdd: function (evt) {  // new control was added to form
                var itemEl = $(evt.item);  // the new dragged control
                $.confirm({
                    title: '{/literal}{'Edit Control'|gettext}{literal}',
                    content: function() {
                        var self = this;
                        return $.ajax({
                            url: EXPONENT.PATH_RELATIVE + "index.php?controller=forms&action=edit_control&ajax_action=1&forms_id={/literal}{$form->id}{literal}&control_type=" + evt.item.type + "&rank=" + (evt.newIndex + 1),
                        }).done(function (response) {
                            self.setContent(response);
                        });
                    },
                    confirmButton: '{/literal}{'Save'|gettext}{literal}',
                    cancelButton: '{/literal}{'Cancel'|gettext}{literal}',
    //fixme we should act differently for static controls which are auto-added without edit
                    confirm: function() {
                        // we need to trap the 'save' action to save the control to the database by calling formsController->save_control()
                        //Retreive the data from the form:
                        if (typeof CKEDITOR !== "undefined") {
                            for ( instance in CKEDITOR.instances )
                                CKEDITOR.instances[instance].updateElement();
                        }
                        var data = $(".forms.edit.edit-control form").serializeArray();
                        //Add in additional data to the original form data:
                        data.push(
                            {name: 'rank', value: evt.newIndex + 1}
                        );
                        $.ajax({
                            type: "POST",
                            headers: { 'X-Transaction': 'Add Form Control'},
                            url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=save_control&ajax_action=1',
                            dataType: 'json',
                            data: data,
                            success:function(msg) {
                                //fixme we get a control id after save to then build the control
                                $.ajax({
                                    type: "POST",
                                    headers: { 'X-Transaction': 'Build Form Control'},
                                    url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=build_control&ajax_action=1',
                                    data: 'id=' + msg,
                                    success:function(msg) {
                                        //fixme we must get the (fake) control html and display it to the page via itemEl.html()
                                        // we then need to remove it from the display
                                        $(evt.item).replaceWith(msg);  //  add control to the displayed form
                                        $('#fakeform .delete').attr('onClick', '');
                                    }
                                });
                            }
                        });
                    }
                });
                evt.preventDefault();
            },
            onEnd: function (evt) {  // control was moved to new rank
                if (evt.oldIndex != evt.newIndex && evt.newIndex != undefined) {
                    // we need to rerank the controls by calling formsController->rerank_control()
                    $.ajax({
                        type: "POST",
                        headers: { 'X-Transaction': 'Rerank Form Control'},
                        url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=rerank_control&ajax_action=1',
                        data: 'id=' + evt.item.id + '&rank=' + (evt.newIndex + 1),
                    });
                }
            },
        });

        // control list
        Sortable.create(controls, {
            group: {
                name: 'controls',
                pull: 'clone',
                put: false
            },
            sort: false,
            draggable: ".item",
        });

        // trash can
        Sortable.create(trash, {
            group: {
                name: 'trash',
                pull: false,
                put: ['form']
            },
            sort: false,
            onAdd: function (evt) {
                var itemEl = $(evt.item);  // dragged control from form
                // we need to remove the control from the database
                $.ajax({
                    type: "POST",
                    url: EXPONENT.PATH_RELATIVE+'index.php?controller=forms&action=delete_control&ajax_action=1',
                    data: 'id=' + evt.item.id,
                    success: function(o){
                        itemEl.remove();  //  we don't won't to show removed controls sitting in the trash can
                        //  control has already been removed from the displayed form
                    }
                });
            },
        });
    });
{/literal}
{/script}
