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
{literal}
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/ace/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var editor = ace.edit("body");
        editor.setTheme("ace/theme/twilight");
        editor.getSession().setMode("ace/mode/javascript");
        editor.setOptions({
            maxLines: 20,
            minLines: 20,
            autoScrollEditorIntoView: true,
            useWorker: false
        });
        editor.setFontSize(14);
    </script>
{/literal}
{elseif $smarty.const.SITE_CODE_EDITOR == 'codemirror'}
{css unique="snippet-codemirror"}
    .CodeMirror {
      line-height: 1.2em;
      height: 24.4em!important;
    }
{/css}
{literal}
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/lib/codemirror.css">
    <link rel="stylesheet" href="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/addon/fold/foldgutter.css">
    <link rel="stylesheet" href="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror//theme/twilight.css">
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/addon/fold/foldcode.js"></script>
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/addon/fold/foldgutter.js"></script>
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/addon/fold/brace-fold.js"></script>
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/addon/fold/comment-fold.js"></script>
    <script src="{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/CodeMirror/mode/javascript/javascript.js"></script>
    <script>
        var editor = CodeMirror.fromTextArea(document.getElementById("body"), {
            lineNumbers: true,
            theme: "twilight",
            mode: "javascript",
//            lineWrapping: true,
            foldGutter: true,
            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
        });
    </script>
{/literal}
{/if}
