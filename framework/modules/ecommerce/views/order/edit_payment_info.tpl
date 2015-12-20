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

<div class="module order edit">
    <div id="editpayment">
        {form action=save_payment_info}
            {control type="hidden" name="id" value=$orderid}
            <div id="editpayment-tabs" class="yui-navset exp-skin-tabview hide">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>{'Edit Payment Info'|gettext}</em></a></li>
                </ul>
                <div class="yui-content">
                    <div id="tab1">
                        {control type="text" name="result[transId]" label="Payment Reference #"|gettext value=$opts->result->transId focus=1}
                        {control type="text" name="billing_cost" label='Transaction Cost'|gettext value=$billing_cost description='Refunds are negative amounts'|gettext}
                        {control type="dropdown" name="transaction_state" label="Transaction State"|gettext items="Authorization Pending,Authorized,Complete,Voided,Refunded,Payment Due,Paid,Error"|gettxtlist values="authorization pending,authorized,complete,voided,refunded,payment due,paid,error"|gettxtlist default=$transaction_state}
                        {group label="Payment Response"|gettext}
                        {foreach from=$opts->result item=field key=key}
                            {if $key != 'transId'}
                                {control type="text" name="result[`$key`]" label=$key|replace:"_":" "|ucwords value=$field}
                            {/if}
                        {foreachelse}
                            {'None'|gettext}
                        {/foreach}
                        {/group}
                        {foreach from=$opts item=field key=key}
                            {if !is_array($field) && !is_object($field)}
                                {control type="text" name="`$key`" label=$key|replace:"_":" "|ucwords value=$field}
                            {/if}
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

{script unique="editform" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#editpayment-tabs'});
		Y.one('#editpayment-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
