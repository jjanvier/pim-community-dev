<?php

namespace spec\Pim\Component\Catalog\Normalizer\Indexing\Product;

use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;
use Pim\Component\Catalog\Normalizer\Indexing\Product\TextNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TextNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TextNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    function it_supports_text_product_value(
        ProductValueInterface $numberValue,
        ProductValueInterface $textValue,
        AttributeInterface $numberAttribute,
        AttributeInterface $textAttribute
    ) {
        $textValue->getAttribute()->willReturn($textAttribute);
        $numberValue->getAttribute()->willReturn($numberAttribute);

        $textAttribute->getBackendType()->willReturn('varchar');
        $numberAttribute->getBackendType()->willReturn('decimal');

        $this->supportsNormalization(new \stdClass(), 'indexing')->shouldReturn(false);
        $this->supportsNormalization(new \stdClass(), 'whatever')->shouldReturn(false);

        $this->supportsNormalization($textValue, 'indexing')->shouldReturn(true);
        $this->supportsNormalization($numberValue, 'whatever')->shouldReturn(false);
        $this->supportsNormalization($numberValue, 'indexing')->shouldReturn(false);
    }

    function it_normalizes_a_text_product_value_with_no_locale_and_no_channel(
        ProductValueInterface $textValue,
        AttributeInterface $textAttribute
    ) {
        $textValue->getAttribute()->willReturn($textAttribute);
        $textValue->getLocale()->willReturn(null);
        $textValue->getScope()->willReturn(null);
        $textValue->getData()->willReturn('a product name');

        $textAttribute->getCode()->willReturn('name');
        $textAttribute->getBackendType()->willReturn('varchar');

        $this->normalize($textValue, 'indexing')->shouldReturn([
            'name-varchar' => [
                '<all_locales>' => [
                    '<all_channels>' => 'a product name'
                ]
            ]
        ]);
    }

    function it_keeps_the_string_as_is_during_normalization(
        ProductValueInterface $textValue,
        AttributeInterface $textAttribute
    ) {
        $textValue->getAttribute()->willReturn($textAttribute);
        $textValue->getLocale()->willReturn(null);
        $textValue->getScope()->willReturn(null);
        $textValue->getData()->willReturn('<h1>My <strong>ProDucT</strong> is awesome</h1>');

        $textAttribute->getCode()->willReturn('name');
        $textAttribute->getBackendType()->willReturn('varchar');

        $this->normalize($textValue, 'indexing')->shouldReturn([
            'name-varchar' => [
                '<all_locales>' => [
                    '<all_channels>' => '<h1>My <strong>ProDucT</strong> is awesome</h1>'
                ]
            ]
        ]);
    }

    function it_normalizes_a_text_product_value_with_locale_and_no_scope(
        ProductValueInterface $textValue,
        AttributeInterface $textAttribute
    ) {
        $textValue->getAttribute()->willReturn($textAttribute);
        $textValue->getLocale()->willReturn('fr_FR');
        $textValue->getScope()->willReturn(null);
        $textValue->getData()->willReturn('a product name');

        $textAttribute->getCode()->willReturn('name');
        $textAttribute->getBackendType()->willReturn('varchar');

        $this->normalize($textValue, 'indexing')->shouldReturn([
            'name-varchar' => [
                'fr_FR' => [
                    '<all_channels>' => 'a product name'
                ]
            ]
        ]);
    }

    function it_normalizes_a_text_product_value_with_no_scope_and_no_locale(
        ProductValueInterface $textValue,
        AttributeInterface $textAttribute
    ) {
        $textValue->getAttribute()->willReturn($textAttribute);
        $textValue->getLocale()->willReturn(null);
        $textValue->getScope()->willReturn('ecommerce');
        $textValue->getData()->willReturn('a product name');

        $textAttribute->getCode()->willReturn('name');
        $textAttribute->getBackendType()->willReturn('varchar');

        $this->normalize($textValue, 'indexing')->shouldReturn([
            'name-varchar' => [
                '<all_locales>' => [
                    'ecommerce' => 'a product name'
                ]
            ]
        ]);
    }

    function it_normalizes_a_text_product_value_with_locale_and_scope(
        ProductValueInterface $textValue,
        AttributeInterface $textAttribute
    ) {
        $textValue->getAttribute()->willReturn($textAttribute);
        $textValue->getLocale()->willReturn('fr_FR');
        $textValue->getScope()->willReturn('ecommerce');
        $textValue->getData()->willReturn('a product name');

        $textAttribute->getCode()->willReturn('name');
        $textAttribute->getBackendType()->willReturn('varchar');

        $this->normalize($textValue, 'indexing')->shouldReturn([
            'name-varchar' => [
                'fr_FR' => [
                    'ecommerce' => 'a product name'
                ]
            ]
        ]);
    }
}
