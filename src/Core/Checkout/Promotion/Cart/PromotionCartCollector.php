<?php declare(strict_types=1);

namespace SwagPromotion\Core\Checkout\Promotion\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PromotionCartCollector implements CartDataCollectorInterface
{
    public const DATA_KEY = 'swag_promotion';
    public const TYPE = 'swagpromotion';
    public const TYPE_SUB_PRODUCT = 'sub-product';
    public const DISCOUNT_TYPE = 'swagpromotion-discount';
    public const DISCOUNT_TYPE_ABSOLUTE = 'absolute';
    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    protected $promotionRepository;

    public function __construct(EntityRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        /** @var LineItemCollection $productLineItems */
        $productLineItems = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);
        if (\count($productLineItems) === 0) {
            return;
        }
        $promotions = $this->fetchPromotions($productLineItems, $context);

        if (!$promotions) {
            return;
        }

        foreach ($promotions as $promotion) {
            $data->set($promotion->getId().self::DATA_KEY, $promotion);
        }
        foreach ($productLineItems as $productLineItem) {
            $productLineItem = $this->addSubProduct($productLineItem);
            foreach ($promotions as $promotion) {
                if ($promotion->getProduct()->getId() == $productLineItem->getReferencedId()) {
                    $promotionLineItem = new LineItem($promotion->getId()."-".self::TYPE, self::TYPE, $promotion->getId());
                    $promotionLineItem->setLabel($promotion->getName());
                    $promotionLineItem->setGood(false);
                    $promotionLineItem->setRemovable(true);
                    $promotionLineItem->setStackable(true);
                    $promotionLineItem->setQuantity($productLineItem->getQuantity());
                    $productLineItem->addChild($promotionLineItem);
                }
            }
        }
    }

    private function fetchPromotions(LineItemCollection $productLineItems, SalesChannelContext $context)
    {
        $productIds = $productLineItems->getReferenceIds();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('productId', $productIds));
        $criteria->addAssociation('product');
        $promotions = $this->promotionRepository->search($criteria, $context->getContext())->getEntities();

        if (count($promotions) === 0) {
            return null;
        }

        return $promotions;
    }

    private function addSubProduct(LineItem $productLineItem)
    {
        $subProduct = new LineItem($productLineItem->getId()."-".self::TYPE_SUB_PRODUCT, self::TYPE_SUB_PRODUCT, $productLineItem->getId());
        $subProduct->setLabel("sub Product")
            ->setStackable(true)
            ->setRemovable(true)
            ->setQuantity($productLineItem->getQuantity());

        $productLineItem->addChild($subProduct);
        return $productLineItem;
    }
}
