{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
 


<div class="module countdown show">

    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {if !$config}
        <strong style="color:red">{"To Display the 'Countdown' Module, you MUST First 'Configure Settings'"|gettext|cat:"!"}</strong>
    {else}    
    {if $config.title}<h3>{$config.title}</h3>{/if}

    <script language="JavaScript">
    TargetDate = "{$config.count}";
    BackColor = "";
    ForeColor = "";
    CountActive = true;
    CountStepper = -1;
    LeadingZero = true;
    DisplayFormat = "D:%%D%% H:%%H%% M:%%M%% S:%%S%%";
    FinishMessage = "{$config.message}";
    </script>
    <script language="JavaScript" src="{$asset_path}/js/countdown.js"></script>
    
    <div class="bodycopy">
        {$config.body}
    </div>
    {/if}

</div>

{*
    see http://www.hashemian.com/tools/javascript-countdown.htm
    for more info on this script/
*}