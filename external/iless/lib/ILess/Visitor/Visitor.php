<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Visitor;

use ILess\Node;
use ILess\Node\VisitableInterface;

/**
 * Visitor base class.
 */
abstract class Visitor implements VisitorInterface
{
    /**
     * Is the visitor replacing?
     *
     * @var bool
     */
    protected $isReplacing = false;

    /**
     * Method cache.
     *
     * @var array
     */
    private $methodCache = [];

    /**
     * @var string
     */
    protected $type = VisitorInterface::TYPE_POST_COMPILE;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // prepare the method cache to speed up the visitors
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'visit') === 0) {
                $this->methodCache[$method] = true;
            }
        }
    }

    /**
     * Runs the visitor.
     *
     * @param ILess\Node|array
     *
     * @return mixed
     */
    public function run($root)
    {
        return $this->visit($root);
    }

    /**
     * Is the visitor replacing?
     *
     * @return bool
     */
    public function isReplacing()
    {
        return $this->isReplacing;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Visits a node or an array of nodes.
     *
     * @param Node|array|string|null $node The node to visit
     *
     * @return mixed The visited node
     */
    public function visit($node)
    {
        if (is_array($node)) {
            return $this->visitArray($node);
        }

        if (!($node instanceof VisitableInterface)) {
            return $node;
        }

        if (($type = $node->getType()) && ($funcName = 'visit' . $type) && isset($this->methodCache[$funcName])) {
            $arguments = new VisitorArguments([
                'visitDeeper' => true,
            ]);

            $newNode = $this->$funcName($node, $arguments);

            if ($this->isReplacing()) {
                $node = $newNode;
            }

            if ($arguments->visitDeeper && $node instanceof VisitableInterface) {
                $node->accept($this);
            }

            $funcName .= 'Out';

            if (isset($this->methodCache[$funcName])) {
                $this->$funcName($node, isset($arguments) ? $arguments : new VisitorArguments());
            }
        } else {
            $node->accept($this);
        }

        return $node;
    }

    /**
     * Visits an array of nodes.
     *
     * @param array $nodes Array of nodes
     * @param bool $nonReplacing
     *
     * @return array
     */
    public function visitArray(array $nodes, $nonReplacing = false)
    {
        // Non-replacing
        if ($nonReplacing || !$this->isReplacing()) {
            // @ is here intentionally to prevent warnings like when exception is thrown:
            // Warning: array_map(): An error occurred while invoking the map callback
            @array_map([$this, 'visit'], $nodes);

            return $nodes;
        }

        // Replacing
        $newNodes = [];
        foreach ($nodes as $node) {
            $evald = $this->visit($node);
            if ($evald === null) {
                continue;
            }
            if (is_array($evald)) {
                self::flatten($evald, $newNodes);
            } else {
                $newNodes[] = $evald;
            }
        }

        return $newNodes;
    }

    /**
     * Flattens an array.
     *
     * @param array $array The array to flatten
     * @param array $out The output array
     */
    protected static function flatten(array $array, array &$out)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if (null !== $item) {
                    $out[] = $item;
                }
                continue;
            }
            foreach ($item as $nestedItem) {
                // skip null values
                if ($nestedItem === null) {
                    continue;
                }
                if (is_array($nestedItem)) {
                    self::flatten($nestedItem, $out);
                } else {
                    $out[] = $nestedItem;
                }
            }
        }
    }
}
