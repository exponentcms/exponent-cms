{include file='menu.inc'}

	<div class="rightcol">
	    <div id="dashboard-tabs" class="yui-navset yui3-skin-sam hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'New Orders'|gettext}</em></a></li>
                <!--li><a href="#tab2"><em>Top Selling Items</em></a></li>
                <li><a href="#tab3"><em>Most Viewed</em></a></li>
                <li><a href="#tab4"><em>Customers</em></a></li-->
            </ul>
            <div class="yui-content">
                <div id="tab1" class="exp-ecom-table">
                    {control type="dropdown" name="filter" label="Range"|gettext|cat:": " items="Last 24 hours, Last 48 hours, Jurassic Period and prior" values="Last 24 hours, Last 48 hours, Jurassic Period and prior"}
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="even">
                                <td>{'Carts Started (visits)'|gettext}
                                </td>
                                <td>{'Sessions Started (visits)'|gettext}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="dashboard-totals">
                        <strong>107 Items</strong>
                        <strong>$1,208.22</strong>
                    </div>
                </div>
                <div id="tab2">
                </div>
                <div id="tab3">
                </div>
                <div id="tab4">
                </div>
                <div id="tab5">
                </div>
            </div>
	    </div>
	    <div class="loadingdiv">{'Loading Dashboard'|gettext}</div>
    </div>
    {clear}
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

