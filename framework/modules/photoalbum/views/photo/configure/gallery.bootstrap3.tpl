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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Photo Album Settings"|gettext) module="photo"}
		</div>
        <h2>{"Photo Album Settings"|gettext}</h2>
        <blockquote>
            {"This is where you can configure the settings used by this Photo Album module."|gettext}&#160;&#160;
            {"These settings only apply to this particular module."|gettext}
        </blockquote>
	</div>
</div>
{group label="Gallery Page"|gettext}
    {control type=dropdown name=order label="Sort By"|gettext items="Order Manually, Random"|gettxtlist values="rank,RAND()" value=$config.order|default:rank focus=1}
    {control type=text name="pa_showall_thumbbox" label="Box size for image thumbnails"|gettext value=$config.pa_showall_thumbbox|default:100 size="5"}
    {control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
    {control type="checkbox" name="lightbox" label="Use lightbox effect"|gettext value=1 checked=$config.lightbox}
    {if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
        {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
    {else}
        {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
    {/if}
    <div id="alt-control-landing" class="alt-control">
        <div class="control"><label class="label">{'Display Gallery pages as:'|gettext}</label></div>
        <div class="alt-body">
            {control type=radiogroup columns=2 name="landing" items="Gallery,Slideshow"|gettxtlist values="showall,slideshow" default=$config.landing|default:"showall"}
            <div id="showall-div" class="alt-item" style="display:none;">
                <quote>{'There are no gallery settings'|gettext}</quote>
            </div>
            <div id="slideshow-div" class="alt-item" style="display:none;">
                {group label='Slideshow View Settings'|gettext}
                    {control type=text name="width" label="Slideshow Width"|gettext value=$config.width|default:350 size="5"}
                    {control type=text name="height" label="Slideshow Height"|gettext value=$config.height|default:200 size="5"}
                    {control type=text name="speed" label="Seconds per slide"|gettext value=$config.speed|default:5 size="5"}
                    {*{control type=text name="quality" label="Slide Thumbnail JPEG Quality"|gettext|cat:" (0 - 95, 100)<br><small>"|cat:("If quality is set to 100, the raw image will be used instead of thumbnailing"|gettext)|cat:"</small>" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}*}
                    {control type=checkbox name="hidetext" label="Hide Title and Description text in slides"|gettext checked=$config.hidetext value=1}
                    {*{control type="checkbox" name="hidecontrols" label="Hide controls"|gettext checked=$config.hidecontrols|default:0 value=1}*}
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
                        {*{control type=text name="duration" label="Animation Duration"|gettext value=$config.duration|default:5 size="5" description='in tenths of seconds'|gettext}*}
                    {/group}
                {/group}
            </div>
        </div>
    </div>
{/group}
{group label="Detail Page or Lightbox"|gettext}
    {control type=text name="pa_showall_enlarged" label="Box size for enlarged images"|gettext value=$config.pa_showall_enlarged|default:300 size="5"}
    {control type="dropdown" name="pa_float_enlarged" label="Float enlarged image"|gettext items="No Float,Left,Right"|gettxtlist values="No Float,Left,Right" value=$config.pa_float_enlarged}
{/group}

{script unique="landing-type" jquery=1}
{literal}
$(document).ready(function(){
    var radioSwitcher_landing = $('#alt-control-landing input[type="radio"]');
    radioSwitcher_landing.on('click', function(e){
        $("#alt-control-landing .alt-item").css('display', 'none');
        var curdiv = $("#" + e.target.value + "-div");
        curdiv.css('display', 'block');
    });

    radioSwitcher_landing.each(function(k, node){
        if(node.checked == true){
            $(node).trigger('click');
        }
    });
});
{/literal}
{/script}
