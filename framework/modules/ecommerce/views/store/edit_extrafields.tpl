{if $record->parent_id == 0}
	{control type="hidden" name="tab_loaded[extrafields]" value=1} 
	<h2>Extra Fields</h2> 
	You may add up to four extra fields to your product here.  These field names are also picked up by your child products where you can assign values to them.
	<table> 
		<tr>
			<td>{control type="text" name="extra_fields_name[0]" label="Extra Field Name #1:" value=$record->extra_fields.0.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[0]" label="Extra Field Value #1:"|gettext value=$record->extra_fields.0.value}</td>{/if}
		</tr>
		<tr>
			<td>{control type="text" name="extra_fields_name[1]" label="Extra Field Name #2:" value=$record->extra_fields.1.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[1]" label="Extra Field Value #2:"|gettext value=$record->extra_fields.1.value}</td>{/if}
		</tr>
		<tr>
			<td>{control type="text" name="extra_fields_name[2]" label="Extra Field Name #3:" value=$record->extra_fields.2.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[2]" label="Extra Field Value #3:"|gettext value=$record->extra_fields.2.value}</td>{/if}
		</tr>
		<tr>
			<td>{control type="text" name="extra_fields_name[3]" label="Extra Field Name #4:" value=$record->extra_fields.3.name}</td>
			{if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[3]" label="Extra Field Value #4:"|gettext value=$record->extra_fields.3.value}</td>{/if}
		</tr>
	</table>
{else}
	<h2>Extra Fields</h2>                     
	Extra field names are defined in this product's parent.  You may enter the field values for this product here. 
	<table> 
		{if $parent->extra_fields.0.name != '' }
			<tr>
				<td>
				{control type="hidden" name="extra_fields_name[0]" value=$parent->extra_fields.0.name}
				{control type="text" name="extra_fields_value[0]" label="Value for extra field - '`$parent->extra_fields.0.name`':" value=$record->extra_fields.0.value}</td>
			</tr>
			{if $parent->extra_fields.1.name != '' } 
				<tr>
				<td>
				{control type="hidden" name="extra_fields_name[1]" value=$parent->extra_fields.1.name}
				{control type="text" name="extra_fields_value[1]" label="Value for extra field - '`$parent->extra_fields.1.name`':" value=$record->extra_fields.1.value}</td>
			</tr>
			{/if}
			{if $parent->extra_fields.2.name != '' } 
				<tr>
					<td>
					{control type="hidden" name="extra_fields_name[2]" value=$parent->extra_fields.2.name}
					{control type="text" name="extra_fields_value[2]" label="Value for extra field - '`$parent->extra_fields.2.name`':" value=$record->extra_fields.2.value}</td>
				</tr>
			 {/if}
			{if $parent->extra_fields.3.name != '' } 
				 <tr>
					<td>
					{control type="hidden" name="extra_fields_name[3]" value=$parent->extra_fields.3.name}
					{control type="text" name="extra_fields_value[3]" label="Value for extra field - '`$parent->extra_fields.3.name`':" value=$record->extra_fields.3.value}</td>
				</tr>
			{/if}
		{else}
			{br}{br}<i>There are no extra fields defined for this item.</i>
		{/if} 
	</table>
{/if}