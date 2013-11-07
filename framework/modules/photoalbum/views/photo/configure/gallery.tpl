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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Photo Album Settings"|gettext) module="photo"}
		</div>
        <h2>{"Photo Album Settings"|gettext}</h2>
	</div>
</div>
<blockquote>
    {"This is where you can configure the settings used by this Photo Album module."|gettext}&#160;&#160;
    {"These settings only apply to this particular module."|gettext}
</blockquote>
{group label="Gallery Page"|gettext}
    {control type=dropdown name=order label="Sort By"|gettext items="Order Manually, Random"|gettxtlist values="rank,RAND()" value=$config.order|default:rank}
    {control type=text name="pa_showall_thumbbox" label="Box size for image thumbnails"|gettext value=$config.pa_showall_thumbbox|default:100 size="5"}
    {control type=text name="quality" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}
    {control type="checkbox" name="lightbox" label="Use lightbox effect"|gettext value=1 checked=$config.lightbox}
    <div id="alt-control" class="alt-control">
        <div class="control"><label class="label">{'Display Gallery pages as:'|gettext}</label></div>
        <div class="alt-body">
            {control type=radiogroup columns=2 name="landing" items="Gallery,Slideshow"|gettxtlist values="showall,slideshow" default=$config.landing|default:"showall"}
            <div id="showall-div" class="alt-item" style="display:none;">
            </div>
            <div id="slideshow-div" class="alt-item" style="display:none;">
                {group label='Slideshow View Settings'|gettext}
                    {control type=text name="width" label="Slideshow Width"|gettext value=$config.width|default:350 size="5"}
                    {control type=text name="height" label="Slideshow Height"|gettext value=$config.height|default:200 size="5"}
                    {control type=text name="speed" label="Seconds per slide"|gettext value=$config.speed|default:5 size="5"}
                    {*{control type=text name="quality" label="Slide Thumbnail JPEG Quality"|gettext|cat:" (0 - 95, 100)<br><small>"|cat:("If quality is set to 100, the raw image will be used instead of thumbnailing"|gettext)|cat:"</small>" value=$config.quality|default:$smarty.const.THUMB_QUALITY size="5"}*}
                    {control type=checkbox name="hidetext" label="Hide Title and Description text in slides"|gettext checked=$config.hidetext value=1}
                    {control type="checkbox" name="hidecontrols" label="Hide controls"|gettext checked=$config.hidecontrols|default:0 value=1}
                    {control type="checkbox" name="dimcontrols" label="Dim controls"|gettext checked=$config.dimcontrols|default:0 value=1}
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
                {/group}
            </div>
        </div>
    </div>

{/group}
{group label="Detail Page or Lightbox"|gettext}
    {control type=text name="pa_showall_enlarged" label="Box size for enlarged images"|gettext value=$config.pa_showall_enlarged|default:300 size="5"}
    {control type="dropdown" name="pa_float_enlarged" label="Float enlarged image"|gettext items="No Float,Left,Right"|gettxtlist values="No Float,Left,Right" value=$config.pa_float_enlarged}
{/group}

{script unique="landing-type" yui3mods="node,node-event-simulate"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate', function(Y) {
    var radioSwitchers = Y.all('#alt-control input[type="radio"]');
    radioSwitchers.on('click',function(e){
        Y.all(".alt-item").setStyle('display','none');
        var curdiv = Y.one("#" + e.target.get('value') + "-div");
        curdiv.setStyle('display','block');
    });

    radioSwitchers.each(function(node,k){
        if(node.get('checked')==true){
            node.simulate('click');
        }
    });
});
{/literal}
{/script}