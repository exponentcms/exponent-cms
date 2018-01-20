<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ILess\Parser;

/**
 * Issue #52 test.
 */
class ILess_Test_Issues_056Test extends Test_TestCase
{
    public function testIssue()
    {
        $parser = new Parser();

        $parser->setVariables([
            'swatch' => '',
        ]);

        $parser->parseString('body { color: @swatch }');

        $css = $parser->getCSS();

        $expected = <<< EXPECTED
body {
  color: ;
}

EXPECTED;

        $this->assertEquals($expected, $css);
    }
}
