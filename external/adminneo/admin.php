<?php

require_once('../../exponent.php');

use AdminNeo\Admin;
use AdminNeo\SerializedPreviewPlugin;
use AdminNeo\Bz2OutputPlugin;
use AdminNeo\ExternalLoginPlugin;
//use AdminNeo\FileUploadPlugin;
use AdminNeo\ForeignEditPlugin;
use AdminNeo\ConventionForeignKeys;
//use AdminNeo\FrameSupportPlugin;
//use AdminNeo\JsonDumpPlugin;
use AdminNeo\JsonPreviewPlugin;
//use AdminNeo\SlugifyEditPlugin;
//use AdminNeo\SystemForeignKeysPlugin;
use AdminNeo\TinyMcePlugin;
use AdminNeo\EditCalendarPlugin;
//use AdminNeo\TranslationPlugin;
//use AdminNeo\XmlDumpPlugin;
use AdminNeo\ZipOutputPlugin;

function adminneo_instance()
{
	class CustomAdmin extends Admin
	{
		public function getServiceTitle()
		{
			return 'Exponent CMS Database';
		}

		public function getDatabases($flush = true): array {
			return array(DB_NAME);
		}

		public function getTableName(array $tableStatus): string
		{
			$table = $tableStatus["Name"];
			// remove db prefix
		    $name = str_replace(array(DB_TABLE_PREFIX . '_', '_'), array('', ' '), $table);  // remove underscores
			return $name;
		}
	}

	global $user;
	if ($user->isLoggedIn() && $user->isSuperAdmin()) {
		$servers = [
			[
				"driver" => "mysql",
				"name" => "Exponent DB",
				"server" => DB_HOST,
				"database" => DB_NAME,
				"config" => ["colorVariant" => 'blue'],
				"username" => DB_USER,
				"password" => DB_PASS
			]
		];
	} else {
		$servers = [
			[
			]
		];
	}

	$config = [
		"colorVariant" => "blue",
		"navigationMode" => "dual",
		"preferSelection" => true,
		"jsonValuesDetection" => true,
		"jsonValuesAutoFormat" => true,
		"recordsPerPage" => 30,
		"hiddenDatabases" => ["__system"],
		"hiddenSchemas" => ["__system"],
		"defaultPasswordHash" => "",
		"sslTrustServerCertificate" => true,
		"visibleCollations" => ["utf8mb4*czech*ci", "ascii_general_ci"],
		"servers" => $servers,
		"defaultServer" => DB_HOST,
		"defaultDatabase" => DB_NAME,
		"versionVerification" => false,
		"relationLinks" => true
	];

	$plugins = [
		//new OtpLoginPlugin(base64_decode('RXiwXQLdoq7jVQ==')),
		new ConventionForeignKeys(),
		new ExternalLoginPlugin($user->isLoggedIn() && $user->isSuperAdmin()),
		new JsonPreviewPlugin(),
		new SerializedPreviewPlugin(),
		new ZipOutputPlugin(),
		new Bz2OutputPlugin(),
//		new JsonDumpPlugin(),
//		new XmlDumpPlugin(),
		// new SqlLogPlugin(),
		new TinyMcePlugin(
			PATH_RELATIVE."external/editors/tinymce/tinymce.min.js",
			PATH_RELATIVE
		),
		new EditCalendarPlugin(
	"<link rel='stylesheet' type='text/css' href='".JQUERYUI_CSS."'>\n"
		   . "<link rel='stylesheet' type='text/css' href='".JQUERY_RELATIVE."addons/css/jquery-ui-timepicker-addon.css'>\n"
		   . AdminNeo\script_src(JQUERY_SCRIPT)
		   . AdminNeo\script_src(JQUERYUI_SCRIPT)
		   . AdminNeo\script_src(JQUERY_RELATIVE."addons/js/jquery-ui-timepicker-addon.js"),
   JQUERY_RELATIVE."js/ui/i18n/datepicker-%s.js"),
		// new FileUploadPlugin("../export/upload"),
//		new TranslationPlugin(),
//		new SystemForeignKeysPlugin(),
		new ForeignEditPlugin(),
		// new SlugifyEditPlugin(),
		// new FrameSupportPlugin(),
	];

	return CustomAdmin::create($config, $plugins);
}

include "adminneo-5.2.1.php";
