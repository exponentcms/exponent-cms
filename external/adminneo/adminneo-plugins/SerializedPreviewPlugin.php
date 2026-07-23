<?php

namespace AdminNeo;

/**
 * Displays Serialized preview as a table and hover text.
 *
 * Last changed in release: v5.2.0
 *
 * @author Peter Knut
 *
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class SerializedPreviewPlugin extends Plugin
{
	/** @var bool */
	protected $inTable;

	/** @var bool */
	protected $inEdit;

	/** @var int */
	protected $maxLevel;

	/** @var int */
	protected $maxTextLength = 100;

	/** @var string */
	protected $linkIdBase;

	/** @var int */
	protected $counter = 1;

	/**
	 * @param bool $inTable Whether apply Serialized preview in data table.
	 * @param bool $inEdit Whether apply Serialized preview in edit form.
	 * @param int $maxLevel Max. level in recursion.
	 * @param int $maxTextLength Maximal length of string values. Longer texts will be truncated with ellipsis sign '…'.
	 */
	public function __construct($inTable = true, $inEdit = true, $maxLevel = 0, $maxTextLength = 100)
	{
		$this->inTable = $inTable;
		$this->inEdit = $inEdit;
		$this->maxLevel = $maxLevel;
		$this->maxTextLength = $maxTextLength;

		$this->linkIdBase = (string)microtime(true);
	}

	public function inject($admin, Config $config, Settings $settings, Locale $locale)
	{
		parent::inject($admin, $config, $settings, $locale);

		$param = $settings->getParameter("jsonPreview");
		if ($param) {
			$this->inTable = in_array("table", $param);
			$this->inEdit = in_array("edit", $param);
		}
	}

	/**
	 * Prints HTML code inside <head>.
	 */
	public function printToHead()
	{
		?>

		<style>
			/* Table */
			.json {
				width: auto;
				margin: 4px 0;
				border-color: var(--code-border);
				border-left: 7px solid var(--code-border);
				background-color: var(--code-bg);
			}

			.json th {
				padding: 0;
				width: 1px;
				background-color: transparent;
				border-color: var(--code-border);
			}

			.json td {
				padding: 0;
				border-color: var(--code-border);
			}

			.json code {
				display: block;
				background: transparent;
				padding: 3px 7px;
				white-space: normal;
			}

			.json .json {
				display: table;
				width: 100%;
				border: none;
				margin: 0;
			}

			.json + textarea {
				margin-top: 3px;
			}
		</style>

		<?php
	}

	public function formatSelectionValue($val, $link, $field, $original)
	{
		if (!$field || !$this->inTable) {
			return null;
		}

		if (is_string($original) && $this->is_serialized($original)) {
			$out = "";
			$out .= '<div title="'.htmlentities(print_r($this->expUnserialize(html_entity_decode($original)),true)).'">';
			$out .= "<a class='toggle jsonly' href='#json-code-s$this->linkIdBase-$this->counter' title='Serialized Text' data-value='" . h($val) . "'>" . icon_chevron_right() . "</a>" .
					" <code class='jush-json'>$val</code>";
			$out .= $this->convertSerialized($this->expUnserialize($original), 1, $this->counter++);
			$out .= '</div>';
			return $out;
		}
	}

	public function getFieldInput($table, array $field, $attrs, $value, $function)
	{
		if (!$this->inEdit) {
			return null;
		}

		if (is_string($value) && $this->is_serialized($value)) {
			$out = "<div class='jsonly' title='" . htmlentities(print_r($this->expUnserialize($value),true)) . "'><a class='toggle' href='#json-code-s$this->linkIdBase-$this->counter'>Serialized" . icon_chevron_down() . "</a></div>";
			$out .= $this->convertSerialized($this->expUnserialize($value), 1, $this->counter++);
			$out .= "<textarea class='jush-json' title=\"" . htmlentities(print_r($this->expUnserialize($value),true)) . "\" cols='50' rows='12'$attrs>" . h($value) . '</textarea>';
//			$out .= "</div>";
			return $out;
		}
	}

	public function convertSerialized($json, $level = 1, $counter = 0)
	{
		$value = "<table class='json hidden'" . ($counter && $level == 1 ? " id='json-code-s$this->linkIdBase-$counter'" : "") . ">";

        if (is_object($json)) {
            $value .= "<tr><th colspan='2'><code>" . h(get_class($json)) . " Object</code></th></tr>";
        }

		if (!empty($json)) foreach ($json as $key => $val) {
			$value .= "<tr><th><code>" . h($key) . "</code>";
			$value .= "<td>";

			if ((is_array($val) || is_object($val)) && ($this->maxLevel <= 0 || $level < $this->maxLevel)) {
				$value .= $this->convertSerialized($val, $level + 1);
			} elseif (is_array($val)) {
				$value .= "<code class='jush-json'>" . h(preg_replace('/([,:])([^\s])/', '$1 $2', json_encode($val))) . "</code>";
			} elseif (is_string($val)) {
                if (!empty($val) && $this->is_json($val)) {
                    $val = json_decode(str_replace('\"', '"', $val));
                    // it is now a nested object
                    $value .= $this->convertSerialized($val, $level + 1);
                } else {
                    // Shorten string to max. length.
                    if (mb_strlen($val, "UTF-8") > $this->maxTextLength) {
                        $val = mb_substr($val, 0, $this->maxTextLength - 3, "UTF-8") . "...";
                    }

                    // Add extra new line to make it visible in HTML output.
    				if (preg_match("@\n$@", $val)) {
                        $val .= "\n";
                    }
					if (is_numeric($val) || empty($val)) {
						$value .= "<code class='jush-json'>" . nl2br($val) . "</code>";
					} else {
						$value .= "<code class='jush-json'>'" . nl2br($val) . "'</code>";
					}
                }
			} elseif (is_bool($val)) {
				// Handle boolean values.
				$value .= "<code class='jush-json'>" . h($val ? "true" : "false") . "</code>";
			} elseif (is_null($val)) {
				// Handle null value.
				$value .= "<code class='jush-json'>null</code>";
            } elseif (is_object($val) || is_array($val)) {
			    //fixme here is a deep nested object/array
                $value .= "<code class='jush-json'>" . serialize($val) . "</code>";
            } else {
                $value .= "<code class='jush-json'>" . $val . "</code>";
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
	//    $out1 = @preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
	//    $out1 = preg_replace_callback(
	//        '!s:(\d+):"(.*?)";!s',
	//        create_function ('$m',
	//            '$m_new = str_replace(\'"\',\'\"\',$m[2]);
	//            return "s:".strlen($m_new).\':"\'.$m_new.\'";\';'
	//        ),
	//        $serial_str );
	    $out = preg_replace_callback(
	        '!s:(\d+):"(.*?)";!s',
	        function ($m) {
	            $m_new = str_replace('"','\"',$m[2]);
	            return "s:".strlen($m_new).':"'.$m_new.'";';
	        }, $serial_str );
	//    if ($out1 !== $out) {
	//        eDebug('problem:<br>'.$out.'<br>'.$out1);
	//    }
	    $out2 = @unserialize($out);
	    // list of fields with rich text
	    $stripList = array(
	        'moduledescription',
	        'description',
	        'report_desc',
	        'report_def',
	        'report_def_showall',
	        'response',
	        'auto_respond_body',
	        'ecomheader',
	        'ecomfooter',
	        'cart_description_text',
	        'policy',
	        'checkout_message_top',
	        'checkout_message_bottom',
	        'message'
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
