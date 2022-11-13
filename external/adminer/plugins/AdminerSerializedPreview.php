<?php

/**
 * Displays Serialized preview as a table.
 *
 * Based on AdminerJsonPreview
 * @author Peter Knut
 * @copyright 2014-2018 Pematon, s.r.o. (http://www.pematon.com/)
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
                font-size: 110%;
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
			a.serialize-icon {
				display: inline-block;
				padding: 0;
				overflow: hidden;
                background-image: url("<?php echo ME; ?>file=down.gif");
				background-position: center center;
				background-repeat: no-repeat;
				text-indent: -50px;
				vertical-align: middle;
			}

			a.serialize-link {
				width: auto;
				padding-left: 18px;
				background-position: left center;
				text-indent: 0;
			}

			a.serialize-link span {
				color: #fff;
				padding: 0 5px;
			}

			a.serialize-icon.serialize-up {
                background-image: url("<?php echo ME; ?>file=up.gif");
			}

			/* No javascript support */
			.nojs .serialize-icon, .nojs .serialize-link {
				display: none;
			}

			.nojs .json {
				display: table !important;
			}
		</style>

		<script <?php echo nonce(); ?>>
            (function(document) {
                "use strict";

                document.addEventListener("DOMContentLoaded", init, false);

                function init() {
                    var links = document.querySelectorAll('a.serialize-icon');

                    for (var i = 0; i < links.length; i++) {
                        links[i].addEventListener("click", function(event) {
                            event.preventDefault();
                            toggleSerial(this);
                        }, false);
                    }
                }

                function toggleSerial(button) {
                    var index = button.dataset.index;

                    var obj = document.getElementById("serialize-code-" + index);
				if (!obj)
					return;

				if (obj.style.display === "none") {
					button.className += " serialize-up";
					obj.style.display = "";
				} else {
					button.className = button.className.replace(" serialize-up", "");
					obj.style.display = "none";
				}
			}
            })(document);
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
			$val = "<a class='icon serialize-icon' href='#' title='Serialized Data' data-index='$counter'>Serialized</a> " . $val;
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
			echo "<a class='icon serialize-icon serialize-link' href='#' title='Serialized Data' data-index='$counter'><span>Serialized</span></a><br/>";
			echo $this->convertSerialized($this->expUnserialize($value), 1, $counter++);
		}
	}

	public function convertSerialized($json, $level = 1, $id = 0)
	{
		$value = "";

		$value .= "<table class='json'";
		if ($level === 1 && $id > 0) {
			$value .= "style='display: none' id='serialize-code-$id'";
		}
		$value .= ">";
        if (is_object($json)) {
            $value .= "<tr><th colspan='2'><code>" . h(get_class($json)) . " Object</code></th></tr>";
        }

		if (!empty($json)) foreach ($json as $key => $val) {
			$value .= "<tr><th><code>" . h($key) . "</code>";
			$value .= "<td>";

			if ((is_array($val) || is_object($val)) && ($this->maxLevel <= 0 || $level < $this->maxLevel)) {
				$value .= $this->convertSerialized($val, $level + 1);
			} elseif (is_array($val)) {
				$value .= "<code class='jush-js'>" . h(preg_replace('/([,:])([^\s])/', '$1 $2', json_encode($val))) . "</code>";
			} elseif (is_string($val)) {
                if (!empty($val) && $this->is_json($val)) {
                    $val = json_decode(str_replace('\"', '"', $val));
                    // it is now a nested object
                    $value .= $this->convertSerialized($val, $level + 1);
                } else {
                    // Shorten string to max. length.
                    if (mb_strlen($val, "UTF-8") > self::MAX_TEXT_LENGTH) {
                        $val = mb_substr($val, 0, self::MAX_TEXT_LENGTH - 3, "UTF-8") . "...";
                    }

                    // Add extra new line to make it visible in HTML output.
    				if (preg_match("@\n$@", $val)) {
                        $val .= "\n";
                    }

                    $value .= "<code>" . nl2br(h($val)) . "</code>";
                }
			} elseif (is_bool($val)) {
				// Handle boolean values.
				$value .= "<code class='jush'>" . h($val ? "true" : "false") . "</code>";
			} elseif (is_null($val)) {
				// Handle null value.
				$value .= "<code class='jush'>null</code>";
            } elseif (is_object($val) || is_array($val)) {
			    //fixme here is a deep nested object/array
                $value .= "<code class='jush'>" . h(serialize($val)) . "</code>";
            } else {
                $value .= "<code class='jush'>" . h($val) . "</code>";
			}
		}

		$value .= "</table>";

		return $value;
	}

    public function is_serialized($data)
    {
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($m) {
                $m_new = str_replace('"','\"',$m[2]);
                return "s:".strlen($m_new).':"'.$m_new.'";';
            }, $data );
        return (@unserialize($out) !== false);
    }

    public function is_json($data)
    {
        return (is_object(@json_decode(str_replace('\"', '"', $data))));
    }

    public function expUnserialize($serial_str) {
        if ($serial_str === 'Array' || is_null($serial_str))
            return null;  // empty array string??
        if (is_array($serial_str) || is_object($serial_str))
            return $serial_str;  // already unserialized
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($m) {
                $m_new = str_replace('"','\"',$m[2]);
                return "s:".strlen($m_new).':"'.$m_new.'";';
            }, $serial_str );
        $out2 = @unserialize($out);
        // list of fields with links requiring cleaning
        $stripList = array(
            'moduledescription',
            'description',
            'report_desc',
            'report_def',
            'report_def_showall',
            'response',
            'auto_respond_body'
        );
        if (is_array($out2)) {
            foreach ($stripList as $strip) {
                if (!empty($out2[$strip])) {  // work-around for links in rich text
                    $out2[$strip] = stripslashes($out2[$strip]);
                }
            }
        } elseif (is_object($out2) && $out2 instanceof \htmlcontrol) {
            $out2->html = stripslashes($out2->html);
        }
        if ($out2 === false && !empty($out)) {
            $out2 = $out;
        }
        return $out2;
    }
}
