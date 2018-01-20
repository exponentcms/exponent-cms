<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ILess\Parser;

/**
 * Issue #50 test.
 */
class ILess_Test_Issues_050Test extends Test_TestCase
{
    public function testIssue()
    {
        $parser = new Parser([
            'compress' => false,
        ]);

        $parser->parseString('@swatch: foobar;
@import "../../../bootstrap3/less/@{swatch}/variables.less";
');

        $this->setExpectedException('ILess\Exception\ImportException', '/bootstrap3/less/foobar/variables.less');

        $css = $parser->getCSS();
    }

    public function testIssueWithApiVariables()
    {
        $parser = new Parser([
            'compress' => false,
        ]);

        $parser->parseString('
@import "../../../bootstrap3/less/@{swatch}/variables.less";
');
        $parser->setVariables([
            'swatch' => 'foobar',
        ]);

        $this->setExpectedException('ILess\Exception\ImportException', '/bootstrap3/less/foobar/variables.less');

        $css = $parser->getCSS();
    }
}
