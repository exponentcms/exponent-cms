{if $smarty.const.SITE_WYSIWYG_EDITOR=="ckeditor"}
    {include file="picker_cke.tpl"}
{else}
    {"Uh... yeah, we\'re not supporting that editor. Feel free to integrate it yourself though."|gettext}
{/if}