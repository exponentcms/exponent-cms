<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ILess\Context;
use ILess\Node\AnonymousNode;
use ILess\Node\VariableNode;
use ILess\Node\ValueNode;
use ILess\Output\StandardOutput;

/**
 * ILess\Variable node tests.
 *
 * @covers Node_Variable
 * @group node
 */
class Test_Node_VariableTest extends Test_TestCase
{
    /**
     * @covers __constructor
     */
    public function testConstructor()
    {
        $v = new VariableNode('foo', [
            new AnonymousNode('foobar'),
        ]);
    }

    /**
     * @covers getType
     */
    public function testGetType()
    {
        $v = new ValueNode([
            new AnonymousNode('foobar'),
        ]);
        $this->assertEquals('Value', $v->getType());
    }

    /**
     * @covers generateCss
     */
    public function testGenerateCss()
    {
        $env = new Context();
        $output = new StandardOutput();

        $v = new ValueNode([
            new AnonymousNode('foobar'),
        ]);

        $v->generateCss($env, $output);
        $this->assertEquals('foobar', $output->toString());
    }
}
