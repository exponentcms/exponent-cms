<?php
	include_once('../../exponent_bootstrap.php');
	include_once(BASE.'conf/config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta http-equiv="refresh" content="2;url=<?php echo URL_FULL; ?>cart/confirm">
	<title></title>
</head>
<body>
	<?php
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	?>
</body>
</html>