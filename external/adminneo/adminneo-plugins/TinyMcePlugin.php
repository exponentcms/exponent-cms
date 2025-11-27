<?php

namespace AdminNeo;

/**
 * Use TinyMCE 7 editor for all edit fields containing "_html" in their name.
 *
 * Last changed in release: v5.2.0
 *
 * @link https://www.tiny.cloud/docs/tinymce/latest/php-projects/
 * @link https://www.tiny.cloud/docs/tinymce/latest/basic-setup/
 * @link https://www.tiny.cloud/get-tiny/language-packages/
 *
 * @link https://www.adminneo.org/plugins/#usage
 *
 * @author Jakub Vrana, https://www.vrana.cz/
 * @author Peter Knut
 *
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class TinyMcePlugin extends Plugin
{
	/** @var string */
	protected $path;

	/** @var string */
	protected $licenseKey;

	public function __construct($path = "tinymce/tinymce.min.js", $rpath = '', $licenseKey = "gpl")
	{
		$this->path = $path;
		$this->rpath = $rpath;
		$this->licenseKey = $licenseKey;
	}

	public function printToHead()
	{
		$language = $this->locale->getLanguage();
		$language = ($language == "zh" ? "zh-CN" : ($language == "zh-tw" ? "zh-TW" : $language));
		if (!file_exists(dirname($this->path) . "/langs/$language.js")) {
			$language = "en";
		}

//		$jqui = JQUERYUI_CSS;
//		echo script_src(JQUERY_SCRIPT);
//		echo script_src(JQUERYUI_SCRIPT);
//		echo "<link rel='stylesheet' type='text/css' href='$jqui'>\n";
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.version.js");
//		echo script_src($this->rpath . "external/elFinder/js/jquery.dialogelfinder.js");
//		echo script_src($this->rpath . "external/elFinder/js/jquery.elfinder.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.mimetypes.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.options.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.options.netmount.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.history.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.command.js");
//		echo script_src($this->rpath . "external/elFinder/js/elFinder.resources.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/button.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/contextmenu.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/cwd.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/dialog.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/fullscreenbutton.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/navbar.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/navdock.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/overlay.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/panel.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/path.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/places.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/searchbutton.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/sortbutton.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/stat.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/toast.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/toolbar.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/tree.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/uploadButton.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/viewbutton.js");
//		echo script_src($this->rpath . "external/elFinder/js/ui/workzone.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/archive.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/back.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/chmod.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/colwidth.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/copy.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/cut.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/download.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/duplicate.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/edit.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/empty.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/extract.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/forward.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/fullscreen.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/getfile.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/help.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/hidden.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/hide.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/home.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/info.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/links.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/mkdir.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/mkfile.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/netmount.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/open.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/opendir.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/opennew.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/paste.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/places.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/preference.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/quicklook.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/quicklook.plugins.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/reload.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/rename.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/resize.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/restore.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/rm.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/search.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/selectall.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/selectinvert.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/selectnone.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/sort.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/undo.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/up.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/upload.js");
//		echo script_src($this->rpath . "external/elFinder/js/commands/view.js");
//		echo script_src($this->rpath . "framework/modules/file/connector/i18n/elfinder." . $language . ".js");
//		echo script_src($this->rpath . "external/elFinder/js/extras/editors.default.js");

		echo script_src($this->path);
//		echo script_src($this->rpath . "framework/modules/file/connector/tinymceElfinder.js");
		?>

		<script <?= nonce(); ?>>
			//const mceElf = new tinymceElfinder({
			//   // connector URL (Set your connector)
			//   url: '<?php //echo $this->rpath; ?>//framework/modules/file/connector/elfinder.php',
			//   // upload target folder hash for this tinyMCE
			//   uploadTargetHash: 'lexp2_Lw', // Hash value on elFinder of writable folder
			//   // elFinder dialog node id
			//   nodeId: 'elfinder', // Any ID you decide
			//   baseUrl: '<?php //echo $this->rpath; ?>//external/elFinder/',
			//   cssAutoLoad: '<?php //echo $this->rpath; ?>//external/elFinder' + <?php //echo ELFINDER_THEME; ?>// + '/css/theme.css',
		   //});

			tinyMCE.init({
				license_key: '<?= js_escape($this->licenseKey); ?>',
				selector: 'textarea[data-editor="tinymce"]',
				width: 800,
				height: 300,
				entity_encoding: 'raw',
				language: '<?= $language; ?>',
//				plugins: 'image link',
				toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | link image help',

				plugins: "advlist,autolink,lists,link,image,imagetools,charmap,print,preview,hr,anchor,pagebreak" +
					",searchreplace,wordcount,visualblocks,visualchars,code,fullscreen" +
					",media,nonbreaking,save,table,directionality" +
					",emoticons,paste,quickupload,localautosave,help",
				// "advlist,autolink,lists,link,charmap,print,preview,hr,anchor,pagebreak" +
				//  ",searchreplace,wordcount,visualblocks,visualchars,code,fullscreen" +
				//  ",nonbreaking,save,table,contextmenu,directionality" +
				//  ",emoticons,paste,textcolor",  //image,imagetools.media not available at this time
				browser_spellcheck: true,
				entity_encoding: 'raw',
				relative_urls : false,
				convert_urls: false,
				remove_script_host : true,
				document_base_url : '<?php echo $this->rpath; ?>',
				end_container_on_empty_block: true,

			//	image_advtab: true,
			//	image_title: true,
			// 	image_caption: true,

				// file_picker_callback: mceElf.browser,
				// images_upload_handler: mceElf.uploadHandler

				//file_picker_callback: function expBrowser (callback, value, meta) {
				//	tinymce.activeEditor.windowManager.open({
				//		file: '<?php //echo makelink(array("controller" => "file", "action" => "picker", "ajax_action" => 1, "update" => "tiny")); ?>//?filter='+meta.filetype,
				//		title: '<?php //echo gt('File Manager'); ?>//',
				//		width: <?php //echo FM_WIDTH ?>//,
				//		height: <?php //echo FM_HEIGHT ?>//,
				//		resizable: 'yes'
				//	}, {oninsert: function (url, alt, title) {
				//			// Provide file and text for the link dialog
				//			if (meta.filetype == 'file')
				//				callback(url, {text: alt, title: title});
				//			// Provide image and alt text for the image dialog
				//			if (meta.filetype == 'image')
				//				callback(url, {alt: alt});
				//			// Provide alternative source and posted for the media dialog
				//			if (meta.filetype == 'media')
				//				callback(url);
				//		}
				//	});
				//	return false;
				//},

			});
		</script>

		<?php
		return null;
	}

	public function getFieldInput($table, array $field, $attrs, $value, $function)
	{
		if (str_contains($field["type"], "text") && str_contains($field["field"], "body")) {
			return "<textarea $attrs cols='30' rows='12' data-editor='tinymce' style='width: 800px; height: 300px;'>" . h($value) . "</textarea>";
		}

		return null;
	}
}
