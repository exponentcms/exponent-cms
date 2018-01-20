<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess;

use ILess\Node\CompilableInterface;
use ILess\Node\GenerateCSSInterface;
use ILess\Node\VisitableInterface;
use ILess\Output\OutputInterface;
use ILess\Output\StandardOutput;
use ILess\Visitor\VisitorInterface;

/**
 * Base node.
 */
abstract class Node implements VisitableInterface,
    GenerateCSSInterface, CompilableInterface
{
    /**
     * The value.
     *
     * @var Node|string
     */
    public $value;

    /**
     * ILess\Debug information.
     *
     * @var DebugInfo
     */
    public $debugInfo;

    /**
     * The node type. Each node should define the type.
     *
     * @var string
     */
    protected $type;

    /**
     * Current file info.
     *
     * @var FileInfo
     */
    public $currentFileInfo;

    /**
     * @var bool
     */
    public $compileFirst = false;

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the node type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Checks if given method exists.
     *
     * @param mixed $var The variable name
     * @param string $methodName The method name
     *
     * @return bool
     */
    public static function methodExists($var, $methodName)
    {
        return is_object($var) && method_exists($var, $methodName);
    }

    /**
     * Checks if given property exists.
     *
     * @param mixed $var The variable to check
     * @param string $property The property name
     *
     * @return bool
     */
    public static function propertyExists($var, $property)
    {
        return is_object($var) && property_exists($var, $property);
    }

    /**
     * Returns debug information for the node.
     *
     * @param Context $context The context
     * @param Node $node The context node
     * @param string $lineSeparator Line separator
     *
     * @return string
     */
    public static function getDebugInfo(Context $context, Node $node, $lineSeparator = '')
    {
        $result = '';

        if ($node->debugInfo && $context->dumpLineNumbers && !$context->compress) {
            switch ((string) $context->dumpLineNumbers) {
                case DebugInfo::FORMAT_COMMENT;
                    $result = $node->debugInfo->getAsComment();
                    break;

                case DebugInfo::FORMAT_MEDIA_QUERY;
                    $result = $node->debugInfo->getAsMediaQuery();
                    break;

                case DebugInfo::FORMAT_ALL;
                case '1':
                    $result = sprintf('%s%s%s',
                        $node->debugInfo->getAsComment(), $lineSeparator,
                        $node->debugInfo->getAsMediaQuery()
                    );
                    break;
            }
        }

        return $result;
    }

    /**
     * Outputs the ruleset rules.
     *
     * @param Context $context
     * @param OutputInterface $output
     * @param array $rules
     */
    public static function outputRuleset(Context $context, OutputInterface $output, array $rules)
    {
        ++$context->tabLevel;
        $rulesCount = count($rules);

        // compression
        if ($context->compress) {
            $output->add('{');
            for ($i = 0; $i < $rulesCount; ++$i) {
                $rules[$i]->generateCSS($context, $output);
            }
            $output->add('}');
            --$context->tabLevel;

            return;
        }

        // Non-compressed
        $tabSetStr = "\n" . str_repeat('  ', $context->tabLevel - 1);
        $tabRuleStr = $tabSetStr . '  ';

        // Non-compressed
        if (!$rulesCount) {
            $output->add(' {' . $tabSetStr . '}');
        } else {
            $output->add(' {' . $tabRuleStr);
            $rules[0]->generateCSS($context, $output);
            for ($i = 1; $i < $rulesCount; ++$i) {
                $output->add($tabRuleStr);
                $rules[$i]->generateCSS($context, $output);
            }

            $output->add($tabSetStr . '}');
        }

        --$context->tabLevel;
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this->value;
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function toCSS(Context $context)
    {
        $output = new StandardOutput();
        $this->generateCSS($context, $output);

        return $output->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $this->value = $visitor->visit($this->value);
    }

    /**
     * Generate the CSS and put it in the output container.
     *
     * @param Context $context The context
     * @param OutputInterface $output The output
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $output->add($this->value);
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return Node
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function isRulesetLike()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function compileFirst()
    {
        return $this->compileFirst;
    }
}
