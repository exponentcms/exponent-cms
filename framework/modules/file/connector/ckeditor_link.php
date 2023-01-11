<?PHP

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

/**
 * Implements the browse server feature within the CKEditor insert url/link dialogs
 */

	define("SCRIPT_EXP_RELATIVE","framework/modules/file/connector/");
	define("SCRIPT_FILENAME","ckeditor_link.php");

	require_once('../../../../exponent.php');
?>
<!DOCTYPE HTML>
<html>

	<head>
		<title><?PHP echo gt('Insert/Modify Link'); ?></title>

		<script type="text/javascript" src="<?PHP echo PATH_RELATIVE ?>exponent.js2.php"></script>
  		<script type="text/javascript">
			// Helper function to get parameters from the url
			function getUrlParam(paramName) {
				var pathArray = window.location.pathname.split( '/' );
                if (paramName == 'update' || paramName == 'filter') {
                    if (paramName == 'update') {
                        var parmu = pathArray.indexOf('update');
                        if (parmu > 0)
							return pathArray[parmu+1];
                    } else if (paramName == 'filter') {  //fixme we never get here?
                        var parmf = pathArray.indexOf('filter');
                        if (parmf > 0)
							return pathArray[parmf+1];
                    }
                }
				if (EXPONENT.SEF_URLS && pathArray.indexOf(paramName) != -1) {
					var parm = pathArray.indexOf(paramName);
					if (parm > 0)
						return pathArray[parm+1];
				} else {
					var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
					var match = window.location.search.match(reParam) ;
					return (match && match.length > 1) ? match[1] : '' ;
				}
			}

			function onPageSelect(section, text, title) {
                var update = getUrlParam('update');
                if (update !== 'noupdate' && typeof top.tinymce !== 'undefined' && top.tinymce !== null) update = 'tiny';
                var fileUrl = EXPONENT.PATH_RELATIVE+section;
                if (update == 'ck') {
       				// CKEditor integration
                    var funcNum = getUrlParam('CKEditorFuncNum');
                    window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
                } else if (update == 'tiny') {
       				// TinyMCE integration
                    // pass selected file path to TinyMCE
//                    top.tinymce.activeEditor.windowManager.getParams().setUrl(fileUrl);
					top.tinymce.activeEditor.windowManager.getParams().oninsert(fileUrl, text, title);
                    // close popup window
                    top.tinymce.activeEditor.windowManager.close();
                }

				window.close();
				return false;
			};

			function onOK() {
                var update = getUrlParam('update');
                if (update !== 'noupdate' && typeof top.tinymce !== 'undefined' && top.tinymce !== null) update = 'tiny';
                var fileUrl = EXPONENT.PATH_RELATIVE+document.getElementById("f_href").value;
                if (update == 'ck') {
       				// CKEditor integration
                    var funcNum = getUrlParam('CKEditorFuncNum');
                    window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
                } else if (update == 'tiny') {
       				// TinyMCE integration
                    // pass selected file path to TinyMCE
//                    top.tinymce.activeEditor.windowManager.getParams().setUrl(fileUrl);
					top.tinymce.activeEditor.windowManager.getParams().oninsert(fileUrl, document.getElementById("f_title").value, document.getElementById("f_alt").value);
                    // close popup window
                    top.tinymce.activeEditor.windowManager.close();
                }

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
                var update = getUrlParam('update');
                if (typeof top.tinymce !== 'undefined' && top.tinymce !== null) update = 'tiny';
				window.resizeTo(<?PHP echo FM_WIDTH ?>, <?PHP echo FM_HEIGHT ?>);
                if (update == 'ck') {
                    var funcNum = getUrlParam('CKEditorFuncNum');
                    var partNum = getUrlParam('CKEditor');
                    if (EXPONENT.SEF_URLS) {
                        window.location.href=EXPONENT.PATH_RELATIVE+'file/picker/ajax_action/1/update/ck/CKEditor/'+partNum+'/CKEditorFuncNum/'+funcNum+'/langCode/en/';
                    } else {
                        window.location.href=EXPONENT.PATH_RELATIVE+'file/picker?ajax_action=1&update=ck&CKEditor='+partNum+'&CKEditorFuncNum='+funcNum+'&langCode=en';
                    }
                } else if (update == 'tiny') {
                    if (EXPONENT.SEF_URLS) {
                        window.location.href = EXPONENT.PATH_RELATIVE + 'file/picker/ajax_action/1/update/tiny/';
                    } else {
                        window.location.href = EXPONENT.PATH_RELATIVE + 'file/picker?ajax_action=1&update=tiny';
                    }
                }
            }

			function openContentLinker() {
                window.open("../../../../source_selector.php?dest="+escape("framework/modules/file/connector/content_linked.php?dummy")+"&vview=_linkPicker&vmod=container&showmodules=all","contentlinker","toolbar=no,title=no,width=800,height=600,scrollbars=yes");
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
		<table style="width: 100%;">
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
						<input id="f_alt" type="hidden"/>
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
    $sections = section::levelTemplate(0,0);
    $standalones = $db->selectObjects('section','parent = -1');
?>
<strong><?PHP echo gt('Site Hierarchy'); ?></strong><hr>
	<table cellpadding="1" cellspacing="0" border="0" width="100%">
<?PHP
           foreach ($sections as $section) {
?>
               <tr><td style="padding-left: <?PHP echo ($section->depth*20); ?>px">
               <?PHP
                   if ($section->active) {
               ?>
                       <a href="javascript:onPageSelect(<?PHP echo "'".$section->sef_name."'"; ?>,<?PHP echo "'".addslashes($section->name)."'"; ?>,<?PHP echo "'".addslashes($section->page_title)."'"; ?>)" class="navlink"><?PHP echo htmlentities($section->name); ?></a>&#160;
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
<strong><?PHP echo gt('Standalone Pages'); ?></strong><hr>
	<table cellpadding="1" cellspacing="0" border="0" width="100%">
    <?PHP
           foreach ($standalones as $section) {
    ?>
               <tr><td style="padding-left: 20px">
                   <a href="javascript:onPageSelect(<?PHP echo "'".$section->sef_name."'"; ?>,<?PHP echo "'".$section->name."'"; ?>,<?PHP echo "'".$section->page_title."'"; ?>)" class="navlink"><?PHP echo htmlentities($section->name); ?></a>&#160;
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