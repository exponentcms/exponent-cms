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

{control type="hidden" name="tab_loaded[extrafields]" value=1}
{if $record->parent_id == 0}
	<h2>{'Extra Fields'|gettext}</h2>
	<blockquote>{'You may add up to four extra fields of information to your product here which will be picked up by any child products to assign values to them.  They allow you to display specific details which differentiate the child products from each other.'|gettext}</blockquote>
	<table> 
		<tr>
			<td>{control type="text" name="extra_fields_name[0]" label="Extra Field Name #1:" size=30 value=$record->extra_fields.0.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[0]" label="Extra Field Value #1:"|gettext size=30 value=$record->extra_fields.0.value}</td>{/if}
		</tr>
		<tr>
			<td>{control type="text" name="extra_fields_name[1]" label="Extra Field Name #2:" size=30 value=$record->extra_fields.1.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[1]" label="Extra Field Value #2:"|gettext size=30 value=$record->extra_fields.1.value}</td>{/if}
		</tr>
		<tr>
			<td>{control type="text" name="extra_fields_name[2]" label="Extra Field Name #3:" size=30 value=$record->extra_fields.2.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[2]" label="Extra Field Value #3:"|gettext size=30 value=$record->extra_fields.2.value}</td>{/if}
		</tr>
		<tr>
			<td>{control type="text" name="extra_fields_name[3]" label="Extra Field Name #4:" size=30 value=$record->extra_fields.3.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[3]" label="Extra Field Value #4:"|gettext size=30 value=$record->extra_fields.3.value}</td>{/if}
		</tr>
	</table>
{else}
	<h2>{'Extra Fields'|gettext}</h2>
	{'Extra field names are defined in this product\'s parent.  You may enter the field values for this product here.'|gettext}
	<table> 
		{if $parent->extra_fields.0.name != '' }
			<tr>
				<td>
				{control type="hidden" name="extra_fields_name[0]" size=30 value=$parent->extra_fields.0.name}
				{control type="text" name="extra_fields_value[0]" label="Value for extra field - '`$parent->extra_fields.0.name`':" size=30 value=$record->extra_fields.0.value}</td>
			</tr>
			{if $parent->extra_fields.1.name != '' } 
				<tr>
				<td>
				{control type="hidden" name="extra_fields_name[1]" size=30 value=$parent->extra_fields.1.name}
				{control type="text" name="extra_fields_value[1]" label="Value for extra field - '`$parent->extra_fields.1.name`':" size=30 value=$record->extra_fields.1.value}</td>
			</tr>
			{/if}
			{if $parent->extra_fields.2.name != '' } 
				<tr>
					<td>
					{control type="hidden" name="extra_fields_name[2]" size=30 value=$parent->extra_fields.2.name}
					{control type="text" name="extra_fields_value[2]" label="Value for extra field - '`$parent->extra_fields.2.name`':" size=30 value=$record->extra_fields.2.value}</td>
				</tr>
			 {/if}
			{if $parent->extra_fields.3.name != '' } 
				 <tr>
					<td>
					{control type="hidden" name="extra_fields_name[3]" size=30 value=$parent->extra_fields.3.name}
					{control type="text" name="extra_fields_value[3]" label="Value for extra field - '`$parent->extra_fields.3.name`':" size=30 value=$record->extra_fields.3.value}</td>
				</tr>
			{/if}
		{else}
			{br}{br}<em>{'There are no extra fields defined for this item.'|gettext}</em>
		{/if} 
	</table>
{/if}