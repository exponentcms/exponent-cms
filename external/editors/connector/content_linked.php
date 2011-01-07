<?php
define("SCRIPT_EXP_RELATIVE","external/editors/connector/");
define("SCRIPT_FILENAME","content_linked.php");

require_once("../../../exponent.js.php");
global $router;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript">
		/* <![CDATA[ */
			var f_url = window.opener.document.getElementById("f_href");
			f_url.value = "<?php echo $router->buildUrlByPageId(exponent_sessions_get("last_section")); ?>#mod_<?php echo $_GET['cid']; ?>";
			var f_extern = window.opener.document.getElementById("f_extern");
			f_extern.checked = false;
			//TODO: find a way(maybe via containermod:_source_picker.tpl) to pass the title of the contained module to the Link Picker
			window.close();
		/* ]]> */
		</script>
	</head>
	<body/>
</html>
