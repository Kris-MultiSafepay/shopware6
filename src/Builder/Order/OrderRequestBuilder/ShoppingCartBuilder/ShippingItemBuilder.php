<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is provided with Magento in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * Copyright © 2021 MultiSafepay, Inc. All rights reserved.
 * See DISCLAIMER.md for disclaimer details.
 *
 */

declare(strict_types=1);

namespace MultiSafepay\Shopware6\Builder\Order\OrderRequestBuilder\ShoppingCartBuilder;

use MultiSafepay\Api\Transactions\OrderRequest\Arguments\ShoppingCart\ShippingItem as TransactionItem;
use MultiSafepay\Shopware6\Util\PriceUtil;
use MultiSafepay\Shopware6\Util\TaxUtil;
use MultiSafepay\ValueObject\Money;
use Shopware\Core\Checkout\Order\OrderEntity;

class ShippingItemBuilder implements ShoppingCartBuilderInterface
{
    /**
     * @var PriceUtil
     */
    private $priceUtil;
    /**
     * @var TaxUtil
     */
    private $taxUtil;

    /**
     * ShippingItemBuilder constructor.
     * @param PriceUtil $priceUtil
     * @param TaxUtil $taxUtil
     */
    public function __construct(PriceUtil $priceUtil, TaxUtil $taxUtil)
    {
        $this->priceUtil = $priceUtil;
        $this->taxUtil = $taxUtil;
    }

    /**
     * @param OrderEntity $order
     * @param string $currency
     * @return array
     */
    public function build(OrderEntity $order, string $currency): array
    {
        $items = [];

        $shippingTaxRate = $this->taxUtil->getTaxRate($order->getShippingCosts());
        $items[] = (new TransactionItem())
            ->addName('Shipping')
            ->addUnitPrice(new Money(round(
                $this->priceUtil->getUnitPriceExclTax($order->getShippingCosts(), $order->getPrice()->hasNetPrices()) * 100,
                10
            ), $currency))
            ->addQuantity($order->getShippingCosts()->getQuantity())
            ->addDescription('Shipping')
            ->addTaxRate($shippingTaxRate)
            ->addTaxTableSelector((string)$shippingTaxRate);

        return $items;
    }
}
