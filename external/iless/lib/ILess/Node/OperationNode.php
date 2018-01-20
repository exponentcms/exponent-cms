<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Node;

use ILess\Context;
use ILess\Exception\CompilerException;
use ILess\Node;
use ILess\Output\OutputInterface;
use ILess\Visitor\VisitorInterface;

/**
 * Operation node.
 */
class OperationNode extends Node
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Operation';

    /**
     * Operator.
     *
     * @var string
     */
    protected $operator;

    /**
     * Array of operands.
     *
     * @var array
     */
    protected $operands;

    /**
     * Is spaced flag.
     *
     * @var bool
     */
    public $isSpaced = false;

    /**
     * Parens.
     *
     * @var bool
     */
    public $parensInOp = false;

    /**
     * Constructor.
     *
     * @param string $operator The operator
     * @param array $operands Array of operands
     * @param bool $isSpaced Is spaced?
     */
    public function __construct($operator, array $operands, $isSpaced = false)
    {
        $this->operator = trim($operator);
        $this->operands = $operands;
        $this->isSpaced = $isSpaced;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $this->operands = $visitor->visit($this->operands);
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @throws CompilerException
     *
     * @return Node
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        $a = $this->operands[0]->compile($context);
        $b = $this->operands[1]->compile($context);

        if ($context->isMathOn()) {
            if ($a instanceof DimensionNode && $b instanceof ColorNode) {
                $a = $a->toColor();
            }

            if ($b instanceof DimensionNode && $a instanceof ColorNode) {
                $b = $b->toColor();
            }

            if (!self::methodExists($a, 'operate')) {
                throw new CompilerException('Operation on an invalid type.');
            }

            return $a->operate($context, $this->operator, $b);
        } else {
            return new self($this->operator, [$a, $b], $this->isSpaced);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $this->operands[0]->generateCSS($context, $output);

        if ($this->isSpaced) {
            $output->add(' ');
        }

        $output->add($this->operator);

        if ($this->isSpaced) {
            $output->add(' ');
        }

        $this->operands[1]->generateCSS($context, $output);
    }
}
