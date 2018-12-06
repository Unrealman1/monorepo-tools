<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

class PricingService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price[] $prices
     * @return bool
     */
    public function arePricesDifferent(array $prices)
    {
        if (count($prices) === 0) {
            throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidArgumentException('Array can not be empty.');
        }

        $firstPrice = array_pop($prices);
        /* @var $firstPrice \Shopsys\FrameworkBundle\Model\Pricing\Price */
        foreach ($prices as $price) {
            if ($price->getPriceWithoutVat() !== $firstPrice->getPriceWithoutVat()
                || $price->getPriceWithVat() !== $firstPrice->getPriceWithVat()
            ) {
                return true;
            }
        }

        return false;
    }
}
