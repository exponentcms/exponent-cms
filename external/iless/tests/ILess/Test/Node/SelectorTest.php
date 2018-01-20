<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ILess\Context;
use ILess\Node\ElementNode;
use ILess\Node\SelectorNode;
use ILess\Output\StandardOutput;

/**
 * Selector node tests.
 *
 * @covers Node_Selector
 * @group node
 */
class Test_Node_SelectorTest extends Test_TestCase
{
    /**
     * @covers __constructor
     */
    public function testConstructor()
    {
        $s = new SelectorNode([
            new ElementNode(' ', 'foobar'),
        ], []);
    }

    /**
     * @covers getType
     */
    public function testGetType()
    {
        $s = new SelectorNode([
            new ElementNode(' ', 'foobar'),
        ], []);
        $this->assertEquals('Selector', $s->getType());
    }

    /**
     * @covers generateCss
     */
    public function testGenerateCss()
    {
        $env = new Context();
        $output = new StandardOutput();

        $s = new SelectorNode([
            new ElementNode('', 'foobar'),
        ], []);

        $s->generateCss($env, $output);
        $this->assertEquals(' foobar', $output->toString());
    }
}
