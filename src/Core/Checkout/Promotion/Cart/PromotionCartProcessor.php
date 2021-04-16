<?php declare(strict_types=1);

namespace SwagPromotion\Core\Checkout\Promotion\Cart;

use PhpCsFixer\DocBlock\Line;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use SwagPromotion\Core\Content\Promotion\PromotionEntity;

class PromotionCartProcessor implements CartProcessorInterface
{
    /**
     * @var PercentagePriceCalculator
     */
    private $percentagePriceCalculator;

    /**
     * @var QuantityPriceCalculator
     */
    private $quantityPriceCalculator;

    public function __construct(
        PercentagePriceCalculator $percentagePriceCalculator,
        QuantityPriceCalculator $quantityPriceCalculator
    ) {
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
    }


    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // collect all product in cart
        /** @var LineItemCollection $productLineItems */
        $productLineItems = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);
        if (\count($productLineItems) === 0) {
            return;
        }

        foreach ($productLineItems as $productLineItem) {
            $promotions = $productLineItem->getChildren()->filterType(PromotionCartCollector::TYPE);
            $productPrice = $this->quantityPriceCalculator->calculate($productLineItem->getPriceDefinition(), $context);
            if ($promotions) {
                foreach ($promotions as $promotion) {
                    $dataPromotion = $data->get($promotion->getReferencedId().PromotionCartCollector::DATA_KEY);
                    $this->setPromotionPrice($promotion, $dataPromotion, $productPrice, $context, $productLineItem);
                }
            }

            $this->setLabelAndPriceSubProduct($productLineItem, $productPrice);

            $productLineItem->setPrice($productLineItem->getChildren()->getPrices()->sum());
        }

    }

    private function setLabelAndPriceSubProduct(LineItem $productLineItem, CalculatedPrice $productPrice)
    {
        $subProduct = $productLineItem->getChildren()->get($productLineItem->getId()."-".PromotionCartCollector::TYPE_SUB_PRODUCT);
        $subProduct->setLabel($productLineItem->getLabel());
        $subProduct->setPrice($productPrice);
        $subProduct->setQuantity($productLineItem->getQuantity());
    }

    private function setPromotionPrice(LineItem $promotion, PromotionEntity $dataPromotion, CalculatedPrice $productPrice, SalesChannelContext $context, LineItem $productLineItem)
    {
        $priceDefinition = new PercentagePriceDefinition($dataPromotion->getDiscountRate() * -1, 2);
        $promotion->setPriceDefinition($priceDefinition);

        $price = $this->percentagePriceCalculator->calculate($priceDefinition->getPercentage(), new PriceCollection([$productPrice]), $context);
        $promotion->setPrice($price);
        $promotion->setQuantity($productLineItem->getQuantity());

    }
}
