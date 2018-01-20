<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Node;

use ILess\Context;
use ILess\Node;
use ILess\Output\OutputInterface;

/**
 * Attribute.
 */
class AttributeNode extends Node
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Attribute';

    /**
     * The key.
     *
     * @var string|Node
     */
    public $key;

    /**
     * The operator.
     *
     * @var string
     */
    public $operator;

    /**
     * Constructor.
     *
     * @param string|Node $key
     * @param string $operator
     * @param string|Node $value
     */
    public function __construct($key, $operator, $value)
    {
        $this->key = $key;
        $this->operator = $operator;
        parent::__construct($value);
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return AttributeNode
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        return new self(
            $this->key instanceof CompilableInterface ? $this->key->compile($context) : $this->key,
            $this->operator,
            $this->value instanceof CompilableInterface ? $this->value->compile($context) : $this->value
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $output->add($this->toCSS($context));
    }

    /**
     * {@inheritdoc}
     */
    public function toCSS(Context $context)
    {
        $value = $this->key instanceof GenerateCSSInterface ? $this->key->toCSS($context) : $this->key;

        if ($this->operator) {
            $value .= $this->operator;
            $value .= ($this->value instanceof GenerateCSSInterface ? $this->value->toCSS($context) : $this->value);
        }

        return '[' . $value . ']';
    }
}
