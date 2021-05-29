{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

<div class="module order address edit address-form">
    <div id="editbilling">
        {form action=save_payment_info}
            {control type="hidden" name="id" value=$orderid}
            <div id="editbilling-tabs" class="">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-bs-toggle="tab"><em>{'Edit Payment Info'|gettext}</em></a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                        {foreach from=$opts item=field key=key}
                            {control type="text" name="result[`$key`]" label=$key value=$field}
                        {/foreach}
                        {control type="buttongroup" submit="Save Payment Info"|gettext cancel="Cancel"|gettext}

                    </div>
                </div>
            </div>
            {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
            {loading}
        {/form}
    </div>
</div>

{script unique="tabload" jquery=1 bootstrap="tab"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}