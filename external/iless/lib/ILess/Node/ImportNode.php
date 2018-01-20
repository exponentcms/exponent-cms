<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Node;

use ILess\Context;
use ILess\Exception\Exception;
use ILess\FileInfo;
use ILess\Node;
use ILess\Output\OutputInterface;
use ILess\Util;
use ILess\Visitor\VisitorInterface;
use LogicException;

/**
 * Import.
 */
class ImportNode extends Node implements \Serializable
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Import';

    /**
     * The path.
     *
     * @var QuotedNode|UrlNode
     */
    public $path;

    /**
     * Current file index.
     *
     * @var int
     */
    public $index = 0;

    /**
     * Array of options.
     *
     * @var array
     */
    public $options = [
        'inline' => false,
    ];

    /**
     * The features.
     *
     * @var Node
     */
    public $features;

    /**
     * Import CSS flag.
     *
     * @var bool
     */
    public $css = false;

    /**
     * Skip import?
     *
     * @var bool
     *
     * @see Visitor_Import::visitImport
     */
    public $skip = false;

    /**
     * The root node.
     *
     * @var Node
     */
    public $root;

    /**
     * Error.
     *
     * @var Exception
     */
    public $error;

    /**
     * Imported filename.
     *
     * @var string
     */
    public $importedFilename;

    /**
     * Constructor.
     *
     * @param Node $path The path
     * @param Node $features The features
     * @param array $options Array of options
     * @param int $index The index
     * @param FileInfo $currentFileInfo Current file info
     */
    public function __construct(
        Node $path,
        Node $features = null,
        array $options = [],
        $index = 0,
        FileInfo $currentFileInfo = null
    ) {
        $this->path = $path;
        $this->features = $features;
        $this->options = array_merge($this->options, $options);
        $this->index = $index;
        $this->currentFileInfo = $currentFileInfo;
        if (isset($this->options['less']) || $this->options['inline']) {
            $this->css = !isset($this->options['less']) || !$this->options['less'] || $this->options['inline'];
        } else {
            $path = $this->getPath();
            if ($path && preg_match('/[#\.\&\?\/]css([\?;].*)?$/', $path)) {
                $this->css = true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        if ($this->features) {
            $this->features = $visitor->visit($this->features);
        }

        $this->path = $visitor->visit($this->path);
        if (!$this->getOption('plugin') && !$this->getOption('inline') && $this->root) {
            $this->root = $visitor->visit($this->root);
        }
    }

    /**
     * Returns the path.
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path instanceof UrlNode ? $this->path->value->value : $this->path->value;
    }

    /**
     * @return bool
     */
    public function isVariableImport()
    {
        $path = $this->path;
        if ($path instanceof UrlNode) {
            $path = $path->value;
        }

        if ($path instanceof QuotedNode) {
            return $path->containsVariables();
        }

        return true;
    }

    /**
     * Compiles the node.
     *
     * @param Context $context The context
     * @param array|null $arguments Array of arguments
     * @param bool|null $important Important flag
     *
     * @return array|ImportNode|MediaNode
     *
     * @throws Exception
     * @throws LogicException
     */
    public function compile(Context $context, $arguments = null, $important = null)
    {
        if ($this->getOption('plugin')) {
            $registry = isset($context->frames[0]) && $context->frames[0]->functionRegistry ?
                $context->frames[0]->functionRegistry : null;
            if ($registry && $this->root && $this->root->functions) {
                /* @var $registry FunctionRegistry */
                foreach ($this->root->functions as $funcFile) {
                    $registry->loadPlugin($funcFile);
                }
            }

            return [];
        }

        if ($this->skip) {
            return [];
        }

        if ($this->getOption('inline')) {
            $contents = new AnonymousNode($this->root, 0, new FileInfo([
                'filename' => $this->importedFilename,
                'reference' => $this->path->currentFileInfo->reference,
            ]), true, true, false);

            return $this->features ? new MediaNode([$contents], $this->features->value) : [$contents];
        } elseif ($this->css) {
            $features = $this->features ? $this->features->compile($context) : null;
            $import = new self($this->compilePath($context), $features, $this->options, $this->index);

            if (!$import->css && $this->hasError()) {
                throw $this->getError();
            }

            return $import;
        } else {
            $ruleset = new RulesetNode([], $this->root ? $this->root->rules : []);
            $ruleset->compileImports($context);

            return $this->features ? new MediaNode($ruleset->rules, $this->features->value) : $ruleset->rules;
        }
    }

    /**
     * Compiles the path.
     *
     * @param Context $context
     *
     * @return UrlNode
     */
    public function compilePath(Context $context)
    {
        $path = $this->path->compile($context);
        if (!($path instanceof UrlNode)) {
            $rootPath = $this->currentFileInfo && $this->currentFileInfo->rootPath ? $this->currentFileInfo->rootPath : false;
            if ($rootPath) {
                $pathValue = $path->value;
                // Add the base path if the import is relative
                if ($pathValue && Util::isPathRelative($pathValue)) {
                    $path->value = $rootPath . $pathValue;
                }
            }
            $path->value = Util::normalizePath($path->value);
        }

        return $path;
    }

    /**
     * Compiles the node for import.
     *
     * @param Context $context
     *
     * @return ImportNode
     */
    public function compileForImport(Context $context)
    {
        $path = $this->path;
        if ($this->path instanceof UrlNode) {
            $path = $path->value;
        }

        return new self($path->compile($context),
            $this->features, $this->options, $this->index, $this->currentFileInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $isNotReference = !isset($this->path->currentFileInfo) || !$this->path->currentFileInfo->reference;
        if ($this->css && $isNotReference) {
            $output->add('@import ');
            $this->path->generateCSS($context, $output);
            if ($this->features) {
                $output->add(' ');
                $this->features->generateCSS($context, $output);
            }
            $output->add(';');
        }

        return $output;
    }

    /**
     * Returns the option with given $name.
     *
     * @param string $name The option name
     * @param string $default The default value
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * Has the import an error?
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->error !== null;
    }

    /**
     * Returns the error.
     *
     * @return null|Exception
     */
    public function getError()
    {
        return $this->error;
    }

    public function serialize()
    {
        $vars = get_object_vars($this);
        unset($vars['skip'], $vars['error']);

        return Util\Serializer::serialize($vars);
    }

    public function unserialize($serialized)
    {
        $unserialized = Util\Serializer::unserialize($serialized);
        foreach ($unserialized as $var => $val) {
            $this->$var = $val;
        }
    }
}
