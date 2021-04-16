<?php declare(strict_types=1);

namespace SwagPromotion\Storefront\Page\Product\Subscriber;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use SwagPromotion\Core\Content\Promotion\PromotionCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageCriteriaSubscriber implements EventSubscriberInterface
{
    private $promotionRepository;
    private $systemConfigService;

    public function __construct(
        EntityRepositoryInterface $promotionRepository,
        SystemConfigService $systemConfigService
    )
    {
        $this->promotionRepository = $promotionRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded'
        ];
    }

    public function onProductsLoaded(EntityLoadedEvent $event): void
    {
        if (!$this->systemConfigService->get('SwagPromotion.config.showInStorefront')) {
            return;
        }
        /** @var ProductEntity $productEntity */
        foreach ($event->getEntities() as $productEntity) {
            $promotion = $this->fetchPromotion($event->getContext(), $productEntity->getId());
            $productEntity->addExtension('promotion', $promotion);
        }
    }

    /**
     * @param Context $context
     * @param string $productId
     * @return PromotionCollection
     */
    public function fetchPromotion(Context $context, string $productId): PromotionCollection
    {
        $limit = $this->systemConfigService->get('SwagPromotion.config.numberPromotion');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->setLimit($limit);

        /** @var PromotionCollection $promotionCollection */
        $promotionCollection = $this->promotionRepository->search($criteria, $context)->getEntities();

        return $promotionCollection;
    }
}
