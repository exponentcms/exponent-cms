<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess;

use ILess\Node\AnonymousNode;
use ILess\Node\ColorNode;
use ILess\Node\DimensionNode;
use ILess\Node\ValueNode;
use ILess\Node\RuleNode;
use ILess\Node\QuotedNode;

/**
 * Variable represents custom variable passed by the API (not from less string or file).
 */
class Variable
{
    /**
     * Dimension detection regexp.
     */
    const DIMENSION_REGEXP = '/^([+-]?\d*\.?\d+)(%|[a-z]+)?$/';

    /**
     * Quoted detection regexp.
     */
    const QUOTED_REGEXP = '/^"((?:[^"\\\\\r\n]|\\\\.)*)"|\'((?:[^\'\\\\\r\n]|\\\\.)*)\'$/';

    /**
     * RGBA detection regexp.
     */
    const RGBA_COLOR_REGEXP = '/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/';

    /**
     * Important flag.
     *
     * @var bool
     */
    protected $important = false;

    /**
     * The name.
     *
     * @var string
     */
    protected $name;

    /**
     * The value.
     *
     * @var Node
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param string $name The name of the variable
     * @param Node $value The value of the variable
     * @param bool $important Important?
     */
    public function __construct($name, Node $value, $important = false)
    {
        $this->name = ltrim($name, '@');
        $this->value = $value;
        $this->important = (boolean) $important;
    }

    /**
     * Creates the variable. Detects the type.
     *
     * @param string $name The name of the variable
     * @param mixed $value The value of the variable
     *
     * @return Variable
     */
    public static function create($name, $value)
    {
        $important = false;
        // name is marked as !name
        if (strpos($name, '!') === 0) {
            $important = true;
            $name = substr($name, 1);
        }

        // Color
        if (Color::isNamedColor($value) || strtolower($value) === 'transparent' || strpos($value, '#') === 0) {
            $value = new ColorNode(new Color($value));
        } elseif (preg_match(self::RGBA_COLOR_REGEXP, $value, $matches)) { // RGB(A) colors
            $value = new ColorNode(new Color([
                $matches[1],
                $matches[2],
                $matches[3],
            ], isset($matches[4]) ? $matches[4] : 1));
        } // Quoted string
        elseif (preg_match(self::QUOTED_REGEXP, $value, $matches)) {
            $value = new QuotedNode($matches[0], $matches[0][0] == '"' ? $matches[1] : $matches[2]);
        } // URL
        elseif (strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0) {
            $value = new AnonymousNode($value);
        } // Dimension
        elseif (preg_match(self::DIMENSION_REGEXP, $value, $matches)) {
            $value = new DimensionNode($matches[1], isset($matches[2]) ? $matches[2] : null);
        } // everything else
        else {
            $value = new AnonymousNode($value);
        }

        return new self($name, $value, $important);
    }

    /**
     * Converts the variable to the node.
     *
     * @return RuleNode
     */
    public function toNode()
    {
        return new RuleNode('@' . $this->name, new ValueNode([
            $this->value,
        ]), $this->important ? '!important' : '');
    }

    /**
     * Returns the variable name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the variable value.
     *
     * @return Node
     */
    public function getValue()
    {
        return $this->value;
    }
}
