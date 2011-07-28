<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.LANG_CHARSET}" />
		<title>{$smarty.const.SITE_TITLE} :: {$_TR.down}</title>
		<style type="text/css">{literal}
			div {
				font-size: 10pt;
				font-family: Arial, sans-serif;
				font-weight: normal;
				color: #333;
			}
		{/literal}</style>
	</head>
	<body>
	
	<div style="border: 1px solid black; margin: 15%; padding: 3em;">
		{$smarty.const.MAINTENANCE_MSG_HTML}
		<p>
			<!--a href="login.php">{$_TR.login}</a-->
		</p>
		<h3>{$_TR.login}</h3>
		{chain module=loginmodule view=Default title="Administrators Login"}
	</div>
	</body>
</html>
