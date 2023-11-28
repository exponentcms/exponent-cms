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

{if (bs3() || bs4() || bs5())}
    {$label_class = "control-label"}
{else}
    {$label_class = "label"}
{/if}
<div class="control form-group col-sm-12">
<label class="{$label_class}" for="recur">{'Recurrence'|gettext}:</label>
<select class="form-control form-select" id="recur" name="recur" onchange="showSubform(this)">
	<option value="recur_none">{'None'|gettext}</option>
	<option value="recur_daily">{'Daily'|gettext}</option>
	<option value="recur_weekly">{'Weekly'|gettext}</option>
    <option value="recur_monthly">{'Monthly'|gettext}</option>
	<option value="recur_yearly">{'Yearly'|gettext}</option>
</select>
</div>
<div id="recur_daily" style="display: none">
	{'Every'|gettext} <input class="form-control" type="number" size="2" name="recur_freq_recur_daily" maxlength="2" value="1" /> {'day(s)'|gettext}
</div>

<div id="recur_weekly" style="display: none">
	{'Every'|gettext} <input class="form-control" type="number" size="2" name="recur_freq_recur_weekly" maxlength="2" value="1" /> {'week(s) on'|gettext}
	<table style="display:inline;" rules="all">
		<tr>
			<td id="day_0" align="center">
				S<br />
				<input name="day[0]" type="checkbox" />
			</td>
			<td id="day_1" align="center">
				M<br />
				<input name="day[1]" type="checkbox" />
			</td>
			<td id="day_2" align="center">
				T<br />
				<input name="day[2]" type="checkbox" />
			</td>
			<td id="day_3" align="center">
				W<br />
				<input name="day[3]" type="checkbox" />
			</td>
			<td id="day_4" align="center">
				T<br />
				<input name="day[4]" type="checkbox" />
			</td>
			<td id="day_5" align="center">
				F<br />
				<input name="day[5]" type="checkbox" />
			</td>
			<td id="day_6" align="center">
				S<br />
				<input name="day[6]" type="checkbox" />
			</td>
		</tr>
	</table>
</div>

<div id="recur_monthly" style="display: none">
	{'Every'|gettext} <input class="form-control" type="number" size="2" name="recur_freq_recur_monthly" maxlength="2" value="1" /> {'month(s)'|gettext}
	<input type="radio" name="month_type" value="1" checked="1" /> {'By Day'|gettext}
	<input type="radio" name="month_type" value="0" /> {'By Date'|gettext}
</div>

<div id="recur_yearly" style="display: none">
	{'Every'|gettext} <input class="form-control" type="number" size="2" name="recur_freq_recur_yearly" maxlength="2" value="1" /> {'year(s)'|gettext}
</div>

<div id="until_date" style="display: none">
  {if empty($record->eventdate->date)}
        {$until = $smarty.now}
    {else}
        {$until = $record->eventdate->date}
    {/if}
    {control type=yuicalendarcontrol name=untildate label='Until'|gettext value=$until+365*86400 showtime=false}
    {* FIXME yuicalendarcontrol does NOT display time *}
</div>

{script unique="recurring"}
{literal}
    var last = "recur_none";

    function showSubform(sel) {
        var id = sel.options[sel.selectedIndex].value;

        if (last != "recur_none") {
            var lastElem = document.getElementById(last);
            lastElem.style.display = "none";
        }

        var until_date = document.getElementById("until_date");

        if (id != "recur_none") {
            var elem = document.getElementById(id);
            elem.style.display = "block";
            until_date.style.display = "block";
        } else {
            until_date.style.display = "none";
        }

        last = id;
    }
{/literal}
{/script}