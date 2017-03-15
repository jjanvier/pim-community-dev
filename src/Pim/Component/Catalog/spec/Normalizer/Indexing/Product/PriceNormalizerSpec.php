<?php

namespace spec\Pim\Component\Catalog\Normalizer\Indexing\Product;

use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductPriceInterface;
use Pim\Component\Catalog\Normalizer\Indexing\Product\PriceNormalizer;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PriceNormalizerSpec extends ObjectBehavior
{
    function let(NormalizerInterface $stdNormalizer)
    {
        $this->beConstructedWith($stdNormalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PriceNormalizer::class);
    }

    function it_support_prices(ProductPriceInterface $price)
    {
        $this->supportsNormalization(new \stdClass(), 'whatever')->shouldReturn(false);
        $this->supportsNormalization(new \stdClass(), 'indexing')->shouldReturn(false);
        $this->supportsNormalization($price, 'whatever')->shouldReturn(false);
        $this->supportsNormalization($price, 'indexing')->shouldReturn(true);
    }

    function it_normalizes_prices($stdNormalizer, ProductPriceInterface $price)
    {
        $stdNormalizer->normalize($price, 'indexing', ['context'])->willReturn('std-price');

        $this->normalize($price, 'indexing', ['context'])->shouldReturn('std-price');
    }
}
