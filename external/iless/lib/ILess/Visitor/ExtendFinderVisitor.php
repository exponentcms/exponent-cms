<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Visitor;

use ILess\Node\DirectiveNode;
use ILess\Node\ExtendNode;
use ILess\Node\MediaNode;
use ILess\Node\MixinDefinitionNode;
use ILess\Node\RulesetNode;
use ILess\Node\RuleNode;

/**
 * ExtendFinder visitor.
 */
class ExtendFinderVisitor extends Visitor
{
    /**
     * @var array
     */
    protected $contexts = [];

    /**
     * @var array
     */
    protected $allExtendsStack = [[]];

    /**
     * Found extends flag.
     *
     * @var bool
     */
    public $foundExtends = false;

    /**
     * {@inheritdoc}
     */
    public function run($root)
    {
        $root = $this->visit($root);
        $root->allExtends = &$this->allExtendsStack[0];

        return $root;
    }

    /**
     * Visits a rule node.
     *
     * @param RuleNode $node The node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitRule(RuleNode $node, VisitorArguments $arguments)
    {
        $arguments->visitDeeper = false;
    }

    /**
     * Visits a mixin definition node.
     *
     * @param MixinDefinitionNode $node The node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitMixinDefinition(MixinDefinitionNode $node, VisitorArguments $arguments)
    {
        $arguments->visitDeeper = false;
    }

    /**
     * Visits a ruleset node.
     *
     * @param RulesetNode $node The node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitRuleset(RulesetNode $node, VisitorArguments $arguments)
    {
        if ($node->root) {
            return;
        }

        $allSelectorsExtendList = [];
        // get &:extend(.a); rules which apply to all selectors in this ruleset
        for ($i = 0, $count = count($node->rules); $i < $count; ++$i) {
            if ($node->rules[$i] instanceof ExtendNode) {
                $allSelectorsExtendList[] = $node->rules[$i];
                $node->extendOnEveryPath = true;
            }
        }

        // now find every selector and apply the extends that apply to all extends
        // and the ones which apply to an individual extend
        for ($i = 0, $count = count($node->paths); $i < $count; ++$i) {
            $selectorPath = $node->paths[$i];
            $selector = end($selectorPath);
            $list = array_merge($selector->extendList, $allSelectorsExtendList);
            $extendList = [];
            foreach ($list as $allSelectorsExtend) {
                $extendList[] = clone $allSelectorsExtend;
            }

            for ($j = 0, $extendsCount = count($extendList); $j < $extendsCount; ++$j) {
                $this->foundExtends = true;
                $extend = $extendList[$j];
                /* @var $extend ExtendNode */
                $extend->findSelfSelectors($selectorPath);
                $extend->ruleset = $node;
                if ($j === 0) {
                    $extend->firstExtendOnThisSelectorPath = true;
                }
                $temp = count($this->allExtendsStack) - 1;
                $this->allExtendsStack[$temp][] = $extend;
            }
        }
        $this->contexts[] = $node->selectors;
    }

    /**
     * Visits the ruleset (again!).
     *
     * @param RulesetNode $node The node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitRulesetOut(RulesetNode $node, VisitorArguments $arguments)
    {
        if (!$node->root) {
            array_pop($this->contexts);
        }
    }

    /**
     * Visits a media node.
     *
     * @param MediaNode $node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitMedia(MediaNode $node, VisitorArguments $argument)
    {
        $node->allExtends = [];
        $this->allExtendsStack[] = &$node->allExtends;
    }

    /**
     * Visits a media node (again!).
     *
     * @param MediaNode $node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitMediaOut(MediaNode $node, VisitorArguments $argument)
    {
        array_pop($this->allExtendsStack);
    }

    /**
     * Visits a directive node.
     *
     * @param DirectiveNode $node The node
     * @param VisitorArguments $arguments The arguments
     *
     * @return DirectiveNode
     */
    public function visitDirective(DirectiveNode $node, VisitorArguments $arguments)
    {
        $node->allExtends = [];
        $this->allExtendsStack[] = &$node->allExtends;
    }

    /**
     * Visits a directive node (again!).
     *
     * @param DirectiveNode $node The node
     * @param VisitorArguments $arguments The arguments
     *
     * @return DirectiveNode
     */
    public function visitDirectiveOut(DirectiveNode $node, VisitorArguments $arguments)
    {
        array_pop($this->allExtendsStack);
    }
}
