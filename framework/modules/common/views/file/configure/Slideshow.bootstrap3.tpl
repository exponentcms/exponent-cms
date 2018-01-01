{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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
    {*{control type=text name="quality" label="Slide thumbnail JPEG quality"|gettext|cat:" (0 - 95, 100)" description="If quality is set to 100, the raw image will be used instead of thumbnailing"|gettext value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}*}
    {{control type=checkbox name="hidetext" label="Hide slide title"|gettext checked=$config.hidetext value=1}}
    {*{control type="checkbox" name="hidecontrols" label="Hide slide controls"|gettext checked=$config.hidecontrols|default:0 value=1}*}
    {*{control type="checkbox" name="dimcontrols" label="Dim controls"|gettext checked=$config.dimcontrols|default:0 value=1}*}
    {control type="checkbox" name="autoplay" label="Autoplay"|gettext checked=$config.autoplay|default:1 value=1}

    {group label='Transition Animation'|gettext}
        {control type=dropdown name="anim_in"
            items="Bounce,Bounce In,Bounce In Down,Bounce In Left,Bounce In Right,Bounce In Up,Bounce Out,Bounce Out Down,Bounce Out Left,Bounce Out Right,Bounce Out Up,Fade In,Fade In Down,Fade In Down Big,Fade In Left,Fade In Left Big,Fade In Right,Fade In Right Big,Fade In Up,Fade In Up Big,Fade Out,Fade Out Down,Fade Out Down Big,Fade Out Left,Fade Out Left Big,Fade Out Right,Fade Out Right Big,Fade Out Up,Fade Out Up Big,Flash,Flip,Flip In X,Flip In Y,Flip Out X,Flip Out Y,Hinge,Light Speed In,Light Speed Out,Pulse,Roll In,Roll Out,Rotate In,Rotate In Down Left,Rotate In Down Right,Rotate In Up Left,Rotate In Up Right,Rotate Out,Rotate Out Down Left,Rotate Out Down Right,Rotate Out Up Left,Rotate Out Up Right,Shake,Swing,Tada,Wobble"|gettxtlist
            values="bounce,bounceIn,bounceInDown,bounceInLeft,bounceInRight,bounceInUp,bounceOut,bounceOutDown,bounceOutLeft,bounceOutRight,bounceOutUp,fadeIn,fadeInDown,fadeInDownBig,fadeInLeft,fadeInLeftBig,fadeInRight,fadeInRightBig,fadeInUp,fadeInUpBig,fadeOut,fadeOutDown,fadeOutDownBig,fadeOutLeft,fadeOutLeftBig,fadeOutRight,fadeOutRightBig,fadeOutUp,fadeOutUpBig,flash,flip,flipInX,flipInY,flipOutX,flipOutY,hinge,lightSpeedIn,lightSpeedOut,pulse,rollIn,rollOut,rotateIn,rotateInDownLeft,rotateInDownRight,rotateInUpLeft,rotateInUpRight,rotateOut,rotateOutDownLeft,rotateOutDownRight,rotateOutUpLeft,rotateOutUpRight,shake,swing,tada,wobble"
            label="Transition In" value=$config.anim_in|default:'fadeIn'
        }
        {control type=dropdown name="anim_out"
            items="Bounce,Bounce In,Bounce In Down,Bounce In Left,Bounce In Right,Bounce In Up,Bounce Out,Bounce Out Down,Bounce Out Left,Bounce Out Right,Bounce Out Up,Fade In,Fade In Down,Fade In Down Big,Fade In Left,Fade In Left Big,Fade In Right,Fade In Right Big,Fade In Up,Fade In Up Big,Fade Out,Fade Out Down,Fade Out Down Big,Fade Out Left,Fade Out Left Big,Fade Out Right,Fade Out Right Big,Fade Out Up,Fade Out Up Big,Flash,Flip,Flip In X,Flip In Y,Flip Out X,Flip Out Y,Hinge,Light Speed In,Light Speed Out,Pulse,Roll In,Roll Out,Rotate In,Rotate In Down Left,Rotate In Down Right,Rotate In Up Left,Rotate In Up Right,Rotate Out,Rotate Out Down Left,Rotate Out Down Right,Rotate Out Up Left,Rotate Out Up Right,Shake,Swing,Tada,Wobble"|gettxtlist
            values="bounce,bounceIn,bounceInDown,bounceInLeft,bounceInRight,bounceInUp,bounceOut,bounceOutDown,bounceOutLeft,bounceOutRight,bounceOutUp,fadeIn,fadeInDown,fadeInDownBig,fadeInLeft,fadeInLeftBig,fadeInRight,fadeInRightBig,fadeInUp,fadeInUpBig,fadeOut,fadeOutDown,fadeOutDownBig,fadeOutLeft,fadeOutLeftBig,fadeOutRight,fadeOutRightBig,fadeOutUp,fadeOutUpBig,flash,flip,flipInX,flipInY,flipOutX,flipOutY,hinge,lightSpeedIn,lightSpeedOut,pulse,rollIn,rollOut,rotateIn,rotateInDownLeft,rotateInDownRight,rotateInUpLeft,rotateInUpRight,rotateOut,rotateOutDownLeft,rotateOutDownRight,rotateOutUpLeft,rotateOutUpRight,shake,swing,tada,wobble"
            label="Transition Out" value=$config.anim_out|default:'fadeOut'
        }
        {control type=text name="duration" label="Animation Duration"|gettext value=$config.duration|default:5 size="5" description='in tenths of seconds'|gettext}
    {/group}

    {*<h4>{'Configure the box size of the Slideshow frame'|gettext}</h4>*}
    {*{control type=text name="pa_slideshow_width" label="Slideshow Width"|gettext value=$config.pa_slideshow_width|default:100 size="5"}*}
    {*{control type=text name="pa_slideshow_height" label="Slideshow Height"|gettext value=$config.pa_slideshow_height|default:100 size="5"}*}

    {*<h4>{'Configure the box size the Slideshow images'|gettext}</h4>*}
    {*{control type=text name="pa_image_width" label="Image Width"|gettext value=$config.pa_image_width|default:100 size="5"}*}
    {*{control type=text name="pa_image_height" label="Image Height"|gettext value=$config.pa_image_height|default:100 size="5"*}
{/group}