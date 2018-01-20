<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Visitor;

use ILess\Node\DirectiveNode;
use ILess\Node\MediaNode;
use ILess\Node\MixinDefinitionNode;
use ILess\Node\SelectorNode;
use ILess\Node\RulesetNode;
use ILess\Node\RuleNode;

/**
 * Join Selector visitor.
 */
class JoinSelectorVisitor extends Visitor
{
    /**
     * Array of contexts.
     *
     * @var array
     */
    protected $contexts = [[]];

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
        $paths = [];

        if (!$node->root) {
            $selectors = [];
            foreach ($node->selectors as $selector) {
                /* @var $selector SelectorNode */
                if ($selector->getIsOutput()) {
                    $selectors[] = $selector;
                }
            }

            $node->selectors = $selectors;

            if (count($selectors)) {
                $context = $this->contexts[count($this->contexts) - 1];
                $paths = $node->joinSelectors($context, $selectors);
            } else {
                $node->rules = [];
            }

            $node->paths = $paths;
        }

        $this->contexts[] = $paths;
    }

    /**
     * Visits the ruleset (again!).
     *
     * @param RulesetNode $node The node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitRulesetOut(RulesetNode $node, VisitorArguments $arguments)
    {
        array_pop($this->contexts);
    }

    /**
     * Visits a media node.
     *
     * @param MediaNode $node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitMedia(MediaNode $node, VisitorArguments $argument)
    {
        $context = end($this->contexts);
        if (!count($context) || $context[0]->multiMedia) {
            $node->rules[0]->root = true;
        }
    }

    /**
     * Visits a directive node.
     *
     * @param DirectiveNode $node
     * @param VisitorArguments $arguments The arguments
     */
    public function visitDirective(DirectiveNode $node, VisitorArguments $argument)
    {
        $context = end($this->contexts);
        if (count($node->rules)) {
            if ($node->isRooted || count($context) === 0) {
                $node->rules[0]->root = true;
            } else {
                $node->rules[0]->root = false;
            }
        }
    }
}
