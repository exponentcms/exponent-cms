{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
 
{group label="Image Slideshow Configuration"|gettext}
    {control type=text name="width" label="Slideshow width"|gettext value=$config.width|default:350 size="5"}
    {control type=text name="height" label="Slideshow height"|gettext value=$config.height|default:200 size="5"}
    {control type=text name="speed" label="Slideshow speed in seconds per slide"|gettext value=$config.speed|default:5 size="5"}
    {control type=text name="quality" label="Slide thumbnail JPEG quality"|gettext|cat:" (0 - 95, 100)" description="If quality is set to 100, the raw image will be used instead of thumbnailing"|gettext value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
    {{control type=checkbox name="hidetext" label="Hide slide title"|gettext checked=$config.hidetext value=1}}
    {control type="checkbox" name="hidecontrols" label="Hide slide controls"|gettext checked=$config.hidecontrols|default:0 value=1}

    {*{control type="checkbox" name="autoplay" label="Autoplay"|gettext checked=$config.autoplay|default:1 value=1}*}

    {group label='Transition Animation'|gettext}
        {control type=dropdown name="anim_in"
            items="None,Fade In,Fade Out,Slide In to Right,Slide Out to Right,Slide In to Left,Slide Out to Left,Slide In to Bottom,Slide Out to Bottom,Slide In to Top,Slide Out to Top"|gettxtlist
            values="none,fadeIn,fadeOut,swipeInLTR,swipeOutLTR,swipeInRTL,swipeOutRTL,swipeInTTB,swipeOutTTB,swipeInBTT,swipeOutBTT"
            label="Transition In" value=$config.anim_in|default:'fadeIn'
        }
        {control type=dropdown name="anim_out"
            items="None,Fade In,Fade Out,Slide In to Right,Slide Out to Right,Slide In to Left,Slide Out to Left,Slide In to Bottom,Slide Out to Bottom,Slide In to Top,Slide Out to Top"|gettxtlist
            values="none,fadeIn,fadeOut,swipeInLTR,swipeOutLTR,swipeInRTL,swipeOutRTL,swipeInTTB,swipeOutTTB,swipeInBTT,swipeOutBTT"
            label="Transition Out" value=$config.anim_out|default:'fadeOut'
        }
        {control type=text name="duration" label="Animation Duration"|gettext value=$config.duration|default:0.5 size="5"}
    {/group}

    {*<h4>{'Configure the box size of the Slideshow frame'|gettext}</h4>*}
    {*{control type=text name="pa_slideshow_width" label="Slideshow Width"|gettext value=$config.pa_slideshow_width|default:100 size="5"}*}
    {*{control type=text name="pa_slideshow_height" label="Slideshow Height"|gettext value=$config.pa_slideshow_height|default:100 size="5"}*}

    {*<h4>{'Configure the box size the Slideshow images'|gettext}</h4>*}
    {*{control type=text name="pa_image_width" label="Image Width"|gettext value=$config.pa_image_width|default:100 size="5"}*}
    {*{control type=text name="pa_image_height" label="Image Height"|gettext value=$config.pa_image_height|default:100 size="5"*}
{/group}