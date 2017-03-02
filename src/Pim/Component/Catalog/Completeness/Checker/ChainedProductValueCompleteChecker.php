<?php

namespace Pim\Component\Catalog\Completeness\Checker;

use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ProductValueInterface;

/**
 * Chained checker that contains all the product value completeness checkers.
 * It's the front checker that should be used to determine if a value is complete on a given couple channel/locale.
 *
 * This checkers supports values that are compatible with the given couple locale/scope.
 * Then it delegates to the internal checkers the responsibility to check the completeness
 * depending on the value's attribute type.
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @internal for internal use only, please use the \Pim\Component\Catalog\Completeness\CompletenessGeneratorInterface
 *           to calculate the completeness on a product
 */
class ChainedProductValueCompleteChecker implements ProductValueCompleteCheckerInterface
{
    /** @var ProductValueCompleteCheckerInterface[] */
    protected $productValueCheckers = [];

    /**
     * {@inheritdoc}
     */
    public function isComplete(
        ProductValueInterface $productValue,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        foreach ($this->productValueCheckers as $productValueChecker) {
            if ($productValueChecker->supportsValue($productValue, $channel, $locale)
                && $productValueChecker->isComplete($productValue, $channel, $locale)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(
        ProductValueInterface $productValue,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        if (null !== $productValue->getScope() && $channel !== $productValue->getScope()) {
            return false;
        }

        if (null !== $productValue->getLocale() && $locale !== $productValue->getLocale()) {
            return false;
        }

        if ($productValue->getAttribute()->isLocaleSpecific() &&
            !$productValue->getAttribute()->hasLocaleSpecific($locale)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param ProductValueCompleteCheckerInterface $checker
     */
    public function addProductValueChecker(ProductValueCompleteCheckerInterface $checker)
    {
        $this->productValueCheckers[] = $checker;
    }
}
