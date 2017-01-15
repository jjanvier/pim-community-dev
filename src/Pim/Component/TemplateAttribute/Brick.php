<?php

namespace Pim\Component\TemplateAttribute;

use Pim\Component\Catalog\Model\AttributeInterface;

class Brick
{
    private $isVariant;
    private $variantAttribute;
    private $attributes = [];

    /**
     * @param array              $attributes
     * @param AttributeInterface $variantAttribute
     */
    public function __construct(array $attributes, AttributeInterface $variantAttribute = null)
    {
        $this->attributes = $attributes;
        $this->variantAttribute = $variantAttribute;
        $this->isVariant = null !== $variantAttribute;
    }

    /**
     * @return boolean
     */
    public function isVariant()
    {
        return $this->isVariant;
    }

    /**
     * @return AttributeInterface
     */
    public function getVariantAttribute()
    {
        return $this->variantAttribute;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}