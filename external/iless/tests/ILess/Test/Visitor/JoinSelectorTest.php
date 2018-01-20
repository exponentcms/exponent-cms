<?php
/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ILess\Visitor\JoinSelectorVisitor;

/**
 * ILess\ILess\Visitor\Visitor\JoinSelectorVisitor tests.
 *
 * @covers Visitor_JoinSelector
 * @group visitor
 */
class Test_Visitor_JoinSelectorTest extends Test_TestCase
{
    /**
     * @covers __constructor
     */
    public function testVisit()
    {
        $v = new JoinSelectorVisitor();
        $this->assertFalse($v->isReplacing());
    }
}
