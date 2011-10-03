{control type="hidden" name="tab_loaded[extrafields]" value=1} 
<h2>Extra Fields</h2> 
You may add up to four extra fields to your product here.  These field names are also picked up by your child products where you can assign values to them.
<table> 
    <tr>
        <td>{control type="text" name="extra_fields_name[0]" label="Extra Field Name #1:" value=$record->extra_fields.0.name}</td>
        {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[0]" label="Extra Field Value #1:" value=$record->extra_fields.0.value}</td>{/if}
    </tr>
    <tr>
        <td>{control type="text" name="extra_fields_name[1]" label="Extra Field Name #2:" value=$record->extra_fields.1.name}</td>
        {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[1]" label="Extra Field Value #2:" value=$record->extra_fields.1.value}</td>{/if}
    </tr>
    <tr>
        <td>{control type="text" name="extra_fields_name[2]" label="Extra Field Name #3:" value=$record->extra_fields.2.name}</td>
        {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[2]" label="Extra Field Value #3:" value=$record->extra_fields.2.value}</td>{/if}
    </tr>
    <tr>
        <td>{control type="text" name="extra_fields_name[3]" label="Extra Field Name #4:" value=$record->extra_fields.3.name}</td>
        {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[3]" label="Extra Field Value #4:" value=$record->extra_fields.3.value}</td>{/if}
    </tr>
</table>
