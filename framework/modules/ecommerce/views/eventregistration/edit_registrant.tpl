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
        {foreach $controls as $control}
            {$ctlname = $control->name}
            {$event->getControl($control,"registrant[`$ctlname`]",null,$registrant->$ctlname)}
        {/foreach}

        {*FIXME no longer being stored in registrant record*}
        {*{control type=text name=payment label="Paid"|gettext value=$registrant->payment}*}

        {control type=buttongroup submit="Save Registrant"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
