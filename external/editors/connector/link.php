<?PHP
	define("SCRIPT_EXP_RELATIVE","external/editors/connector/");
	define("SCRIPT_FILENAME","link.php");
	
	require_once("../../../exponent.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>Insert/Modify Link</title>

		<script type="text/javascript" src="../../../exponent.js.php"></script>
		<script type="text/javascript" src="popup.js"></script>
		<script type="text/javascript" src="<?PHP echo PATH_RELATIVE . 'external/editors/connector/lang/' . exponent_lang_convertLangCode(LANG) . '.js'?>"></script>
  		<script type="text/javascript">
		/* <![CDATA[ */
			var I18N = eXp.I18N;
			

			function i18n(str) {
  				return (I18N[str] || str);
			};

			function onTargetChanged() {
  				var f = document.getElementById("f_other_target");
  				if (this.value == "_other") {
    					f.style.visibility = "visible";
    					f.select();
    					f.focus();
  				} else f.style.visibility = "hidden";
			};

			function Init() {
				__dlg_translate(eXp._TR);
				__dlg_init();
				var param = window.dialogArguments;
				var target_select = document.getElementById("f_target");
				if (param) {
					document.getElementById("f_href").value = param["f_href"];
					document.getElementById("f_extern").checked = (param["f_href"].substr(0,7) == "http://");
					document.getElementById("f_title").value = param["f_title"];
					comboSelectValue(target_select, param["f_target"]);
					if (target_select.value != param.f_target) {
						var opt = document.createElement("option");
						opt.value = param.f_target;
						opt.innerHTML = opt.value;
						target_select.appendChild(opt);
						opt.selected = true;
					}
				}
				var opt = document.createElement("option");
				opt.value = "_other";
				opt.innerHTML = eXp.i18n("Other");
				target_select.appendChild(opt);
				target_select.onchange = onTargetChanged;
				document.getElementById("f_href").focus();
				document.getElementById("f_href").select();
			};


			function onOK() {
				var required = {"f_href": "You must enter the URL where this link points to"};

				for (var i in required) {
					var el = document.getElementById(i);
					if (!el.value) {
						alert(required[i]);
						el.focus();
						return false;
					}
				}
  
				var el = document.getElementById("f_href");
				if (el.value.substr(0,7) != "http://") {
					var cb = document.getElementById("f_extern");
					if (cb.checked == true) {
						//alert("External!");
						//el.setAttribute("value","http://"+el.value);
						el.value = "http://"+el.value;
					} else {
						//alert("Not External");
					}
					//return false;
				}
				// pass data back to the calling window
				var fields = ["f_href", "f_title", "f_target"];
				var param = new Object();
				for (var i in fields) {
					var id = fields[i];
					var el = document.getElementById(id);
					param[id] = el.value;
				}
				if (param.f_target == "_other") {
					param.f_target = document.getElementById("f_other_target").value;
				}
  
				// TinyMCE integration, indicates this comes from a link browser
				param["f_dialogType"] = "Link";
  
				__dlg_close(param);
				return false;
			};

			function onCancel() {
  				__dlg_close(null);
  				return false;
			};

			function openSectionLinker() {
				window.open("../../../modules/navigationmodule/nav.php?linkbase="+escape("../../external/editors/connector/section_linked.php?dummy"),"sectionlinker","toolbar=no,title=no,width=250,height=480,scrollbars=yes");
			}

			function openContentLinker() {
				window.open("../../../content_selector.php?dest="+escape("external/editors/connector/content_linked.php?dummy")+"&vview=_linkPicker&vmod=containermodule&showmodules=all","contentlinker","toolbar=no,title=no,width=640,height=480,scrollbars=yes");
			}
		/* ]]> */
		</script>

		<style type="text/css">
		/* <![CDATA[ */
			html, body {
  				background: ButtonFace;
				color: ButtonText;
				font: 11px Tahoma,Verdana,sans-serif;
				margin: 0px;
				padding: 0px;
			}
			body {
				padding: 5px;
			}
			table {
				font: 11px Tahoma,Verdana,sans-serif;
			}
			select, input, button {
				font: 11px Tahoma,Verdana,sans-serif;
			}
			button {
				width: 70px;
			}
			table .label {
				text-align: right;
				font-weight: normal;
				vertical-align: top;
				width: 8em;
			}

			.title {
				background: #ddf;
				color: #000;
				font-weight: bold;
				font-size: 120%;
				padding: 3px 10px;
				margin-bottom: 10px;
				border-bottom: 1px
				solid black;
				letter-spacing: 2px;
			}

			a {
				text-decoration: none;
				color: rgb(97,115,132);
				font-weight: bold;
			}

			#buttons {
				margin-top: 1em;
				border-top: 1px
				solid #999;
				padding: 2px;
				text-align: right;
			}
		/* ]]> */
		</style>

	</head>

	<body onload="Init()">
		<div class="title">Insert/Modify Link</div>

		<table border="0" style="width: 100%;">
		<tbody>
			<tr>
				<td colspan="2"><hr size="1" /><a class="header">Select a Page, or Content</a></td>
			</tr>
			<tr>
				<td class="label">Internal Links</td>
				<td align="center">
					<a href="#" onclick="openSectionLinker(); return false;">Link to a Page</a>&nbsp;|&nbsp;<a href="#" onclick="openContentLinker(); return false;">Link to Content</a>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr size="1" /><a class="header">Manually Enter Address</a></td>
			</tr>
			<tr>
				<td class="label">URL</td>
				<td><input type="text" id="f_href" style="width: 100%" /></td>
			</tr>
			<tr>
				<td class="label">&nbsp;</td>
				<td ><input type="checkbox" id="f_extern" checked="checked" /><a class="label">External Website</a></td>
			</tr>
			<tr>
				<td colspan="2"><hr size="1" /><a class="header">Additional Link Options</a></td>
			</tr>
  			<tr>
				<td class="label">Title (tooltip)</td>
				<td><input type="text" id="f_title" style="width: 100%" /></td>
			</tr>
			<tr>
				<td class="label">Target</td>
				<td>
					<select id="f_target">
						<option value="">None (use implicit)</option>
						<option value="_blank">New window (_blank)</option>
						<option value="_self">Same frame (_self)</option>
						<option value="_top">Top frame (_top)</option>
					</select>
					<input type="text" name="f_other_target" id="f_other_target" size="10" style="visibility: hidden" />
				</td>
			</tr>
		</tbody>
		</table>

		<div id="buttons">
			<button type="button" name="ok" onclick="return onOK();">OK</button>
			<button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
		</div>

	</body>
</html>
