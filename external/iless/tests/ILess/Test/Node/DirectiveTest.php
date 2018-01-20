<?php
/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ILess\Context;
use ILess\Node\AnonymousNode;
use ILess\Node\DirectiveNode;
use ILess\Output\StandardOutput;

/**
 * Dimension node tests.
 *
 * @covers Node_Directive
 * @group node
 */
class Test_Node_DirectiveTest extends Test_TestCase
{
    /**
     * @covers __constructor
     */
    public function testConstructor()
    {
        $d = new DirectiveNode('15', new AnonymousNode('bar'));
    }

    /**
     * @covers getType
     */
    public function testGetType()
    {
        $d = new DirectiveNode('15', new AnonymousNode('bar'));
        $this->assertEquals('Directive', $d->getType());
    }

    /**
     * @covers generateCss
     */
    public function testGenerateCss()
    {
        $env = new Context();
        $output = new StandardOutput();

        $d = new DirectiveNode('15', new AnonymousNode('bar'));

        $d->generateCss($env, $output);

        $this->assertEquals('15 bar;', $output->toString());
    }

    /**
     * @covers variable
     */
    public function testVariable()
    {
        // FIXME: implement more!
        $d = new DirectiveNode('15', new AnonymousNode('bar'));
        $this->assertEquals(null, $d->variable('foo'));
    }
}
