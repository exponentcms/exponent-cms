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
 * Assignment.
 */
class AssignmentNode extends Node
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Assignment';

    /**
     * The assignment key.
     *
     * @var string
     */
    private $key;

    /**
     * Constructor.
     *
     * @param string $key
     * @param string|Node $value
     */
    public function __construct($key, $value)
    {
        parent::__construct($value);
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $output->add($this->key . '=');
        if ($this->value instanceof GenerateCSSInterface) {
            $this->value->generateCSS($context, $output);
        } else {
            $output->add($this->value);
        }
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return AssignmentNode
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        if ($this->value instanceof CompilableInterface) {
            return new self($this->key, $this->value->compile($context));
        }

        return $this;
    }
}
