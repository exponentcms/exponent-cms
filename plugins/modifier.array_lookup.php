<?php
function smarty_modifier_array_lookup($value='', $from=array(), $index=0)
{
if (array_key_exists($value, $from)) {
return $from[$value][$index];
}
return '';
}
?>