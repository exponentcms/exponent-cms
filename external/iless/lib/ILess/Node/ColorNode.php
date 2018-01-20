<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Node;

use ILess\Color;
use ILess\Context;
use InvalidArgumentException;
use ILess\Math;
use ILess\Node;
use ILess\Output\OutputInterface;

/**
 * Color node.
 */
class ColorNode extends Node implements ComparableInterface
{
    /**
     * Node type.
     *
     * @var string
     */
    protected $type = 'Color';

    /**
     * @var string
     */
    protected $originalForm;

    /**
     * Constructor.
     *
     * @param string|array $rgb The rgb value
     * @param int $alpha Alpha channel
     * @param string $originalForm Original form of the color
     *
     * @throws InvalidArgumentException
     */
    public function __construct($rgb, $alpha = 1, $originalForm = null)
    {
        if (!$rgb instanceof Color) {
            $value = new Color($rgb, $alpha, $originalForm);
        } else {
            $value = $rgb;
        }

        /* @var $value \ILess\Color */
        parent::__construct($value);
    }

    /**
     * Returns the color object.
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCSS(Context $context, OutputInterface $output)
    {
        $output->add($this->toCSS($context));
    }

    /**
     * Returns the RGB channels.
     *
     * @return array
     */
    public function getRGB()
    {
        return $this->value->rgb;
    }

    /**
     * Returns the HSV components of the color.
     *
     * @return array
     */
    public function toHSV()
    {
        return $this->value->toHSV();
    }

    /**
     * Returns the red channel.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed
     */
    public function getRed($raw = false)
    {
        return $raw ? $this->value->getRed() : new DimensionNode($this->value->getRed());
    }

    /**
     * Returns the green channel.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed
     */
    public function getGreen($raw = false)
    {
        return $raw ? $this->value->getGreen() : new DimensionNode($this->value->getGreen());
    }

    /**
     * Returns the blue channel.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed
     */
    public function getBlue($raw = false)
    {
        return $raw ? $this->value->getBlue() : new DimensionNode($this->value->getBlue());
    }

    /**
     * Returns the alpha channel.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed
     */
    public function getAlpha($raw = false)
    {
        return $raw ? $this->value->getAlpha() : new DimensionNode($this->value->getAlpha());
    }

    /**
     * Returns the color saturation.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed
     */
    public function getSaturation($raw = false)
    {
        return $raw ? $this->value->getSaturation() :
            new DimensionNode(round($this->value->getSaturation() * 100), '%');
    }

    /**
     * Returns the color hue.
     *
     * @param bool $raw Raw value?
     *
     * @return mixed
     */
    public function getHue($raw = false)
    {
        return $raw ? $this->value->getHue() : new DimensionNode(round($this->value->getHue()));
    }

    /**
     * Returns the lightness.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed ILessNode\DimensionNode if $raw is false
     */
    public function getLightness($raw = false)
    {
        return $raw ? $this->value->getLightness() :
            new DimensionNode(round($this->value->getLightness() * 100), '%');
    }

    /**
     * Returns the luma.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed ILessNode\DimensionNode if $raw is false
     */
    public function getLuma($raw = false)
    {
        return $raw ? $this->value->getLuma() : new DimensionNode(
            $this->value->getLuma() * $this->value->getAlpha() * 100, '%'
        );
    }

    /**
     * Returns the luminance.
     *
     * @param bool $raw Return raw value?
     *
     * @return mixed ILessNode\DimensionNode if $raw is false
     */
    public function getLuminance($raw = false)
    {
        return $raw ? $this->value->getLuminance() : new DimensionNode(
            $this->value->getLuminance() * $this->value->getAlpha() * 100, '%'
        );
    }

    /**
     * Converts the node to ARGB.
     *
     * @return AnonymousNode
     */
    public function toARGB()
    {
        return new AnonymousNode($this->value->toARGB());
    }

    /**
     * Returns the HSL components of the color.
     *
     * @return array
     */
    public function toHSL()
    {
        return $this->value->toHSL();
    }

    /**
     * Converts the node to string.
     *
     * @param Context $context
     *
     * @return string
     */
    public function toCSS(Context $context)
    {
        return $this->value->toString($context->compress, $context->compress && $context->canShortenColors);
    }

    /**
     * Operations have to be done per-channel, if not,
     * channels will spill onto each other. Once we have
     * our result, in the form of an integer triplet,
     * we create a new color node to hold the result.
     *
     * @param Context $context
     * @param string $op
     * @param Node $other
     *
     * @return ColorNode
     *
     * @throws InvalidArgumentException
     */
    public function operate(Context $context, $op, Node $other)
    {
        $result = [];

        if (!($other instanceof self)) {
            if (!$other instanceof ToColorConvertibleInterface) {
                throw new InvalidArgumentException(
                    'The other node must implement toColor() method to operate, see ILess\Node\Node_ToColorConvertibleInterface'
                );
            }
            $other = $other->toColor();
            if (!$other instanceof self) {
                throw new InvalidArgumentException('The toColor() method must return an instance of ILess\Node\Node_Color');
            }
        }

        $t = $this->getRGB();
        $o = $other->getRGB();

        for ($c = 0; $c < 3; ++$c) {
            $result[$c] = Math::operate($op, $t[$c], $o[$c]);
            if ($result[$c] > 255) {
                $result[$c] = 255;
            } elseif ($result < 0) {
                $result[$c] = 0;
            }
        }

        return new self($result, $this->value->getAlpha() + $other->value->getAlpha());
    }

    /**
     * Compares with another node.
     *
     * @param Node $other
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function compare(Node $other)
    {
        if (!($other instanceof self)) {
            if (!$other instanceof ToColorConvertibleInterface) {
                throw new InvalidArgumentException(
                    'The other node must implement toColor() method to operate, see ILess\Node\Node_ToColorConvertibleInterface'
                );
            }
            $other = $other->toColor();
            if (!$other instanceof self) {
                throw new InvalidArgumentException('The toColor() method must return an instance of ILess\Node\Node_Color');
            }
        }

        // cannot compare with another node
        if (!$other instanceof self) {
            return -1;
        }

        $color = $this->getColor();
        $other = $other->getColor();

        return ($color->rgb === $other->rgb && $color->alpha === $other->alpha) ? 0 : -1;
    }
}
