<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ILess\Parser;

/**
 * Issue #35 test.
 *
 * @group issue
 */
class Test_Issues_035Test extends Test_TestCase
{
    public function testIssue()
    {
        $parser = new Parser();

        $parser->setVariables(['mycolor' => 'transparent']);

        $parser->parseString(
'.test{
  background-color: @mycolor;
}');

        $css = $parser->getCSS();
        $expected =
'.test {
  background-color: transparent;
}
';
        $this->assertEquals($expected, $css);
    }
}
