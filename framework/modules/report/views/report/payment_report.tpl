{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}

<div class="module report build-report">
    <div id="report-form" class="exp-ecom-table">    
    <table border="0" width="50%" align="center" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th colspan="2">
                    <h1>{"Payment Report"|gettext}</h1>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">   
                  <td>
                    {'Visa'|gettext}
                </td>
                <td>
                    {'val'|gettext}
                </td>
            </tr>
            <tr class="odd">   
                  <td>
                    {'MasterCard'|gettext}
                </td>
                <td>
                    {'val'|gettext}
                </td>
            </tr>
            <tr class="even">   
                  <td>
                    {'American Express'|gettext}
                </td>
                <td>
                    {'val'|gettext}
                </td>
            </tr>
            <tr class="odd">   
                  <td>
                    {'Discover'|gettext}
                </td>
                <td>
                    {'val'|gettext}
                </td>
            </tr>
             <tr class="even">   
                  <td>
                    {'PayPal'|gettext}
                </td>
                <td>
                    {'val'|gettext}
                </td>
            </tr>
            <tr class="odd">   
                  <td>
                    {'Total'|gettext}
                </td>
                <td>
                    {'val'|gettext}
                </td>
            </tr>
        </tbody>
    </table>
    
    </div>
</div>
