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
use ILess\Util;
use ILess\Visitor\VisitorInterface;

/**
 * Condition.
 */
class ConditionNode extends Node
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Condition';

    /**
     * The operator.
     *
     * @var string
     */
    private $op;

    /**
     * The left operand.
     *
     * @var Node
     */
    private $lvalue;

    /**
     * The right operand.
     *
     * @var Node
     */
    private $rvalue;

    /**
     * Current index.
     *
     * @var int
     */
    private $index = 0;

    /**
     * Negate the result?
     *
     * @var bool
     */
    private $negate = false;

    /**
     * Constructor.
     *
     * @param string $op The operator
     * @param Node $l The left operand
     * @param Node $r The right operand
     * @param int $i
     * @param bool $negate
     */
    public function __construct($op, Node $l, Node $r, $i = 0, $negate = false)
    {
        $this->op = trim($op);
        $this->lvalue = $l;
        $this->rvalue = $r;
        $this->index = $i;
        $this->negate = (boolean) $negate;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $this->lvalue = $visitor->visit($this->lvalue);
        $this->rvalue = $visitor->visit($this->rvalue);
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return bool
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        $a = $this->lvalue->compile($context);
        $b = $this->rvalue->compile($context);

        switch ($this->op) {
            case 'and':
                $result = $a && $b;
                break;
            case 'or':
                $result = $a || $b;
                break;
            default:
                $compared = Util::compareNodes($a, $b);
                // strict comparison, we cannot use switch here
                if ($compared === -1) {
                    $result = $this->op === '<' || $this->op === '=<' || $this->op === '<=';
                } elseif ($compared === 0) {
                    $result = $this->op === '=' || $this->op === '>=' || $this->op === '=<' || $this->op === '<=';
                } elseif ($compared === 1) {
                    $result = $this->op === '>' || $this->op === '>=' || $this->op === '=>';
                } else {
                    $result = false;
                }
                break;
        }

        return $this->negate ? !$result : $result;
    }
}
