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

{if $record->parent_id == 0}
    {control type="hidden" name="tab_loaded[userinput]" value=1}
    {if count($record->childProduct)}
		<h4><em>({'Child products inherit these settings.'|gettext})</em></h4>
    {/if}
	<h2>{'User Input'|gettext}</h2>
	<blockquote>
        {'You may define fields here that the user is required to fill out when purchasing this product.  For instance, to supply a value to be imprinted on an item.'|gettext}{br}
        {'If a product option is set for \'User Input\', this will only be displayed if the customer selects that/those option(s).'|gettext}
    </blockquote>
    {group label="User Field 1"|gettext}
	{control class="userInputToggle" type="checkbox" name="user_input_use[0]" id=userinput1 label="Show Field"|gettext value=1 checked=$record->user_input_fields.0.use}
	<div id="input1">
		<table>
			<tr>
				<td>
					{control type="text" name="user_input_name[0]" label="Field Name"|gettext value=$record->user_input_fields.0.name}
				</td>
				<td>
					{control type="checkbox" name="user_input_is_required[0]" label="Required?"|gettext value=1 checked=$record->user_input_fields.0.is_required}
				</td>
			</tr>
			<tr>
				<td>
					{control type="text" name="user_input_min_length[0]" label="Minimum Length"|gettext size=5 value=$record->user_input_fields.0.min_length}
				</td>
				<td>
					{control type="text" name="user_input_max_length[0]" label="Maximum Length"|gettext size=5 value=$record->user_input_fields.0.max_length}
				</td>
			</tr>
			<tr>
				<td colspan=2>
					{control type="textarea" name="user_input_description[0]" label="Description For Users"|gettext height=200 value=$record->user_input_fields.0.description}
				</td> 
			</tr>
		</table>
	</div>
    {/group}
    {group label="User Field 2"|gettext}
	{control class="userInputToggle" type="checkbox" name="user_input_use[1]" id=userinput2 label="Show Field"|gettext value=1 checked=$record->user_input_fields.1.use}
	<div id="input2">
		<table>
			<tr>
				<td>
					{control type="text" name="user_input_name[1]" label="Field Name"|gettext value=$record->user_input_fields.1.name}
				</td>
				<td>
					{control type="checkbox" name="user_input_is_required[1]" label="Required?"|gettext value=1 checked=$record->user_input_fields.1.is_required}
				</td>
			</tr>
			<tr>
				<td>
                    {control type="text" name="user_input_min_length[1]" label="Minimum Length"|gettext size=5 value=$record->user_input_fields.1.min_length}
				</td>
				<td>
					{control type="text" name="user_input_max_length[1]" label="Maximum Length"|gettext size=5 value=$record->user_input_fields.1.max_length}
				</td>
			</tr>
			<tr>
				<td colspan=2>
					{control type="textarea" name="user_input_description[1]" label="Description For Users"|gettext height=200 value=$record->user_input_fields.1.description}
				</td> 
			</tr>
		</table>
	</div>
    {/group}
    {group label="User Field 3"|gettext}
	{control class="userInputToggle" type="checkbox" name="user_input_use[2]" id=userinput3 label="Show Field"|gettext value=1 checked=$record->user_input_fields.2.use}
	<div id="input3">
		<table>
			<tr>
				<td>
					{control type="text" name="user_input_name[2]" label="Field Name"|gettext value=$record->user_input_fields.2.name}
				</td>
				<td>
					{control type="checkbox" name="user_input_is_required[2]" label="Required?"|gettext value=1 checked=$record->user_input_fields.2.is_required}
				</td>
			</tr>
			<tr>
				<td>
					{control type="text" name="user_input_min_length[2]" label="Minimum Length"|gettext size=5 value=$record->user_input_fields.2.min_length}
				</td>
				<td>
					{control type="text" name="user_input_max_length[2]" label="Maximum Length"|gettext size=5 value=$record->user_input_fields.2.max_length}
				</td>
			</tr>
			<tr>
				<td colspan=2>
					{control type="textarea" name="user_input_description[2]" label="Description For Users"|gettext height=200 value=$record->user_input_fields.2.description}
				</td> 
			</tr>
		</table>
	</div>
    {/group}
    {group label="User Field 4"|gettext}
	{control class="userInputToggle" type="checkbox" name="user_input_use[3]" id=userinput4 label="Show Field"|gettext value=1 checked=$record->user_input_fields.3.use}
	<div id="input4">
		<table>
			<tr>
				<td>
					{control type="text" name="user_input_name[3]" label="Field Name"|gettext value=$record->user_input_fields.3.name}
				</td>
				<td>
					{control type="checkbox" name="user_input_is_required[3]" label="Required?"|gettext value=1 checked=$record->user_input_fields.3.is_required}
				</td>
			</tr>
			<tr>
				<td>
					{control type="text" name="user_input_min_length[3]" label="Minimum Length"|gettext size=5 value=$record->user_input_fields.3.min_length}
				</td>
				<td>
					{control type="text" name="user_input_max_length[3]" label="Maximum Length"|gettext size=5 value=$record->user_input_fields.3.max_length}
				</td>
			</tr>
			<tr>
				<td colspan=2>
					{control type="textarea" name="user_input_description[3]" label="Description For Users"|gettext height=200 value=$record->user_input_fields.3.description}
				</td> 
			</tr>
		</table>
	</div>
    {/group}
    {group label="User Field 5"|gettext}
	{control class="userInputToggle" type="checkbox" name="user_input_use[4]" id=userinput5 label="Show Field"|gettext value=1 checked=$record->user_input_fields.4.use}
	<div id="input5">
		<table>
			<tr>
				<td>
					{control type="text" name="user_input_name[4]" label="Field Name"|gettext value=$record->user_input_fields.4.name}
				</td>
				<td>
					{control type="checkbox" name="user_input_is_required[4]" label="Required?"|gettext value=1 checked=$record->user_input_fields.4.is_required}
				</td>
			</tr>
			<tr>
				<td>
					{control type="text" name="user_input_min_length[4]" label="Minimum Length"|gettext size=5 value=$record->user_input_fields.4.min_length}
				</td>
				<td>
					{control type="text" name="user_input_max_length[4]" label="Maximum Length"|gettext size=5 value=$record->user_input_fields.4.max_length}
				</td>
			</tr>
			<tr>
				<td colspan=2>
					{control type="textarea" name="user_input_description[4]" label="Description For Users"|gettext height=200 value=$record->user_input_fields.4.description}
				</td> 
			</tr>
		</table>
	</div>
    {/group}
    {group label="User Field 6"|gettext}
	{control class="userInputToggle" type="checkbox" name="user_input_use[5]" id=userinput6 label="Show Field"|gettext value=1 checked=$record->user_input_fields.5.use}
	<div id="input6">
		<table>
			<tr>
				<td>
					{control type="text" name="user_input_name[5]" label="Field Name"|gettext value=$record->user_input_fields.5.name}
				</td>
				<td>
					{control type="checkbox" name="user_input_is_required[5]" label="Required?"|gettext value=1 checked=$record->user_input_fields.5.is_required}
				</td>
			</tr>
			<tr>
				<td>
					{control type="text" name="user_input_min_length[5]" label="Minimum Length"|gettext size=5 value=$record->user_input_fields.5.min_length}
				</td>
				<td>
					{control type="text" name="user_input_max_length[5]" label="Maximum Length"|gettext size=5 value=$record->user_input_fields.5.max_length}
				</td>
			</tr>
			<tr>
				<td colspan=2>
					{control type="textarea" name="user_input_description[5]" label="Description For Users"|gettext height=200 value=$record->user_input_fields.5.description}
				</td> 
			</tr>
		</table>
	</div>
    {/group}
{else}
	<h4><em>({'User Input Fields'|gettext} {'are inherited from this product\'s parent.'|gettext})</em></h4>
{/if}

{script unique="edituserinput" jquery=1}
{literal}
$('#userinput1').change(function() {
    if ($('#userinput1').is(':checked') == false)
        $("#input1").hide("slow");
    else {
        $("#input1").show("slow");
    }
});
if ($('#userinput1').is(':checked') == false)
    $("#input1").hide("slow");

$('#userinput2').change(function() {
    if ($('#userinput2').is(':checked') == false)
        $("#input2").hide("slow");
    else {
        $("#input2").show("slow");
    }
});
if ($('#userinput2').is(':checked') == false)
    $("#input2").hide("slow");

$('#userinput3').change(function() {
    if ($('#userinput3').is(':checked') == false)
        $("#input3").hide("slow");
    else {
        $("#input3").show("slow");
    }
});
if ($('#userinput3').is(':checked') == false)
    $("#input3").hide("slow");

$('#userinput4').change(function() {
    if ($('#userinput4').is(':checked') == false)
        $("#input4").hide("slow");
    else {
        $("#input4").show("slow");
    }
});
if ($('#userinput4').is(':checked') == false)
    $("#input4").hide("slow");

$('#userinput5').change(function() {
    if ($('#userinput5').is(':checked') == false)
        $("#input5").hide("slow");
    else {
        $("#input5").show("slow");
    }
});
if ($('#userinput5').is(':checked') == false)
    $("#input5").hide("slow");

$('#userinput6').change(function() {
    if ($('#userinput6').is(':checked') == false)
        $("#input6").hide("slow");
    else {
        $("#input6").show("slow");
    }
});
if ($('#userinput6').is(':checked') == false)
    $("#input6").hide("slow");
{/literal}
{/script}
