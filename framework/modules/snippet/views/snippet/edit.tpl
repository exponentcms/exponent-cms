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

<div class="module text edit">
    {if $record->id != ""}
        <h1>{'Editing'|gettext}: {$record->title}</h1>
    {else}
        <h1>{'New Code Snippet'|gettext}</h1>
    {/if}

    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        {control type=text name=title label="Title"|gettext value=$record->title focus=1}
        {control type=textarea cols="80" rows=20 id=body name=body label="Code Snippet"|gettext value=$record->body}
        {control type=buttongroup submit="Save Text"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{if $smarty.const.SITE_CODE_EDITOR == 'ace'}
{script unique='aceeditor' jquery=1 src='https://cdnjs.cloudflare.com/ajax/libs/ace/1.22.1/ace.js'}
{literal}
    // Hook up ACE editor to all textareas with data-editor attribute
    $(function () {
        $('textarea').each(function () {
            var textarea = $(this);
            var editDiv = $('<div>', {
                position: 'absolute',
                width: textarea.width(),
                height: textarea.height(),
                'class': textarea.attr('class')
            }).insertBefore(textarea);
            textarea.css('display', 'none');
            var editor = ace.edit(editDiv[0]);
            editor.getSession().setValue(textarea.val());
            editor.setTheme("ace/theme/{/literal}{$smarty.const.SITE_CODE_EDITOR_THEME}{literal}");
            editor.getSession().setMode("ace/mode/javascript");
            editor.setOptions({
                maxLines: 20,
                minLines: 20,
                autoScrollEditorIntoView: true,
                useWorker: false
            });
            editor.setFontSize(14);

            // copy back to textarea on form submit...
            textarea.closest('form').submit(function () {
                textarea.val(editor.getSession().getValue());
            })
        });
    });
{/literal}
{/script}
{elseif $smarty.const.SITE_CODE_EDITOR == 'codemirror'}
{css unique="snippet-codemirror"}
    .CodeMirror {
      line-height: 1.2em;
      height: 24.4em!important;
    }
{/css}
{literal}
    {/literal}{$cdn = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/'}{literal}
    <script src="{/literal}{$cdn}{literal}codemirror.min.js"></script>
    <link rel="stylesheet" href="{/literal}{$cdn}{literal}codemirror.css">
    <link rel="stylesheet" href="{/literal}{$cdn}{literal}addon/fold/foldgutter.css">
    <link rel="stylesheet" href="{/literal}{$cdn}{literal}theme/{/literal}{$smarty.const.SITE_CODE_EDITOR_THEME}{literal}.css">
    <script src="{/literal}{$cdn}{literal}addon/selection/active-line.js"></script>
    <script src="{/literal}{$cdn}{literal}addon/edit/matchbrackets.js"></script>
    <script src="{/literal}{$cdn}{literal}addon/fold/foldcode.js"></script>
    <script src="{/literal}{$cdn}{literal}addon/fold/foldgutter.js"></script>
    <script src="{/literal}{$cdn}{literal}addon/fold/brace-fold.js"></script>
    <script src="{/literal}{$cdn}{literal}addon/fold/comment-fold.js"></script>
    <script src="{/literal}{$cdn}{literal}mode/javascript/javascript.js"></script>
    <script>
        var editor = CodeMirror.fromTextArea(document.getElementById("body"), {
            lineNumbers: true,
            theme: "{/literal}{$smarty.const.SITE_CODE_EDITOR_THEME}{literal}",
            mode: "javascript",
//            lineWrapping: true,
            styleActiveLine: true,
            foldGutter: true,
            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
        });
    </script>
{/literal}
{/if}
