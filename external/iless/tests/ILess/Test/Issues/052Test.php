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
class ILess_Test_Issues_052Test extends Test_TestCase
{
    public function testIssue()
    {
        $parser = new Parser([
            'compress' => false,
        ]);

        $parser->parseString('
#mxtest {
  color2: @b;
  alpha: alpha(@a);
  color: darken(@a, 20);
  background: -moz-linear-gradient(top, @a 0%, darken(@a, 20) 100%);
}');

        $parser->setVariables(['a' => 'rgb(46, 120, 176)', 'b' => 'rgba(0,1,2,0.3)']);

        $css = $parser->getCSS();

        $this->assertContains('alpha: 1;', $css);
        $this->assertContains('color2: rgba(0, 1, 2, 0.3);', $css);
    }
}
