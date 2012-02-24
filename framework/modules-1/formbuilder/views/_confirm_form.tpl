{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{if $recaptcha_theme !=""}
    {literal}
        <script>
        var RecaptchaOptions = {
           theme : {/literal}'{$recaptcha_theme}'{literal}
        };
        </script>
    {/literal}
{/if}

{css unique="form-records" corecss="tables"}

{/css}

<div class="formbuilder confirm-form">
	<h1>{'Please confirm your submission'|gettext}</h1>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="exp-skin-table">
	    <thead>
        	<th>{'Field'|gettext}</th>
        	<th>{'Your Response'|gettext}</th>
        </thead>
        <tbody>
    	{foreach from=$responses item=response key=name}
    		<tr class="{cycle values="odd,even"}">
    			<td><strong>{$name}: </strong>
    			<td>{$response}</td>
    		</tr>
    	{/foreach}
		</tbody>
	</table>

	{form action=submit_form}
		{foreach from=$postdata item=data key=name}
			{control type=hidden name=$name value=$data}
		{/foreach}
		{control type=antispam}
		{control type=buttongroup submit="Submit Form"|gettext cancel="Change Responses"|gettext}
	{/form}
	
</div> 
