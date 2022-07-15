{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

<div class="module report dashboard row">
    {exp_include file='menu.tpl'}

	<div id="right" class="rightcol col-sm-8">
	    <div id="dashboard-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'New Orders'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Top Selling Items'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{"Most Viewed'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><em>{'Customers'|gettext}</em></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="exp-ecom-table tab-pane fade in active">
                    {control type="dropdown" name="filter" label="Range"|gettext|cat:": " items=$quickrange}
                    <table>
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
                <div id="tab2" role="tabpanel">
                </div>
                <div id="tab3" role="tabpanel">
                </div>
                <div id="tab4" role="tabpanel">
                </div>
                <div id="tab5" role="tabpanel">
                </div>
            </div>
	    </div>
        {loading title='Loading Dashboard'|gettext}
    </div>
    {clear}
</div>
