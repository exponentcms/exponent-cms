<?php

namespace AdminNeo;

/**
 * Displays values selection for fields with a foreign key in edit form.
 *
 * Selection will be displayed only if the number of foreign values will not exceed the given limit.
 *
 * Last changed in release: v5.2.0
 *
 * @link https://www.adminneo.org/plugins/#usage
 *
 * @author Jakub Vrana, https://www.vrana.cz/
 * @author Peter Knut
 *
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class ForeignEditPlugin extends Plugin
{
	protected $limit;

	private $foreignTables = [];
	private $foreignOptions = [];

	/**
	 * @param int $limit Max. number of foreign values.
	 */
	public function __construct($limit = 200)
	{
		$this->limit = $limit;
	}

	public function getFieldInput($table, array $field, $attrs, $value, $function)
	{
		if (!$table) {
			return null;
		}

		if (!isset($this->foreignTables[$table])) {
			$this->foreignTables[$table] = column_foreign_keys($table);
		}
		$foreignKeys = $this->foreignTables[$table];

		if (!isset($foreignKeys[$field["field"]])) {
			return null;
		}

		foreach ($foreignKeys[$field["field"]] as $foreignKey) {
			if (count($foreignKey["source"]) != 1) {
				continue;
			}

			$target = $foreignKey["table"];
			$id = $foreignKey["target"][0];

			if (!isset($this->foreignOptions[$target][$id])) {
				$column = idf_escape($id);
				if (preg_match('~binary~', $field["type"])) {
					$column = "HEX($column)";
				}

				$values = get_vals("SELECT $column FROM " . table($target) . " ORDER BY 1 LIMIT " . ($this->limit + 1));

				if (count($values) > $this->limit) {
					$this->foreignOptions[$target][$id] = false;
				} else {
					$this->foreignOptions[$target][$id] = ["" => ""] + $values;
				}
			}

			if ($options = $this->foreignOptions[$target][$id]) {
				return "<select $attrs>" . optionlist($options, $value) . "</select>";
			} else {
				return null;
			}
		}

		return null;
	}
}
