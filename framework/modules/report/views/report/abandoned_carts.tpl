{include file='menu.inc'}
{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}
	<div class="rightcol">

        <div id="dashboard-tabs" class="yui-navset yui3-skin-sam hide">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>New Orders</em></a></li>
                    <!--li><a href="#tab2"><em>Top Selling Items</em></a></li>
                    <li><a href="#tab3"><em>Most Viewed</em></a></li>
                    <li><a href="#tab4"><em>Customers</em></a></li-->
                </ul>            
                <div class="yui-content">      
                    <div id="tab1" class="exp-ecom-table">                                      
                    <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>
                                    <a href="#">{"Order Type"|gettext}</a>
                                    </th>
                                    <th>
                                    <a href="#">{"Order Status"|gettext}</a>
                                    </th>
                                    <th>
                                    <a href="#">{"# of Orders"|gettext}</a>
                                    </th>
                                    <th>
                                    <a href="#">{"# of Items"|gettext}</a>
                                    </th>
                                    <th style="text-align:right;">
                                    <a href="#">{"Total"|gettext}</a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$orders item=order key=tkey name=typeloop}
                                <tr class="{cycle values="even,odd"}" style="font-weight:bold; font-size:120%">
                                    <td>{$tkey}</td>
                                    <td>&nbsp;</td>
                                    <td>{$order.num_orders}</td>
                                    <td>{$order.num_items}</td>
                                    <td style="text-align:right;">${$order.grand_total|number_format:2}</td>
                                </tr>
                                    {foreach from=$order item=stat key=skey name=typeloop}
                                    {if $skey != 'num_orders' && $skey!= 'num_items' && $skey != 'grand_total'}
                                        <tr class="{cycle values="even,odd"}" style="color:grey;">
                                            <td>&nbsp;</td>
                                            <td>{$skey}</td>
                                            <td>{$stat.num_orders}</td>
                                            <td>{$stat.num_items}</td>
                                            <td style="text-align:right;">${$stat.grand_total|number_format:2}</td>    
                                        </tr>
                                    {/if}
                                    {/foreach}
                                {/foreach}
                            <tbody>
                        </table>
                        <table>
                        <tr>                            
                            <td>
                            {form action="abandoned_carts"}
                            {"Quick Range Filter:"|gettext}{br}
                            {control type="dropdown" name="quickrange" label="" items=$quickrange default=$quickrange_default onchange="this.form.submit();"}      
                            {/form}
                            </td>
                            <td>{form action="abandoned_carts"}   
                            {"Purchased Between:"|gettext}{br}
                            {control type="calendar" name="starttime" label="" default_date=$prev_month default_hour=$prev_hour default_min=$prev_min default_ampm=$prev_ampm}{br}                
                            {"And"|gettext}{br}
                            {control type="calendar" name="endtime" label="" default_date=$now_date default_hour=$now_hour default_min=$now_min default_ampm=$now_ampm}    {br}
                            {control type="submit" name="submit" value="Apply Filter"}
                            {/form} 
                            </td>
                        </tr>                    
                    </table>
                    </div>
                    <!--div id="tab2">
                    </div>          
                    <div id="tab3">
                    </div>
                    <div id="tab4">
                    </div>
                    <div id="tab5">
                    </div-->
                </div>
            <div class="loadingdiv">{"Loading Dashboard"|gettext}</div>
        </div>
    </div>
    <div style="clear:both"></div>
</div>

{script unique="editform" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('dashboard-tabview');
//        Y.one('#dashboard-tabs').removeClass('hide').next().remove();
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
		var tabview = new Y.TabView({srcNode:'#dashboard-tabs'});
		tabview.render();
		Y.one('#dashboard-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}

