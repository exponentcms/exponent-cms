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

<div class="module order address edit address-form">
    <div id="editbilling">
        {form action=save_payment_info}
            {control type="hidden" name="id" value=$orderid}
            <div id="editbilling-tabs" class="yui-navset exp-skin-tabview hide">
                <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'Edit Payment Info'|gettext}</em></a></li>
                </ul>
                <div class="yui-content">
                    <div id="tab1">
                        {foreach from=$opts item=field key=key}
                            {control type="text" name="result[`$key`]" label=$key value=$field}
                        {/foreach}
                        {control type="buttongroup" submit="Save Payment Info"|gettext cancel="Cancel"|gettext}

                    </div>
                </div>
            </div>
            <div class="loadingdiv">{'Loading'|gettext}</div>
        {/form}
    </div>
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#editbilling-tabs'});
		Y.one('#editbilling-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
