<?php

/**
 * Displays Serialized preview as a table.
 *
 * Based on AdminerJsonPreview
 * @author Peter Knut
 * @copyright 2014-2015 Pematon, s.r.o. (http://www.pematon.com/)
 */
class AdminerSerializedPreview
{
	const MAX_TEXT_LENGTH = 100;

	/** @var int */
	private $maxLevel;

	/** @var bool */
	private $inTable;

	/** @var bool */
	private $inEdit;

	/**
	 * @param int $maxLevel Max. level in recursion. 0 means no limit.
	 * @param bool $inTable Whether apply JSON preview in selection table.
	 * @param bool $inEdit Whether apply JSON preview in edit form.
	 */
	public function __construct($maxLevel = 0, $inTable = true, $inEdit = true)
	{
		$this->maxLevel = $maxLevel;
		$this->inTable = $inTable;
		$this->inEdit = $inEdit;
	}

	/**
	 * Prints HTML code inside <head>.
	 */
	public function head()
	{
		?>

		<style>
			/* Table */
			.json {
				width: auto;
				border-collapse: collapse;
				border-spacing: 0;
				margin: 4px 0;
				border: 1px solid #999;
			}

			.json tr {
				border-bottom: 1px solid #999;
			}

			.json tr:last-child {
				border-bottom: none;
			}

			.checkable .json .checked th, .checkable .json .checked td {
				background: transparent;
			}

			.json th {
				padding: 0;
				width: 1px;
				border-right: 1px solid #999;
				border-bottom: none;
			}

			.json td {
				padding: 0;
				border: 0;
			}

			.json code {
				display: block;
				background: transparent;
				padding: 2px 3px;
				white-space: normal;
			}

			.json .json {
				width: 100%;
				border: none;
				margin: 0;
			}

			/* Togglers */
			a.json-icon {
				display: inline-block;
				padding: 0;
				overflow: hidden;
				background-image: url("index.php?file=down.gif");
				background-position: center center;
				background-repeat: no-repeat;
				text-indent: -50px;
				vertical-align: middle;
			}

			a.json-link {
				width: auto;
				padding-left: 18px;
				background-position: left center;
				text-indent: 0;
			}

			a.json-link span {
				color: #fff;
				padding: 0 5px;
			}

			a.json-icon.json-up {
				background-image: url("index.php?file=up.gif");
			}

			/* No javascript support */
			.nojs .json-icon, .nojs .json-link {
				display: none;
			}

			.nojs .json {
				display: table !important;
			}
		</style>

		<script>
			function toggleJson(button, counter) {
				var obj = document.getElementById("json-code-" + counter);
				if (!obj)
					return;

				if (obj.style.display === "none") {
					button.className += " json-up";
					obj.style.display = "";
				} else {
					button.className = button.className.replace(" json-up", "");
					obj.style.display = "none";
				}
			}
		</script>

		<?php
	}

	public function selectVal(&$val, $link, $field, $original)
	{
		static $counter = 1;

		if (!$this->inTable) {
			return;
		}

		if (is_string($original) && $this->is_serialized($original)) {
			$val = "<a class='icon json-icon' href='#' onclick='toggleJson(this, $counter);return false;' title='Serialized Data'>Serialized</a> " . $val;
			$val .= $this->convertSerialized($this->expUnserialize($original), 1, $counter++);
		}
	}

	public function editInput($table, $field, $attrs, $value)
	{
		static $counter = 1;

		if (!$this->inEdit) {
			return;
		}

		if (is_string($value) && $this->is_serialized($value)) {
			echo "<a class='icon json-icon json-link' href='#' onclick='toggleJson(this, $counter);return false;' title='Serialized Data'><span>Serialized</span></a><br/>";
			echo $this->convertSerialized($this->expUnserialize($value), 1, $counter);
		}
	}

	public function convertSerialized($json, $level = 1, $id = 0)
	{
		$value = "";

		$value .= "<table class='json'";
		if ($level === 1 && $id > 0) {
			$value .= "style='display: none' id='json-code-$id'";
		}
		$value .= ">";

		if (!empty($json)) foreach ($json as $key => $val) {
			$value .= "<tr><th><code>" . h($key) . "</code>";
			$value .= "<td>";

			if (is_array($val) && ($this->maxLevel <= 0 || $level < $this->maxLevel)) {
				$value .= $this->convertSerialized($val, $level + 1);
			} elseif (is_array($val)) {
				$value .= "<code class='jush-js'>" . h(preg_replace('/([,:])([^\s])/', '$1 $2', json_encode($val))) . "</code>";
			} elseif (is_string($val)) {
				// Shorten string to max. length.
				if (mb_strlen($val, "UTF-8") > self::MAX_TEXT_LENGTH)
					$val = mb_substr($val, 0, self::MAX_TEXT_LENGTH - 3, "UTF-8") . "...";

				// Add extra new line to make it visible in HTML output.
				if (preg_match("@\n$@", $val))
					$val .= "\n";

				$value .= "<code>" . nl2br(h($val)) . "</code>";
			} elseif (is_bool($val)) {
				// Handle boolean values.
				$value .= "<code class='jush'>" . h($val ? "true" : "false") . "</code>";
			} elseif (is_null($val)) {
				// Handle null value.
				$value .= "<code class='jush'>null</code>";
			} else {
				$value .= "<code class='jush'>" . h($val) . "</code>";
			}
		}

		$value .= "</table>";

		return $value;
	}

    public function is_serialized($data)
    {
        return (@unserialize($data) !== false);
    }

    public function expUnserialize($serial_str)
    {
        if ($serial_str === 'Array') return null;  // empty array string??
        if (is_array($serial_str) || is_object($serial_str)) return $serial_str;  // already unserialized
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            create_function('$m',
                '$m_new = str_replace(\'"\',\'\"\',$m[2]);
              return "s:".strlen($m_new).\':"\'.$m_new.\'";\';'
            ),
            $serial_str);
        $out2 = unserialize($out);
        if (is_array($out2)) {
            if (!empty($out2['moduledescription'])) {  // work-around for links in module descriptions
                $out2['moduledescription'] = stripslashes($out2['moduledescription']);
            }
            if (!empty($out2['description'])) {  // work-around for links in forms descriptions
                $out2['description'] = stripslashes($out2['description']);
            }
            if (!empty($out2['report_desc'])) {  // work-around for links in forms report descriptions
                $out2['report_desc'] = stripslashes($out2['report_desc']);
            }
            if (!empty($out2['response'])) {  // work-around for links in forms response
                $out2['response'] = stripslashes($out2['response']);
            }
            if (!empty($out2['auto_respond_body'])) {  // work-around for links in forms auto respond
                $out2['auto_respond_body'] = stripslashes($out2['auto_respond_body']);
            }
        } elseif (is_object($out2) && get_class($out2) == 'htmlcontrol') {
            $out2->html = stripslashes($out2->html);
        }
        return $out2;
    }
}
