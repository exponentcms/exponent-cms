<?php

/**
 * @group core
 */
class ILessTest extends PHPUnit_Framework_TestCase
{
    public function testConstants()
    {
        $this->assertTrue(defined('ILess\Parser::VERSION'));
        $this->assertTrue(defined('ILess\Parser::LESS_JS_VERSION'));
    }
}
