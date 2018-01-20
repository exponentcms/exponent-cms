<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ILess\Context;
use ILess\Node\ColorNode;
use ILess\Node\ElementNode;
use ILess\Node\KeywordNode;
use ILess\Node\SelectorNode;
use ILess\Node\RulesetNode;
use ILess\Node\RuleNode;
use ILess\Output\StandardOutput;
use ILess\Visitor\JoinSelectorVisitor;
use ILess\Visitor\VisitorArguments;

/**
 * Ruleset node tests.
 *
 * @covers Node_Ruleset
 * @group node
 */
class Test_Node_RulesetTest extends Test_TestCase
{
    /**
     * @covers __constructor
     */
    public function testConstructor()
    {
        $r = new RulesetNode([
            new ElementNode('div', 'foobar'),
        ], []);
    }

    /**
     * @covers getType
     */
    public function testGetType()
    {
        $r = new RulesetNode([
            new ElementNode(' ', 'foobar'),
        ], []);
        $this->assertEquals('Ruleset', $r->getType());
    }

    /**
     * @covers generateCss
     */
    public function testGenerateCss()
    {
        $env = new Context();
        $output = new StandardOutput();

        $r = new RulesetNode([
            new SelectorNode([new ElementNode('', 'div')]),
        ], [
            new RuleNode('color', new ColorNode('#fff')),
            new RuleNode('font-weight', new KeywordNode('bold')),
        ]);

        $args = new VisitorArguments([
            'visitDeeper' => true,
        ]);

        $visitor = new JoinSelectorVisitor();
        $visitor->visitRuleset($r, $args);

        $n = new RuleNode('font-weight', new KeywordNode('bold'));
        $n->generateCss($env, $output);

        $this->assertEquals('font-weight: bold;', $output->toString());

        $output = new StandardOutput();
        $r->generateCss($env, $output);

        $expected = "div {\n  color: #ffffff;\n  font-weight: bold;\n}";

        $this->assertEquals($expected, $output->toString());
    }
}
