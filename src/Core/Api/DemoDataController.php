<?php declare(strict_types=1);

namespace SwagPromotion\Core\Api;

use Faker\Factory;
use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Country\Exception\CountryNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class DemoDataController extends AbstractController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $promotionRepository;

    /**
     * DemoDataController constructor.
     * @param EntityRepositoryInterface $productRepository
     * @param EntityRepositoryInterface $promotionRepository
     */
    public function __construct(EntityRepositoryInterface $productRepository, EntityRepositoryInterface $promotionRepository)
    {
        $this->productRepository = $productRepository;
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * @Route("/api/v{version}/_action/swag-promotion/generate", name="api.custom.swag_promotion.generate", methods={"POST"})
     * @param Context $context
     * @return Response
     */
    public function generate(Context $context): Response
    {
        $faker = Factory::create();
        $products = $this->getActiveProduct($context);
        $data = [];
        for ($i = 0; $i < 50;  $i++) {
            $data[] = [
                'id' => Uuid::randomHex(),
                'name' => $faker->name,
                'discountRate' => $faker->randomDigit,
                'startDate' => $faker->dateTimeBetween($startDate = '-5 days', $endDate = 'now')->format("Y-m-d H:i:s"),
                'expiredDate' => $faker->dateTimeBetween($startDate = 'now', $endDate = '+5 days')->format("Y-m-d H:i:s"),
                'productId' => array_rand($products),
            ];
        }
        $this->promotionRepository->create($data, $context);

        return new  Response('generate swag promotion done', Response::HTTP_OK);
    }

    /**
     * @param Context $context
     * @return array
     */
    private function getActiveProduct(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', 1));

        $product = $this->productRepository->search($criteria, $context)->getIds();
        if ($product === null)
        {
            throw new ProductNotFoundException('');
        }
        return $product;
    }
}
