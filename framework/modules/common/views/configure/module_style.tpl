{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Module Style"|gettext) module="module-style"}
		</div>
        <h2>{"Module Style Settings"|gettext}</h2>
	</div>
	<blockquote>
     {'These settings allow you to emphasize a module or set it\'s visibility.'|gettext}&#160;&#160;
     {'The specific styles applied, if any are fully dependent on the theme\'s styling implementation!'|gettext}
 </blockquote>
</div>
{control type=radiogroup name="style[border]" label="Module Borders"|gettext value=$config.style.border|default:0 items="None,Top,Bottom,Top and Bottom,Box"|gettxtlist values="0,top,bottom,topbottom,box"}
{if bs()}
{control type=checkbox name="style[styled]" label="Module Emphasis" value=1 checked=$config.style.styled description='Add \'well\' styling'|gettext}
{/if}
{control type=radiogroup name="style[background]" label="Module Background"|gettext value=$config.style.background|default:0 items="None,Light,Medium,Dark"|gettxtlist values="0,light,medium,dark"}
{if bs2()}
{group label='Module Visibility - Hidden'|gettext}
	{control type=checkbox name="style[hiddensm]" label="Hidden on Phone" value=1 checked=$config.style.hiddensm}
	{control type=checkbox name="style[hiddenmd]" label="Hidden on Tablet" value=1 checked=$config.style.hiddenmd}
	{control type=checkbox name="style[hiddenlg]" label="Hidden on Desktop" value=1 checked=$config.style.hiddenlg}
{/group}
{group label='Module Visibility - Visible'|gettext}
	{control type=checkbox name="style[visiblesm]" label="Visible on Phone" value=1 checked=$config.style.visiblesm}
	{control type=checkbox name="style[visiblemd]" label="Visible on Tablet" value=1 checked=$config.style.visiblemd}
	{control type=checkbox name="style[visiblelg]" label="Visible on Desktop" value=1 checked=$config.style.visiblelg}
{/group}
{/if}
{if bs3()}
{group label='Module Visibility - Hidden'|gettext}
	{control type=checkbox name="style[hiddenxs]" label="Hidden on Extra Small displays" value=1 checked=$config.style.hiddenxs}
	{control type=checkbox name="style[hiddensm]" label="Hidden on Small displays" value=1 checked=$config.style.hiddensm}
	{control type=checkbox name="style[hiddenmd]" label="Hidden on Medium displays" value=1 checked=$config.style.hiddenmd}
	{control type=checkbox name="style[hiddenlg]" label="Hidden on Large displays" value=1 checked=$config.style.hiddenlg}
{/group}
{group label='Module Visibility - Visible'|gettext}
	{control type=checkbox name="style[visiblexs]" label="Visible on Extra Small displays" value=1 checked=$config.style.visiblexs}
	{control type=checkbox name="style[visiblesm]" label="Visible on Small displays" value=1 checked=$config.style.visiblesm}
	{control type=checkbox name="style[visiblemd]" label="Visible on Medium displays" value=1 checked=$config.style.visiblemd}
	{control type=checkbox name="style[visiblelg]" label="Visible on Large displays" value=1 checked=$config.style.visiblelg}
{/group}
{/if}