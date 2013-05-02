<?PHP
	define("SCRIPT_EXP_RELATIVE","external/editors/connector/");
	define("SCRIPT_FILENAME","CKeditor_link.php");
	
	require_once('../../../exponent.php');
?>
<!DOCTYPE HTML>
<html>

	<head>
		<title><?PHP echo gt('Insert/Modify Link'); ?></title>

		<script type="text/javascript" src="<?PHP echo PATH_RELATIVE ?>exponent.js2.php"></script>
<!--		<script type="text/javascript" src="popup.js"></script>-->
<!--		<script type="text/javascript" src="--><?PHP //echo PATH_RELATIVE . 'external/editors/connector/lang/' . exponent_lang_convertLangCode(LANG) . '.js'?><!--"></script>-->
<!--		<script type="text/javascript" src="--><?PHP //echo PATH_RELATIVE . 'external/editors/connector/lang/en.js'?><!--"></script>-->
  		<script type="text/javascript">
		/* <![CDATA[ */
//			I18N = eXp.I18N;
//
//			function i18n(str) {
//  				return (I18N[str] || str);
//			};
			
			function getUrlParam(paramName) {
				var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
				var match = window.location.search.match(reParam) ;

				return (match && match.length > 1) ? match[1] : '' ;
			}
			
			function onPageSelect(section) {
				
				// CKeditor integration
				var funcNum = getUrlParam('CKEditorFuncNum');
				var fileUrl = EXPONENT.PATH_RELATIVE+section;
				window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
		
				window.close();
				return false;
			};

			function onOK() {
				
				// CKeditor integration
				var funcNum = getUrlParam('CKEditorFuncNum');
				var fileUrl = EXPONENT.PATH_RELATIVE+document.getElementById("f_href").value;
				window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);

				window.close();
				return false;
			};

			function onCancel() {
				window.close();
				return false;
			};

            EXPONENT.passBackFile = function(fi) {
                window.opener.EXPONENT.passBackFile(fi);
            }

            function openFileManager() {
                var funcNum = getUrlParam('CKEditorFuncNum');
                var partNum = getUrlParam('CKEditor');
                window.location.href=EXPONENT.PATH_RELATIVE+'file/picker?ajax_action=1&ck=1&update=fck&CKEditor='+partNum+'&CKEditorFuncNum='+funcNum+'&langCode=en';
            }

			function openContentLinker() {
//				window.open("../../../source_selector.php?dest="+escape("external/editors/connector/content_linked.php?dummy")+"&vview=_linkPicker&vmod=containermodule&showmodules=all","contentlinker","toolbar=no,title=no,width=800,height=600,scrollbars=yes");
                window.open("../../../source_selector.php?dest="+escape("external/editors/connector/content_linked.php?dummy")+"&vview=_linkPicker&vmod=container&showmodules=all","contentlinker","toolbar=no,title=no,width=800,height=600,scrollbars=yes");
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
				width: 12em;
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
/*				border-top: 1px */
				solid #999;
				padding: 2px;
				text-align: right;
			}
		/* ]]> */
		</style>

	</head>

<!--	<body onload="__dlg_translate(eXp._TR);">-->
    <body>
		<div class="title"><?PHP echo gt('Insert/Modify Link'); ?></div>
		<table border="0" style="width: 100%;">
			<tbody>
				<tr valign="top">
					<td>
						<a class="header"><?PHP echo gt('Select a Page below'); ?></a>
					</td>
                    <td>
                        <?PHP echo gt('or'); ?>
                    </td>
					<td align="center">
						<a href="#" onclick="openContentLinker(); return false;"><?PHP echo gt('Click Here to Link to Content'); ?></a>
						<input id="f_href" type="hidden"/>
						<input id="f_extern" checked="checked" type="hidden"/>
						<input id="f_title" type="hidden"/>
                        <div id="f_text" style="color:red"><?PHP echo gt('nothing selected'); ?></div>
						<div id="buttons">
							<button type="button" name="ok" onclick="return onOK();"><?PHP echo gt('OK'); ?></button>
							<button type="button" name="cancel" onclick="return onCancel();"><?PHP echo gt('Cancel'); ?></button>
						</div>
					</td>
                    <td align="center">
                        <?PHP echo gt('or'); ?>
                    </td>
                    <td align="right">
                        <a href="#" style="text-align:center;" onclick="openFileManager(); return false;"><?PHP echo gt('Switch to File Manager'); ?></a>
                    </td>
				</tr>
			</tbody>
		</table>
<?PHP
if ($user) {
    $sections = navigationController::levelTemplate(0,0);
    $standalones = $db->selectObjects('section','parent = -1');
?>
<strong><?PHP echo gt('Site Hierarchy'); ?></strong><hr size="1" />
	<table cellpadding="1" cellspacing="0" border="0" width="100%">
<?PHP
           foreach ($sections as $section) {
?>
               <tr><td style="padding-left: <?PHP echo ($section->depth*20); ?>px">
               <?PHP
                   if ($section->active) {
               ?>
                       <a href="javascript:onPageSelect(<?PHP echo "'".$section->sef_name."'"; ?>)" class="navlink"><?PHP echo htmlentities($section->name); ?></a>&#160;
               <?PHP
                   } else {
                       echo $section->name;
                   }
               ?>
               </td></tr>
<?PHP
           }
?>
	</table>
<?PHP
    if (count($standalones)) {
?>
<BR /> <BR />
<strong><?PHP echo gt('Standalone Pages'); ?></strong><hr size="1" />
	<table cellpadding="1" cellspacing="0" border="0" width="100%">
    <?PHP
           foreach ($standalones as $section) {
    ?>
               <tr><td style="padding-left: 20px">
                   <a href="javascript:onPageSelect(<?PHP echo "'".$section->sef_name."'"; ?>)" class="navlink"><?PHP echo htmlentities($section->name); ?></a>&#160;
               </td></tr>
    <?PHP
           }
        ?>
	</table>
<?PHP
    }
?>
<?PHP
}
?>
	</body>
</html>