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
<div id="carousel-container{$config.uniqueid}" class="carousel-container yui-skin-sam hide" style="float: left;">
    
    <div id="container{$config.uniqueid}">
        <ol id="carousel">
           	{foreach from=$files item=pic}
            <li class="item" style="width: {$config.carousel_width}px; height: {$config.carousel_height}px;">
                {img id="img`$pic->id`" class=$params.class file_id=$pic->id hs=`$config.carousel_square` ws=`$config.carousel_square`"}           
            </li>
			{/foreach}           
        </ol>
    </div>
 
</div><!-- end yui-skin-sam -->
<div class="loading-div"><img src="{$smarty.const.URL_BASE}/external/yui/build/carousel/assets/ajax-loader.gif"></div>         
 
{script unique="yuicarousel`$config.uniqueid`" yuimodules="carousel,animation"}
{literal}

    YAHOO.util.Event.onDOMReady(first);
    
           var carousel;
                
       function first () {
            var carousel    = new YAHOO.widget.Carousel("container{/literal}{$config.uniqueid}{literal}", {
                        animation: { speed: 1.0, {/literal}{if $config.carousel_animate !=""}effect:YAHOO.util.Easing.{$config.carousel_animate}{/if}{literal}},
                        {/literal}{if $config.carousel_circular !=""}isCircular: {$config.carousel_circular},{/if}{literal}
                        {/literal}{if $config.carousel_vertical !=""}isVertical: {$config.carousel_vertical},{/if}{literal}
                        {/literal}{if $config.carousel_num_visible !=""}numVisible: {$config.carousel_num_visible},{/if}{literal}
                        firstVisible: 0,
                        scrollIncrement: 1
                        //numItems: 3
                        //autoPlay: 2000
                        });
                    //carousel.set("revealAmount", 20);
                    carousel.set("autoPlayInterval", 5500); //essential for autoplay, should do a config later
                    carousel.startAutoPlay(); //also essential for autoplay
                    carousel.render(); // get ready for rendering the widget
                    carousel.show();   // display the widget
                    
			
		YAHOO.util.Dom.removeClass("carousel-container{/literal}{$config.uniqueid}{literal}", 'hide');                 // looks for id of carouselcontainer, removed class of hide.
        var loading = YAHOO.util.Dom.getElementsByClassName('loading-div', 'div');     //finds loading-div... creates variable.
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');                          // hides loading-div element...  setStyle of loading div to display: none.
			
        }
    
{/literal}
{/script}
 