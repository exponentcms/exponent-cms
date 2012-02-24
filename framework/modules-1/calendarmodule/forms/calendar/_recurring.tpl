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

<select id="recur" name="recur" onchange="showSubform(this)">
	<option value="recur_none">{'None'|gettext}</option>
	<option value="recur_daily">{'Daily'|gettext}</option>
	<option value="recur_weekly">{'Weekly'|gettext}</option>
    <option value="recur_monthly">{'Monthly'|gettext}</option>
	<option value="recur_yearly">{'Yearly'|gettext}</option>
</select>

<div id="recur_daily" style="display: none">
	{'Every'|gettext} <input type="text" size="2" name="recur_freq_recur_daily" maxlength="2" value="1" /> {'day(s)'|gettext}
</div>

<div id="recur_weekly" style="display: none">
	{'Every'|gettext} <input type="text" size="2" name="recur_freq_recur_weekly" maxlength="2" value="1" /> {'week(s)'|gettext}
	
	<table cellspacing="0" cellpadding="0" border="0" style="border: 1px solid black" rules="all">
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
	{'Every'|gettext} <input type="text" size="2" name="recur_freq_recur_monthly" maxlength="2" value="1" /> {'month(s)'|gettext}
	<br />
	<input type="radio" name="month_type" value="1" /> {'By Day'|gettext}
	<br />
	<input type="radio" name="month_type" value="0" /> {'By Date'|gettext}
</div>

<div id="recur_yearly" style="display: none">
	{'Every'|gettext} <input type="text" size="2" name="recur_freq_recur_yearly" maxlength="2" value="1" /> {'year(s)'|gettext}
</div>

<div id="until_date" style="display: none">
    {'Until'|gettext} %%UNTILDATEPICKER%%
</div>

<script type="text/javascript">

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

</script>