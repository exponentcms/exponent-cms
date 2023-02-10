{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

<div class="module navigation move_standalonepage">
    <div class="form_header">
        <div class="info-header">
            <div class="related-actions">
                {help text="Get Help with"|gettext|cat:" "|cat:("Moving Standalone Pages"|gettext) module="move-standalone-page"}
            </div>
            <h2>{'Move Standalone Page'|gettext}</h2>
            <blockquote>{'Select the standalone page you wish to move into the Site Hierarchy, and click \'Save\''|gettext}</blockquote>
        </div>
	</div>

    {form action=reparent_standalone}
        {control type=hidden name=parent value=$parent}
        <div id="configure-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-toggle="tab"><em>{'Page'|gettext}</em></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                    {control type="checkbox" name="new_window" label="Open in New Window"|gettext|cat:"?" checked=$section->new_window value=1}
                    {control type="dropdown" name="page" label="Standalone Page"|gettext items=section::levelDropdownControlArray(-1,0,array(),false,'manage') value=$page}
                </div>
            </div>
        </div>
        {loading title='Loading Pages'|gettext}
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
