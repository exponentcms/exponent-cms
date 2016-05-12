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

{*
{css unique="install-upgrades"}
{literal}
    .install-upgrades h3 {
        display       : inline;
    }
{/literal}
{/css}
*}

<div class="module administration install-upgrades">
    <h1>{'Run Upgrade Scripts'|gettext}</h1>
    <blockquote>
        {'The list of upgrade scripts below apply to the current version, HOWEVER we do NOT recommend running them outside of the upgrade process!'|gettext}
        {'Please run these scripts at your own risk.'|gettext}
    </blockquote>
    {form action=install_upgrades_run}
        <ol>
            {foreach from=$scripts item=upgradescript key=name}
                <li>
                    {$title = $upgradescript->name()}
                    <h3>{control type="checkbox" name=$upgradescript->classname label=$title value=1}</h3>
                    <p>{$upgradescript->description()}</p>
                </li>
            {foreachelse}
                <h3>{'There are no upgrade scripts available to run'|gettext}</h3>
            {/foreach}
        </ol>
        {control type="buttongroup" submit="Run Selected Scripts"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
