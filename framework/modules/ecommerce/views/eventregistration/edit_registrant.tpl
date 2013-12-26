{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module eventregistration edit">
    {if $registrant->id}
        <h1>{'Editing'|gettext} '{$event->title}' {'Registrant'|gettext}</h1>
    {else}
        <h1>{'New Registrant for'|gettext} '{$event->title}'</h1>
    {/if}

    {form action=update_registrant}
        {control type=hidden name=id value=$registrant->id}
        {control type=hidden name=event_id value=$event->id}
        {$controls = $event->getAllControls()}
        {if $controls}
            {foreach $controls as $control}
                {$ctlname = $control->name}
                {$event->showControl($control,"registrant[`$ctlname`]",null,$registrant->$ctlname)}
            {/foreach}
        {else}
            {control type="text" name="control_name" label='Name'|gettext value="`$registrant->control_name`" required=1}
            {control type="text" name="value" label='Quantity'|gettext value="`$registrant->value`" required=1}
        {/if}

        {*FIXME no longer being stored in registrant record*}
        {*{control type=text name=payment label="Paid"|gettext value=$registrant->payment}*}

        {control type=buttongroup submit="Save Registrant"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
