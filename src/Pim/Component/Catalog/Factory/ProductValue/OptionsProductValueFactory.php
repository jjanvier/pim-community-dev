<?php

namespace Pim\Component\Catalog\Factory\ProductValue;

use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Pim\Component\Catalog\Exception\InvalidOptionException;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\AttributeOptionInterface;
use Pim\Component\Catalog\Repository\AttributeOptionRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Factory that creates options (multi-select) product values.
 *
 * @internal  Please, do not use this class directly. You must use \Pim\Component\Catalog\Factory\ProductValueFactory.
 *
 * @author    Damien Carcel (damien.carcel@akeneo.com)
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class OptionsProductValueFactory implements ProductValueFactoryInterface
{
    /** @var AttributeOptionRepositoryInterface */
    protected $attrOptionRepository;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $productValueClass;

    /** @var string */
    protected $supportedAttributeType;

    /**
     * @param AttributeOptionRepositoryInterface $attrOptionRepository
     * @param LoggerInterface                    $logger
     * @param string                             $productValueClass
     * @param string                             $supportedAttributeType
     */
    public function __construct(
        AttributeOptionRepositoryInterface $attrOptionRepository,
        LoggerInterface $logger,
        $productValueClass,
        $supportedAttributeType
    ) {
        $this->attrOptionRepository = $attrOptionRepository;
        $this->logger = $logger;
        $this->productValueClass = $productValueClass;
        $this->supportedAttributeType = $supportedAttributeType;
    }

    /**
     * {@inheritdoc}
     */
    public function create(AttributeInterface $attribute, $channelCode, $localeCode, $data)
    {
        $this->checkData($attribute, $data);

        if (null === $data) {
            $data = [];
        }

        $value = new $this->productValueClass(
            $attribute,
            $channelCode,
            $localeCode,
            $this->getOptions($attribute, $data)
        );

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($attributeType)
    {
        return $attributeType === $this->supportedAttributeType;
    }

    /**
     * Checks if data is valid.
     *
     * @param AttributeInterface $attribute
     * @param mixed              $data
     *
     * @throws InvalidPropertyTypeException
     */
    protected function checkData(AttributeInterface $attribute, $data)
    {
        if (null === $data || [] === $data) {
            return;
        }

        if (!is_array($data)) {
            throw InvalidPropertyTypeException::arrayExpected(
                $attribute->getCode(),
                static::class,
                $data
            );
        }

        foreach ($data as $value) {
            if (!is_string($value)) {
                throw InvalidPropertyTypeException::validArrayStructureExpected(
                    $attribute->getCode(),
                    sprintf('one of the options is not a string, "%s" given', gettype($value)),
                    static::class,
                    $data
                );
            }
        }
    }

    /**
     * Returns an array of attribute options.
     *
     * @param AttributeInterface $attribute
     * @param string[]           $data
     *
     * @throws InvalidOptionException
     * @return array
     */
    protected function getOptions(AttributeInterface $attribute, array $data)
    {
        $options = [];

        foreach ($data as $optionCode) {
            if (null !== $option = $this->getOption($attribute, $optionCode)) {
                $options[] = $option;
            }
        }

        if (empty($options) && !empty($data)) {
            throw InvalidOptionException::validEntityCodeExpected(
                $attribute->getCode(),
                'code',
                'The options do not exist',
                static::class,
                implode(',', $data)
            );
        }

        return $options;
    }

    /**
     * Gets an attribute option from its code.
     *
     * @param AttributeInterface $attribute
     * @param string             $optionCode
     *
     * @return AttributeOptionInterface|null
     */
    protected function getOption(AttributeInterface $attribute, $optionCode)
    {
        $identifier = $attribute->getCode() . '.' . $optionCode;
        $option = $this->attrOptionRepository->findOneByIdentifier($identifier);

        if (null === $option) {
            $this->logger->warning(
                sprintf(
                    'Tried to load a product value for the attribute "%s" with an option "%s" that does not exist.',
                    $attribute->getCode(),
                    $optionCode
                )
            );
        }

        return $option;
    }
}
