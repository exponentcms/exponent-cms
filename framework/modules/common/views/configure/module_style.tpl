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
{control type=radiogroup name="mstyle[border]" label="Module Borders"|gettext value=$config.mstyle.border|default:0 items="None,Top,Bottom,Top and Bottom,Box"|gettxtlist values="0,top,bottom,topbottom,box"}
{if bs()}
{control type=checkbox name="mstyle[styled]" label="Module Emphasis"|gettext value=1 checked=$config.mstyle.styled description='Add \'well\' styling'|gettext}
{/if}
{control type=radiogroup name="mstyle[background]" label="Module Background"|gettext value=$config.mstyle.background|default:0 items="None,Light,Medium,Dark"|gettxtlist values="0,light,medium,dark"}
{if bs2()}
{group label='Module Visibility - Hidden'|gettext}
	{control type=checkbox name="mstyle[hiddensm]" label="Hidden on Phone"|gettext value=1 checked=$config.mstyle.hiddensm}
	{control type=checkbox name="mstyle[hiddenmd]" label="Hidden on Tablet"|gettext value=1 checked=$config.mstyle.hiddenmd}
	{control type=checkbox name="mstyle[hiddenlg]" label="Hidden on Desktop"|gettext value=1 checked=$config.mstyle.hiddenlg}
{/group}
{group label='Module Visibility - Visible'|gettext}
	{control type=checkbox name="mstyle[visiblesm]" label="Visible on Phone"|gettext value=1 checked=$config.mstyle.visiblesm}
	{control type=checkbox name="mstyle[visiblemd]" label="Visible on Tablet"|gettext value=1 checked=$config.mstyle.visiblemd}
	{control type=checkbox name="mstyle[visiblelg]" label="Visible on Desktop"|gettext value=1 checked=$config.mstyle.visiblelg}
{/group}
{/if}
{if bs3()}
{group label='Module Visibility - Hidden'|gettext}
	{control type=checkbox name="mstyle[hiddenxs]" label="Hidden on Extra Small displays"|gettext value=1 checked=$config.mstyle.hiddenxs}
	{control type=checkbox name="mstyle[hiddensm]" label="Hidden on Small displays"|gettext value=1 checked=$config.mstyle.hiddensm}
	{control type=checkbox name="mstyle[hiddenmd]" label="Hidden on Medium displays"|gettext value=1 checked=$config.mstyle.hiddenmd}
	{control type=checkbox name="mstyle[hiddenlg]" label="Hidden on Large displays"|gettext value=1 checked=$config.mstyle.hiddenlg}
{/group}
{group label='Module Visibility - Visible'|gettext}
	{control type=checkbox name="mstyle[visiblexs]" label="Visible on Extra Small displays"|gettext value=1 checked=$config.mstyle.visiblexs}
	{control type=checkbox name="mstyle[visiblesm]" label="Visible on Small displays"|gettext value=1 checked=$config.mstyle.visiblesm}
	{control type=checkbox name="mstyle[visiblemd]" label="Visible on Medium displays"|gettext value=1 checked=$config.mstyle.visiblemd}
	{control type=checkbox name="mstyle[visiblelg]" label="Visible on Large displays"|gettext value=1 checked=$config.mstyle.visiblelg}
{/group}
{/if}