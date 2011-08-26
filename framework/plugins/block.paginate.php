<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################
/** @define "BASE" "../.." */

function smarty_block_paginate($params,$content,&$smarty) {
	if ($content) {
?>

	<script language="JavaScript">

	//Cookie Stuff
	function getCookieVal(offset) {
		var endstr = document.cookie.indexOf(";", offset);
		if (endstr == -1)
			endstr = document.cookie.length;
		return decodeURIComponent (document.cookie.substring(offset, endstr));
	}


	function getCookie(name) {
		var arg = name + "=";
		var alen = arg.length;
		var clen = document.cookie.length;
		var i = 0;
		while (i < clen) {
			var j = i + alen;
			if (document.cookie.substring(i,j) == arg)
				return getCookieVal(j);
			i = document.cookie.indexOf(" ", i) + 1;
			if (i == 0) break;

		}
		return null;
	}

	function setCookie(name, value) {
		document.cookie = name + "=" + encodeURIComponent(value);
	}
	//End Cookie Stuff.


	//Class to define new filters
	function cFilter(name, filterFunc) {
		//This is the display name
		this.name = name;

		//Call back function. Should expect a data object and return a boolean.
		//True = included;
		//False = not included
		this.filterFunc = filterFunc;
	}

	//Class to define columns
	function cColumn(headerText, attribute, overrideFunc, sortFunc, sLink) {
		//Column Header Text
		this.headerText = headerText;

		//Attribut of the data object to display if overrideFunc is null;
		this.attribute = "var_"+attribute;

		//Callback Function. Should expect a dataobject and return a string
		//This will be called for each row and the return data will be displayed.
		//Use this to put any special data into the the cell.
		//If this is defined, attribute will be ignored.
		this.overrideFunc = overrideFunc;

		this.sortFunc = sortFunc;

		//This will add an href with a src of link for each item in the column.
		//The id from the record is appended to the link.
		this.sLink = sLink || "";

		this.ascending = 0;

	}

	//This is the main Sorting/Filtering/Paging class
	var paginate = new function() {

		this.rowsPerPage = <?php echo ((isset($params['rowsPerPage']))?$params['rowsPerPage']:20); ?>;
		this.tableName = <?php echo ((isset($params['tableName']))?'"'.$params['tableName'].'"':'"dataTable"'); ?>;
		this.currentPage = <?php echo ((isset($params['currentPage']))?$params['currentPage']:'1'); ?>;

		this.filterCellName = <?php echo ((isset($params['filterCellName'])) ?'"'.$params['filterCellName'].'"': '"filterCell"'); ?>;
		this.searchCellName = <?php echo ((isset($params['searchCellName'])) ?'"'.$params['searchCellName'].'"': '"searchCell"'); ?>;
		this.modulePrefix = <?php echo ((isset($params['modulePrefix'])) ?'"'.$params['modulePrefix'].'"': '"default"'); ?>;
		this.name = <?php echo ("'".$params['paginateName'] . "';\n"); ?>

		this.noRecords = '<?php echo ((isset($params['noRecordsText']))?$params['noRecordsText']:"No records found"); ?>';
		this.noMatches = '<?php echo ((isset($params['noMatchesText']))?$params['noMatchesText']:"Nothing matched your criteria."); ?>';

		this.filteredData = new Array();
		this.allData = new Array();

		//This will hold the location of each of the controls
		this.controls = new Array();

		//This is an array of cColumn Objects
		this.columns = new Array();

		//This is an array of cFilter Objects.
		this.filters = new Array();

		this.applyFilter = function(sID) {

			this.filteredData = null;
			this.filteredData = new Array();
			var bInclude = false;
			var bHit = false;
			var aFilterArray = new Array();
			var any = 1;

			if (sID == "fromcookie") {
				var cookieStr = getCookie(this.name + "_filters");
				if (cookieStr != null) {
					any = cookieStr.substr(0,1)=="1";

					aFilterArray = cookieStr.substr(2).split(":");
					for (var i in aFilterArray) {
						aFilterArray[i] = aFilterArray[i] == "true";
					}
				}
			}
			else {
				for (var filterKey in this.filters) {
					aFilterArray[filterKey] = document.getElementById(sID + "_filter" + filterKey).checked;
				}
				any = document.getElementById(sID + "_any").checked;
			}

			for (var dataKey in this.allData) {
				bInclude = false;
				bHaveFilter = false;
				bHit = true;
				for (var filterKey in aFilterArray) {
					if (aFilterArray[filterKey]) {
						bHaveFilter = true;
						bHit = this.filters[filterKey].filterFunc(this.allData[dataKey]);

						if (any) {
							if (bHit) {
								bInclude = true;
								break;
							}
						}
						else { //All
							if (!bHit) {
								bInclude = false;
								break;
							}
							else {
								bInclude = true;
							}
						}
					}
				}
				if (bInclude || !bHaveFilter) {
					this.filteredData.push(this.allData[dataKey]);
				}
			}

			var sortcolumn = getCookie(this.name + "_sortcolumn");
			var sortdirection = getCookie(this.name + "_sortdirection");

			this.defaultSort(sortcolumn,sortdirection);

			var sCookie = "";
			if (any) {
				sCookie = "1";
			}
			else {
				sCookie = "0";
			}
			sCookie += ":" + aFilterArray.join(":");

			setCookie(this.name + "_filters",sCookie);

			this.drawTable();
		}

		this.defaultSort = function(sortcolumn,sortdirection) {
			if (sortcolumn != null) {
				this.columns[sortcolumn].ascending = sortdirection;
				this.sort(sortcolumn,true);
			}
			else {
				for (var key in this.columns) {
					if (!(this.columns[key].sortFunc != null && this.columns[key].attribute != "")) {
						this.columns
						this.sort(key,true);
						break;
					}
				}
			}
		}

		this.sort = function(index,doNotDraw) {
			for (var data in this.columns) {
				if (data == index) {
					if (this.columns[index].ascending == 1) {
						this.columns[index].ascending = 0;
					} else {
						this.columns[index].ascending = 1;
					}
				} else {
					this.columns[data].ascending = -1;
				}
			}

			var asc = this.columns[index].ascending==1?-1:1;

			setCookie(this.name + "_sortcolumn",index);
			setCookie(this.name + "_sortdirection",asc);

			if (this.columns[index].sortFunc != null) {
				if (this.columns[index].ascending) {
					this.filteredData.sort(this.columns[index].sortFunc);
				} else {
					var sortFunc = this.columns[index].sortFunc;
					this.filteredData.sort(function(a,b) {
						return -1*(sortFunc(a,b));
					});
				}
			} else {
				var attr = this.columns[index].attribute;
				if (attr != "") {
					this.filteredData.sort(function(a,b) {
						return (asc)*(a[attr].toLowerCase() > b[attr].toLowerCase() ? -1 : 1);
					});
				}
			}

			if (!doNotDraw) this.drawTable();
		}


		this.gotoPage = function(iPage) {
			setCookie(this.name + "_page",iPage);
			this.currentPage = parseInt(iPage);
			this.drawTable();
		}

		this.selectedPage = function(select) {
			this.gotoPage(parseInt(select.options[select.selectedIndex].value) + 1);
		}

		this.drawTable = function() {
			var ptTable = document.getElementById(this.tableName);
			if (ptTable == null) return;
			while (ptTable.rows.length > 0) {
				ptTable.deleteRow(0);
			}

			if (this.currentPage > (Math.floor(this.filteredData.length / this.rowsPerPage) + 1)) {
				this.currentPage = Math.floor(this.filteredData.length / this.rowsPerPage) + 1;
			}

			var startCount = (this.currentPage - 1) * this.rowsPerPage;
			var endCount = startCount + this.rowsPerPage;
			if (endCount > this.filteredData.length) endCount = this.filteredData.length;

			var row = document.createElement("tr");
			var cell = document.createElement("td");
			var cell_content;

			row = document.createElement("tr");

			for (var data in this.columns) {
				cell = document.createElement("th");
				if (document.all) {
					// IE is different
					cell.setAttribute("className","header " + this.modulePrefix + "_header");
				} else {
					cell.setAttribute("class","header " + this.modulePrefix + "_header");
				}
				cell_content = this.columns[data].headerText;
				if (this.columns[data].attribute != "" || this.columns[data].sortFunc != null) {
					cell_content = "<a href='#' onclick='paginate.sort(\""+data+"\"); return false;'>"+this.columns[data].headerText+"</a>";
					if (this.columns[data].ascending != -1) {
						cell_content += "&nbsp;<img id='sortCol_"+data+"' src='<?php echo ICON_RELATIVE; ?>sort"+(this.columns[data].ascending ? "de" : "a")+"scending.png' border='0' />";
					} else {
						cell_content += "&nbsp;<img id='sortCol_"+data+"' src='<?php echo ICON_RELATIVE; ?>blank.gif' border='0' />";
					}
				}
				cell.innerHTML = cell_content;
				row.appendChild(cell);
			}
			ptTable.appendChild(row);
			if (this.filteredData.length) {
				var rowCycle = 0;
				var rowCounter = 0;
				for (var dataObject in this.filteredData) {
					rowCounter++;
					if ((rowCounter > startCount) && (rowCounter <= (startCount + this.rowsPerPage))) {
						row = document.createElement("tr");
						if (document.all) {
							// IE is different
							row.setAttribute("className","row " + ((rowCycle == 0)?"odd":"even"));
						} else {
							row.setAttribute("class","row " + ((rowCycle == 0)?"odd":"even"));
						}
						rowCycle = !rowCycle;
						for (var data in this.columns) {
							cell = document.createElement("td");
							cell.setAttribute("valign","top");
							var sText = "";
							if (this.columns[data].overrideFunc == undefined) {
								sText = (this.filteredData[dataObject][this.columns[data].attribute] == undefined)?"&nbsp;":this.filteredData[dataObject][this.columns[data].attribute];

							}
							else {
								sText = this.columns[data].overrideFunc(this.filteredData[dataObject]);
							}
							if (this.columns[data].sLink != "") {
								cell.innerHTML = "<a href='#' onclick='" + this.columns[data].sLink + this.filteredData[dataObject]['id'] + "' class='mngmntlink " + this.modulePrefix + "_mngmntlink'>" + sText + "</a>";
							} else {
								cell.innerHTML = sText;
							}

							row.appendChild(cell);
						}
						ptTable.appendChild(row);
					}
				}
			} else {
				row = document.createElement("tr");
				if (document.all) {
					// IE is different
					row.setAttribute("className","row");
				} else {
					row.setAttribute("class","row");
				}
				cell = document.createElement("td");
				cell.setAttribute("style","text-align: center; font-style: italic");
				cell.setAttribute("colspan",this.columns.length);
				if (this.allData.length) {
					cell.innerHTML = this.noMatches;
				} else {
					cell.innerHTML = this.noRecords;
				}
				row.appendChild(cell);
				ptTable.appendChild(row);
			}

			for (var key in this.controls) {
				if (this.filteredData.length) {
					switch (key.substr(0,3)) {
						case "pp_":
							//Page Picker Drop Down
							var select = document.createElement("select");
							select.setAttribute("onchange","paginate.selectedPage(this); return false");
							var opt = null;
							for (var i = 0; i < Math.floor(this.filteredData.length/this.rowsPerPage) + 1; i++) {
								opt = document.createElement("option");
								opt.innerHTML = (i+1);
								opt.setAttribute("value",i);
								if (i == this.currentPage-1) opt.setAttribute("selected","true");
								select.appendChild(opt);
							}

							document.getElementById(key).innerHTML = "";
							document.getElementById(key).appendChild(document.createTextNode(this.controls[key]));
							document.getElementById(key).appendChild(select);
							break;
						case "tp_":
							//Text based page picker
							iPad = this.controls[key];

							var totalPages = Math.ceil(this.filteredData.length/this.rowsPerPage);

							var iLeftOverflow = iPad - (this.currentPage-1);
							var iLeftStart = 1;
							if (iLeftOverflow < 0) iLeftStart = Math.abs(iLeftOverflow) + 1;

							var iRightEnd = this.currentPage + iPad;
							var iRightOverflow = totalPages - iRightEnd;

							if (iRightOverflow < 0) iRightEnd = totalPages;

							if (iLeftOverflow > 0) iRightEnd += iLeftOverflow;
							if (iRightEnd > totalPages) iRightEnd = totalPages;

							if (iRightOverflow < 0) iLeftStart -= Math.abs(iRightOverflow);
							if (iLeftStart < 1) iLeftStart = 1;

							var sOut = "";
							if (iLeftStart > 1) {
								sOut = "<a href='JavaScript:paginate.gotoPage(1);' class='mngmntlink " + this.modulePrefix + "_mngmntlink'>&lt&lt</a> <a href='JavaScript:paginate.gotoPage(" + ((this.currentPage - (iPad * 2) < 1)?"1":(this.currentPage - (iPad * 2))) + ");' class='mngmntlink " + this.modulePrefix + "_mngmntlink'>...</a> ";
							}
							for (var x = iLeftStart; x <= iRightEnd; x++) {
								if (x != this.currentPage) {
									sOut += "<a href='JavaScript:paginate.gotoPage(" + x + ");' class='mngmntlink " + this.modulePrefix + "_mngmntlink'>" + x + "</a> ";
								}
								else {
									sOut += "<b>" + x + "</b> ";
								}
							}
							if (iRightEnd < totalPages) {
								sOut += "<a href='JavaScript:paginate.gotoPage(" + ((this.currentPage + (iPad * 2) > totalPages)?totalPages:(this.currentPage + (iPad * 2))) + ");' class='mngmntlink " + this.modulePrefix + "_mngmntlink'>...</a> <a href='JavaScript:paginate.gotoPage(" + totalPages + ");' class='mngmntlink " + this.modulePrefix + "_mngmntlink'>&gt&gt</a>";
							}

							document.getElementById(key).innerHTML = sOut;
							break;
						case "ps_":
							var sText = this.controls[key];
							var regex = /%cp/
							sText = sText.replace(regex,this.currentPage);
							regex = /%tp/
							sText = sText.replace(regex,Math.floor(this.filteredData.length / this.rowsPerPage) + 1);
							regex = /%sr/
							sText = sText.replace(regex,(startCount + 1));
							regex = /%er/
							sText = sText.replace(regex,endCount);
							regex = /%tr/
							sText = sText.replace(regex,this.filteredData.length);
							document.getElementById(key).innerHTML = sText;
							break;
						default:
							break;
					}
				}
			}
		}

		this.drawPagePicker = function(sText) {
			var sID = "pp_" + (Math.floor(Math.random() * 10000));
			this.controls[sID] = sText;
			return "<span id='" + sID + "'></span>";
		}

		this.drawPageTextPicker = function(padding) {
			if (padding == undefined) padding = 3;
			var sID = "tp_" + (Math.floor(Math.random() * 10000));
			this.controls[sID] = padding;
			return "<span id='" + sID + "'></span>";
		}

		this.drawPageStats = function (sText) {
			//This will return write page location information based on sText;
			//Keys that will be replaces...
			//  %cp - current page
			//  %tp - total pages
			//  %sr - starting record
			//  %er - ending record
			//  %tr - total records
			if (sText == "") sText = "On page %cp of %tp viewing records %sr to %er of %tr.";
			var sID = "ps_" + (Math.floor(Math.random() * 10000));
			this.controls[sID] = sText;
			return "<span id='" + sID + "'></span>";
		}

		this.drawFilterForm = function() {
			var sID = "ff_" + (Math.floor(Math.random() * 10000));
			this.controls[sID] = "";
			if (this.filters.length) {

				var aFilterArray = new Array();
				var cookieStr = getCookie(this.name + "_filters");
				var any = true;

				if (cookieStr != null) {
					any = cookieStr.substr(0,1)=="1";

					aFilterArray = cookieStr.substr(2).split(":");
					for (var i in aFilterArray) {
						aFilterArray[i] = aFilterArray[i] == "true";
					}
				}

				var cell = document.createElement("span");
				for (key1 in this.filters) {
					var filter = this.filters[key1];
					var cb = document.createElement("input");
					cb.setAttribute("type","checkbox");
					cb.setAttribute("id", sID + "_filter"+key1);
					if (aFilterArray[key1]) {
						cb.setAttribute("checked","true");
					}
					cell.appendChild(cb);
					cell.appendChild(document.createTextNode(filter.name));
					cell.appendChild(document.createElement("br"));

				}

				var radio_any = document.createElement("input");
				radio_any.setAttribute("type","radio");
				if (any) {
					radio_any.setAttribute("checked","true");
				}
				radio_any.setAttribute("id",sID + "_any");
				radio_any.setAttribute("name", sID + "_match");

				var radio_all = document.createElement("input");
				radio_all.setAttribute("type","radio");
				if (!any) {
					radio_all.setAttribute("checked","true");
				}
				radio_all.setAttribute("id",sID + "_all");
				radio_all.setAttribute("name", sID + "_match");

				cell.appendChild(radio_any);
				cell.appendChild(document.createTextNode("Match Any Criteria"));
				cell.appendChild(document.createElement("br"));

				cell.appendChild(radio_all);
				cell.appendChild(document.createTextNode("Match All Criteria"));
				cell.appendChild(document.createElement("br"));

				var btn = document.createElement("input");
				btn.setAttribute("type","button");
				btn.setAttribute("value","Filter");
				btn.setAttribute("onclick","paginate.applyFilter('" + sID + "'); return false;");
				cell.appendChild(btn);
				return cell.innerHTML;
			}
		}

		this.drawSearchForm = function() {
			var sID = "sf_" + (Math.floor(Math.random() * 10000));
			this.controls[sID] = "";
			return "<span id='" + sID + "'></span>";
		}

		this.drawForms = function() {
			this.drawFilterForm();
			this.drawSearchForm();
		}
	}

	<?php
	if (isset($params['objects']) && count($params['objects']) > 0) {
		//Write Out DataClass. This is generated from the data object.
		echo expJavascript::jClass($params['objects'][0],'paginateDataClass');
	?>

		var tempObj = new paginateDataClass();

		for (var attribute in tempObj) {
			paginate.columns.push(new cColumn(attribute,attribute,null));
		}

	<?php

		//This will load up the data...
		foreach ($params['objects'] as $object) {
			echo "paginate.allData.push(".expJavascript::jObject($object,'paginateDataClass').");\r\n";
			echo "paginate.allData[paginate.allData.length-1].__ID = paginate.allData.length-1;\r\n";
		}

		echo "paginate.filteredData = paginate.allData;\n";
	}

	echo $content;
	?>
	var page = getCookie(paginate.name + "_page");
	if (page != null) {
		paginate.currentPage = parseInt(page);
	}

	var sortcolumn = getCookie(paginate.name + "_sortcolumn");
	var sortdirection = getCookie(paginate.name + "_sortdirection");
	paginate.applyFilter("fromcookie");

	</script>
<?php
	}
}

?>
