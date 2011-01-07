{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div id="showhelp" class="module help show hide exp-skin-tabview">
    
    <h1>Help for {$doc->title} (v{$doc->help_version->version})</h1>
    
    {script unique="showtabs" yuimodules="tabview, element"}
    {literal}
        var tabView = new YAHOO.widget.TabView('demo');
        YAHOO.util.Dom.removeClass("showhelp", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');        
    {/literal}
    {/script}
    
    <div id="demo" class="yui-navset">
        <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Overview of {$doc->title}</em></a></li>
            <li><a href="#tab2"><em>Video Tutorials</em></a></li>
            <li><a href="#tab3"><em>Screenshots</em></a></li>
        </ul>            
        <div class="yui-content">
            <div id="tab1">
                {$doc->body}
            </div>
            <div id="tab2">
                {if $doc->expFile.videos == ""}
                    <em>There currently are no videos for {$doc->title}</em>
                {else}
                    // videos go here.
                {/if}
            </div>
            <div id="tab3">
                {if $doc->expFile.screenshots == ""}
                    <em>There currently are no screenshots for {$doc->title}</em>
                {else}
                    {foreach from=$doc->expFile.screenshots item=pic}
                        {img src=$pic->url}
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>    
</div>
<div class="loadingdiv">Loading Help Files for {$doc->title}</div>
