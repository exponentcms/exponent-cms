<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess;

use InvalidArgumentException;

/**
 * Configurable.
 */
abstract class Configurable
{
    /**
     * Array of options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Array of default options.
     *
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
        $this->setup();
    }

    /**
     * Set options.
     *
     * If $options is an object it will be converted into an array by called
     * it's toArray method.
     *
     * @throws InvalidArgumentException
     *
     * @param array|object $options
     *
     * @return Configurable
     */
    public function setOptions($options)
    {
        // first convert to array if needed
        if (!is_array($options)) {
            if (is_object($options) && is_callable([$options, 'toArray'])) {
                $options = $options->toArray();
            } else {
                throw new InvalidArgumentException(sprintf('Options for "%s" must be an array or a object with ->toArray() method',
                    get_class($this)));
            }
        }

        // combine the passed options with the defaults
        $defaults = $this->getDefaultOptions();
        $this->options = array_merge($defaults, $options);

        return $this;
    }

    /**
     * Returns default options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    /**
     * Initialization hook.
     *
     * Can be used by classes for special behaviour. For instance some options
     * have extra setup work in their 'set' method that also need to be called
     * when the option is passed as a constructor argument.
     *
     * This hook is called by the constructor after saving the constructor
     * arguments in {@link $options}
     */
    protected function setup()
    {
    }

    /**
     * Get an option value by name.
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param string $name
     * @param mixed $default Default value if confiuration of $name is not present
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * Checks is the object has option with given $name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Set an option.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Configurable
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds options. Overrides options already set with the same name.
     *
     * @param array $options Array of options
     *
     * @return Configurable
     */
    public function addOptions(array $options)
    {
        foreach ($options as $o => $v) {
            $this->setOption($o, $v);
        }

        return $this;
    }
}
