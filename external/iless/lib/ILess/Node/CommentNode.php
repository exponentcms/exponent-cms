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

/**
 * Comment.
 */
class CommentNode extends Node implements MarkableAsReferencedInterface
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Comment';

    /**
     * Current index.
     *
     * @var int
     */
    public $index = 0;

    /**
     * Is line comment?
     *
     * @var bool
     */
    protected $isLineComment = false;

    /**
     * Reference flag.
     *
     * @var bool
     */
    protected $isReferenced = false;

    /**
     * Constructor.
     *
     * @param string $value The comment value
     * @param bool $isLineComment
     */
    public function __construct($value, $isLineComment = false, $index = 0, FileInfo $currentFileInfo = null)
    {
        parent::__construct($value);
        $this->isLineComment = (boolean) $isLineComment;
        $this->index = $index;
        $this->currentFileInfo = $currentFileInfo;
    }

    /**
     * Is the comment silent?
     *
     * @param Context $context
     *
     * @return bool
     */
    public function isSilent(Context $context)
    {
        $isReference = $this->currentFileInfo && $this->currentFileInfo->reference && !$this->isReferenced;
        $isCompressed = $context->compress && !preg_match('/^\/\*!/', $this->value);

        return $this->isLineComment || $isReference || $isCompressed;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        if ($this->debugInfo) {
            $output->add(self::getDebugInfo($context, $this), $this->currentFileInfo, $this->index);
        }
        $output->add($this->value);
    }

    /**
     * Mark the comment as referenced.
     */
    public function markReferenced()
    {
        $this->isReferenced = true;
    }
}
