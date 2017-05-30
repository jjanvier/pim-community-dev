<?php

namespace spec\Pim\Component\ReferenceData\Factory\ProductValue;

use Acme\Bundle\AppBundle\Entity\Fabric;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\ReferenceData\Exception\InvalidReferenceDataException;
use Pim\Component\ReferenceData\Factory\ProductValue\ReferenceDataCollectionProductValueFactory;
use Pim\Component\ReferenceData\ProductValue\ReferenceDataCollectionProductValue;
use Pim\Component\ReferenceData\Repository\ReferenceDataRepositoryInterface;
use Pim\Component\ReferenceData\Repository\ReferenceDataRepositoryResolverInterface;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * @author    Damien Carcel (damien.carcel@akeneo.com)
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class ReferenceDataCollectionProductValueFactorySpec extends ObjectBehavior
{
    function let(ReferenceDataRepositoryResolverInterface $repositoryResolver, LoggerInterface $logger)
    {
        $this->beConstructedWith(
            $repositoryResolver,
            $logger,
            ReferenceDataCollectionProductValue::class,
            'pim_reference_data_catalog_multiselect'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceDataCollectionProductValueFactory::class);
    }

    function it_supports_pim_reference_data_catalog_multiselect_attribute_type()
    {
        $this->supports('foo')->shouldReturn(false);
        $this->supports('pim_reference_data_catalog_simpleselect')->shouldReturn(false);
        $this->supports('pim_reference_data_catalog_multiselect')->shouldReturn(true);
    }

    function it_creates_an_empty_reference_data_multi_select_product_value(
        $repositoryResolver,
        AttributeInterface $attribute,
        ReferenceDataRepositoryInterface $referenceDataRepository
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $repositoryResolver->resolve('fabrics')->willReturn($referenceDataRepository);
        $referenceDataRepository->findOneBy(Argument::any())->shouldNotBeCalled();

        $productValue = $this->create(
            $attribute,
            null,
            null,
            null
        );

        $productValue->shouldReturnAnInstanceOf(ReferenceDataCollectionProductValue::class);
        $productValue->shouldHaveAttribute('reference_data_multi_select_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_localizable_and_scopable_empty_reference_data_multi_select_product_value(
        $repositoryResolver,
        AttributeInterface $attribute,
        ReferenceDataRepositoryInterface $referenceDataRepository
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $repositoryResolver->resolve('fabrics')->willReturn($referenceDataRepository);
        $referenceDataRepository->findOneBy(Argument::any())->shouldNotBeCalled();

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            null
        );

        $productValue->shouldReturnAnInstanceOf(ReferenceDataCollectionProductValue::class);
        $productValue->shouldHaveAttribute('reference_data_multi_select_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_reference_data_multi_select_product_value(
        $repositoryResolver,
        AttributeInterface $attribute,
        Fabric $silk,
        Fabric $cotton,
        ReferenceDataRepositoryInterface $referenceDataRepository
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $repositoryResolver->resolve('fabrics')->willReturn($referenceDataRepository);
        $referenceDataRepository->findOneBy(['code' => 'silk'])->willReturn($silk);
        $referenceDataRepository->findOneBy(['code' => 'cotton'])->willReturn($cotton);

        $productValue = $this->create(
            $attribute,
            null,
            null,
            ['silk', 'cotton']
        );

        $productValue->shouldReturnAnInstanceOf(ReferenceDataCollectionProductValue::class);
        $productValue->shouldHaveAttribute('reference_data_multi_select_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldHaveReferenceData([$silk, $cotton]);
    }

    function it_creates_a_localizable_and_scopable_reference_data_multi_select_product_value(
        $repositoryResolver,
        AttributeInterface $attribute,
        Fabric $silk,
        Fabric $cotton,
        ReferenceDataRepositoryInterface $referenceDataRepository
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $repositoryResolver->resolve('fabrics')->willReturn($referenceDataRepository);
        $referenceDataRepository->findOneBy(['code' => 'silk'])->willReturn($silk);
        $referenceDataRepository->findOneBy(['code' => 'cotton'])->willReturn($cotton);

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            ['silk', 'cotton']
        );

        $productValue->shouldReturnAnInstanceOf(ReferenceDataCollectionProductValue::class);
        $productValue->shouldHaveAttribute('reference_data_multi_select_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldHaveReferenceData([$silk, $cotton]);
    }

    function it_throws_an_exception_when_provided_data_is_not_an_array(AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $exception = InvalidPropertyTypeException::arrayExpected(
            'reference_data_multi_select_attribute',
            ReferenceDataCollectionProductValueFactory::class,
            true
        );

        $this->shouldThrow($exception)->during('create', [$attribute, null, null, true]);
    }

    function it_throws_an_exception_when_provided_data_is_not_an_array_of_string(AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $exception = InvalidPropertyTypeException::validArrayStructureExpected(
            'reference_data_multi_select_attribute',
            'array key "foo" expects a string as value, "array" given',
            ReferenceDataCollectionProductValueFactory::class,
            ['foo' => ['bar']]
        );

        $this->shouldThrow($exception)->during('create', [$attribute, null, null, ['foo' => ['bar']]]);
    }

    function it_throws_an_exception_when_provided_data_is_not_an_existing_reference_data_code(
        $repositoryResolver,
        $logger,
        ReferenceDataRepositoryInterface $referenceDataRepository,
        AttributeInterface $attribute
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $repositoryResolver->resolve('fabrics')->willReturn($referenceDataRepository);
        $referenceDataRepository->findOneBy(['code' => 'foobar'])->willReturn(null);

        $logger
            ->warning('Tried to load a product value for the attribute "reference_data_multi_select_attribute" with a reference data "foobar" that does not exist.')
            ->shouldBeCalled();

        $exception = InvalidReferenceDataException::validEntityCodeExpected(
            'reference_data_multi_select_attribute',
            'reference data code',
            'The reference data "fabrics" do not exist',
            ReferenceDataCollectionProductValueFactory::class,
            'foobar'
        );

        $this->shouldThrow($exception)->during('create', [$attribute, null, null, ['foobar']]);
    }

    function it_logs_a_warning_when_all_provided_data_are_not_existing_reference_data_codes(
        $repositoryResolver,
        $logger,
        Fabric $silk,
        ReferenceDataRepositoryInterface $referenceDataRepository,
        AttributeInterface $attribute
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('reference_data_multi_select_attribute');
        $attribute->getType()->willReturn('pim_reference_data_catalog_multiselect');
        $attribute->getBackendType()->willReturn('reference_data_options');
        $attribute->isBackendTypeReferenceData()->willReturn(true);
        $attribute->getReferenceDataName()->willReturn('fabrics');

        $repositoryResolver->resolve('fabrics')->willReturn($referenceDataRepository);
        $referenceDataRepository->findOneBy(['code' => 'silk'])->willReturn($silk);
        $referenceDataRepository->findOneBy(['code' => 'cotton'])->willReturn(null);

        $logger
            ->warning('Tried to load a product value for the attribute "reference_data_multi_select_attribute" with a reference data "cotton" that does not exist.')
            ->shouldBeCalled();
        $logger
            ->warning('Tried to load a product value for the attribute "reference_data_multi_select_attribute" with a reference data "silk" that does not exist.')
            ->shouldNotBeCalled();

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            ['silk', 'cotton']
        );

        $productValue->shouldReturnAnInstanceOf(ReferenceDataCollectionProductValue::class);
        $productValue->shouldHaveAttribute('reference_data_multi_select_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldHaveReferenceData([$silk]);
    }

    public function getMatchers()
    {
        return [
            'haveAttribute'     => function ($subject, $attributeCode) {
                return $subject->getAttribute()->getCode() === $attributeCode;
            },
            'beLocalizable'     => function ($subject) {
                return null !== $subject->getLocale();
            },
            'haveLocale'        => function ($subject, $localeCode) {
                return $localeCode === $subject->getLocale();
            },
            'beScopable'        => function ($subject) {
                return null !== $subject->getScope();
            },
            'haveChannel'       => function ($subject, $channelCode) {
                return $channelCode === $subject->getScope();
            },
            'beEmpty'           => function ($subject) {
                return is_array($subject->getData()) && 0 === count($subject->getData());
            },
            'haveReferenceData' => function ($subject, $expected) {
                $fabrics = $subject->getData();

                $hasFabrics = false;
                foreach ($fabrics as $fabric) {
                    $hasFabrics = in_array($fabric, $expected);
                }

                return $hasFabrics;
            },
        ];
    }
}
