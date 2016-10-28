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

{permissions}
    <div class="exp-container-module-wrapper exp-skin">
        <div class="exp-container-chrome exp-container-chrome-module hardcoded-chrome">
            <a id="dropdownMenu{$container->id}" class="exp-trigger" data-toggle="dropdown" href="#">{$container->info.module|gettext} <i class="fa fa-caret-down fa-fw"></i></a>
            {nocache}{getchromemenu module=$container rank=$i+1 rerank=$rerank last=$last hcview=1}{/nocache}
        </div>
    </div>
{/permissions}

{script unique="hard-coded-module" jquery=1}
{literal}
    $(document).ready(function(){
        // move hard coded mod menus inside the mod wrapper they pertain to
        $('.hardcoded-chrome').each(function(k,node){
            $(node.parentNode).next().prepend($(node.parentNode));
        });
    });
{/literal}
{/script}
