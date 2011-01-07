{css unique="ecom-dashboard1" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/dashboard.css"}

{/css}

{css unique="ecom-dashboard2" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}



<div class="module report dashboard">
    <div class="leftcol">
        
        <div id="quickstats" class="panel">
            <div class="hd"><h2>Quick Stats</h2><a href="#" class="collapse">Collapse</a></div>
            <div class="bd">
                <ul>
                    <li>
                        <strong><a href="#">View recent orders</a></strong>
                        View and manage all new and existing orders.
                    </li>
                </ul>
            </div>
        </div>
        
        <div id="orders" class="panel">
            <div class="hd"><h2>Orders</h2><a href="#" class="collapse">Collapse</a></div>
            <div class="bd">
                <ul>
                    <li>
                        <strong><a href="#">View recent orders</a></strong>
                        View and manage all new and existing orders.
                    </li>
                    <li>
                        <strong><a href="#">Create a Report</a></strong>
                        Create reports based on orders, products, and other information user information on your site.
                    </li>
                </ul>
            </div>
        </div>
        
        <div id="configuration" class="panel">
            <div class="hd"><h2>Store Settings</h2><a href="#" class="collapse">Collapse</a></div>
            <div class="bd">
                <ul>
                    <li>
                        <strong><a href="#">Configure General Store Settings</a></strong>
                        Set up how your site will look and act, and notify you.
                    </li>
                    <li>
                        <strong><a href="#">Manage Status Codes</a></strong>
                        Manage the labeling of each phase a completed order goes through.
                    </li>
                    <li>
                        <strong><a href="#">Manage Status Messages</a></strong>
                        Create, edit, and delete Status Messages.
                    </li>
                </ul>
            </div>
        </div>
        
        <div id="products" class="panel">
            <div class="hd"><h2>Products and Categories</h2><a href="#" class="collapse">Collapse</a></div>
            <div class="bd">
                <ul>
                    <li>
                        <strong><a href="#">Add a Product</a></strong>
                        Add a <a href="#">Product</a>, <a href="#">Donation</a>, <a href="#">Gift Card</a>, or <a href="#">Event Registration</a> to your store.
                    </li>
                    <li>
                        <strong><a href="#">Manage Products</a></strong>
                        List all products in your store and make it easy to manage them.
                    </li>
                    <li>
                        <strong><a href="#">Manage Product Options</a></strong>
                        Create options for your products, like 'large', 'small', 'red', 'greed', 'blue', etc.
                    </li>
                    <li>
                        <strong><a href="#">Manage Store Categories</a></strong>
                        Create and manage Categories in a hierarchical fashion.
                    </li>
                </ul>
            </div>
        </div>
        
        <div id="shipping" class="panel">
            <div class="hd"><h2>Billing and Shipping</h2><a href="#" class="collapse">Collapse</a></div>
            <div class="bd">
                <ul>
                    <li>
                        <strong><a href="#">Configure Billing Settings</a></strong>
                        Set up your store to use PayPal, Authorize.net, and other billing methods.
                    </li>
                    <li>
                        <strong><a href="#">Configure Shipping Information</a></strong>
                        Set up your site to use UPS, FedEx, in-store pickup and other shipping methods..
                    </li>
                </ul>
            </div>
        </div>
                
    </div
    
    <div class="rightcol">

        <div id="dashboard-tabs" class="hide exp-skin-tabview">
            {script unique="dashboard-tabs" yuimodules="tabview, element"}
            {literal}
                var tabView = new YAHOO.widget.TabView('dashboard-tabview');     
                YAHOO.util.Dom.removeClass("dashboard-tabs", 'hide');
                var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
                YAHOO.util.Dom.setStyle(loading, 'display', 'none');
            {/literal}
            {/script}

            <div id="dashboard-tabview" class="yui-navset">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>New Orders</em></a></li>
                    <li><a href="#tab2"><em>Top Selling Items</em></a></li>
                    <li><a href="#tab3"><em>Most Viewed</em></a></li>
                    <li><a href="#tab4"><em>Customers</em></a></li>
                </ul>            
                <div class="yui-content">      
                    <div id="tab1" class="exp-ecom-table">
                        {control type="dropdown" name="filter" label="Range: " items="Last 24 hours, Last 48 hours, Jurassic Period and prior"}      
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>
                                    <a href="#">{gettext str="Customer"}</a>
                                    </th>
                                    <th>
                                    <a href="#">{gettext str="Date of Purchase"}</a>
                                    </th>
                                    <th>
                                    <a href="#">{gettext str="Invoice #"}</a>
                                    </th>
                                    <th>
                                    <a href="#">{gettext str="# of Items"}</a>
                                    </th>
                                    <th>
                                    <a href="#">{gettext str="Total"}</a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="even">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                    Bill McGee
                                    </td>
                                    <td>
                                    Today - 12:08pm
                                    </td>
                                    <td>
                                    129787
                                    </td>
                                    <td>
                                    6 Items
                                    </td>
                                    <td>
                                    $186.54
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
        </div>
        <div class="loadingdiv">Loading Dashboard</div>
        
    </div>
    <div style="clear:both"></div>
</div>

{script unique="expand-panels"}
{literal}
YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node','cookie','anim', function(Y) {
        var panels = Y.all(".dashboard .panel");
        var expandHeight = [];
        var action = function(e){
            e.halt();

            var pBody = e.target.ancestor('.panel').query('.bd');
            var pID = e.target.ancestor('.panel').getAttribute('id');
            var cfg = {
                node: pBody,
                duration: 0.5,
                easing: Y.Easing.easeOut
            }
            
            if (e.target.getAttribute("class")=="collapse") {
                cfg.to = { height: 0 };
                cfg.from = { height: expandHeight[pID] };
                pBody.setStyle('height',expandHeight[pID]+"px");
                pBody.replaceClass('expanded','collapsed');
                e.target.replaceClass('collapse','expand');
                Y.Cookie.set(pID, "collapsed");
            } else {
                pBody.setStyle('height',0);
                cfg.from = { height: 0 };
                cfg.to = { height: expandHeight[pID] };
                pBody.replaceClass('collapsed','expanded');
                e.target.replaceClass('expand','collapse');
                Y.Cookie.set(pID, "expanded");
            }
            var anim = new Y.Anim(cfg);
            
            anim.run();
        }
        panels.each(function(n,k){
            n.delegate('click',action,'.hd a');
            if (Y.Cookie.get(n.get('id'))==="collapsed") {
                n.query('.hd a').replaceClass('collapse','expand');
                n.query('.bd').addClass('collapsed');
            };
            expandHeight[n.get('id')] = n.query('.bd ul').get('offsetHeight');
        });
    });
    {/literal}
{/script}