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
use ILess\Visitor\VisitorInterface;

/**
 * Value.
 */
class ValueNode extends Node
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Value';

    /**
     * Constructor.
     *
     * @param array $value Array of value
     */
    public function __construct(array $value)
    {
        parent::__construct($value);
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $this->value = $visitor->visitArray($this->value);
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return ValueNode
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        if (count($this->value) == 1) {
            return $this->value[0]->compile($context);
        }

        $return = [];
        foreach ($this->value as $v) {
            $return[] = $v->compile($context);
        }

        return new self($return);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        for ($i = 0, $count = count($this->value); $i < $count; ++$i) {
            $this->value[$i]->generateCSS($context, $output);
            if ($i + 1 < $count) {
                $output->add($context->compress ? ',' : ', ');
            }
        }
    }
}
