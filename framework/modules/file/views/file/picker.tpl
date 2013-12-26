{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{if $smarty.const.SITE_WYSIWYG_EDITOR=="ckeditor" || $smarty.const.SITE_WYSIWYG_EDITOR=="tinymce"}
    {if $smarty.const.SITE_FILE_MANAGER=="elfinder"}
        {include file="elfinder.tpl"}
    {else}
        {include file="picker_cke.tpl"}
    {/if}
{else}
    {"Uh... yeah, we\'re not supporting that editor. Feel free to integrate it yourself though."|gettext}
{/if}