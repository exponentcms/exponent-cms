<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Node;

use ILess\Context;
use ILess\FileInfo;
use ILess\Node;
use ILess\Output\OutputInterface;
use ILess\Visitor\VisitorInterface;

/**
 * Element.
 */
class ElementNode extends Node
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Element';

    /**
     * ILess\Node combinator.
     *
     * @var CombinatorNode
     */
    public $combinator;

    /**
     * The current index.
     *
     * @var int
     */
    public $index = 0;

    /**
     * Constructor.
     *
     * @param CombinatorNode|string $combinator The combinator
     * @param string|Node $value The value
     * @param int $index The current index
     * @param FileInfo $currentFileInfo Current file information
     */
    public function __construct($combinator, $value, $index = 0, FileInfo $currentFileInfo = null)
    {
        $this->combinator = $combinator instanceof CombinatorNode ? $combinator : new CombinatorNode($combinator);

        if (is_string($value)) {
            $value = trim($value);
        } elseif (!$value) {
            $value = '';
        }

        parent::__construct($value);

        $this->index = $index;
        $this->currentFileInfo = $currentFileInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $this->combinator = $visitor->visit($this->combinator);
        if (is_object($this->value)) {
            $this->value = $visitor->visit($this->value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $output->add($this->toCSS($context), $this->currentFileInfo, $this->index);
    }

    /**
     * Convert to CSS.
     *
     * @param Context $context The context
     *
     * @return string
     */
    public function toCSS(Context $context)
    {
        $firstSelector = $context->firstSelector;
        if ($this->value instanceof ParenNode) {
            $context->firstSelector = true;
        }

        $value = $this->value instanceof GenerateCSSInterface ? $this->value->toCSS($context) : $this->value;

        $context->firstSelector = $firstSelector;
        if ($value === '' && strlen($this->combinator->value) && $this->combinator->value[0] === '&') {
            return '';
        } else {
            return $this->combinator->toCSS($context) . $value;
        }
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return ElementNode
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        return new self($this->combinator,
            $this->value instanceof CompilableInterface ? $this->value->compile($context) : $this->value,
            $this->index, $this->currentFileInfo
        );
    }
}
