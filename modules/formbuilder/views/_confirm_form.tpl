{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

{*	<!-- 
	THIS IS PROBABLY NOT THE RIGHT PLACE TO PUT THIS LITTLE SCRIPT DEAL, BUT IT WILL WORK FOR NOW.  
	WE'RE GONNA REBUILD THE FORMBUILDER SOMEDAY ANYHOW, EH? 2.0? 

	FOR CUSTOM THEME:
    We would need that {css} plugin to work... then we could setup the config to pick colors for the following items, 
    pass it in here, and have Exponent send it to the head.
        
     .recaptchatable .recaptcha_image_cell, #recaptcha_table {
       background-color:#FF0000 !important; //reCaptcha widget background color
     }
     
     #recaptcha_table {
       border-color: #FF0000 !important; //reCaptcha widget border color
     }
     
     #recaptcha_response_field {
       border-color: #FF0000 !important; //Text input field border color
       background-color:#FF0000 !important; //Text input field background color
     }
    -->
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

<div class="formbuilder confirm-form">
	<h1>Please confirm your submission</h1>
	<table width="90%">
	<th>Field</th>
	<th>Your Response</th>
	{foreach from=$responses item=response key=name}
		<tr>
			<td><strong>{$name}: </strong>
			<td>{$response}</td>
		</tr>
	{/foreach}	
	</table>

	<p>If the information above looks correct, fill out the security question below to submit your form submission</p>
	{form action=submit_form}
		{foreach from=$postdata item=data key=name}
			{control type=hidden name=$name value=$data}
		{/foreach}
		{control type=antispam}
		{control type=buttongroup submit="Submit Form" cancel="Change Responses"}
	{/form}
</div> 
