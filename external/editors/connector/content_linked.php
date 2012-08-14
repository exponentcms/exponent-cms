<?php
define("SCRIPT_EXP_RELATIVE","external/editors/connector/");
define("SCRIPT_FILENAME","content_linked.php");

require_once("../../../exponent.php");
global $router;
?>
<!DOCTYPE HTML>
<html>
	<head>
        <script type="text/javascript">
            function chosen() {
                var f_url = window.opener.document.getElementById("f_href");
                f_url.value = "<?php echo $router->buildUrlByPageId(expSession::get("last_section")); ?>#mod_<?php echo $_GET['cid']; ?>";
                var f_extern = window.opener.document.getElementById("f_extern");
                f_extern.checked = false;
                var f_text = window.opener.document.getElementById("f_text");
                f_text.innerHTML = "<?php echo $router->buildUrlByPageId(expSession::get("last_section")); ?>#mod_<?php echo $_GET['cid']; ?>";
                window.close();
            };
  		</script>
	</head>
    <body onload='chosen();'>
	<body/>
</html>