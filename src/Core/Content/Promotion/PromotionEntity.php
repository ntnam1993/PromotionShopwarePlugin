<?php declare(strict_types=1);

namespace SwagPromotion\Core\Content\Promotion;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PromotionEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PromotionEntity
     */
    public function setName(string $name): PromotionEntity
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @var integer
     */
    protected $discountRate;

    /**
     * @return int
     */
    public function getDiscountRate(): int
    {
        return $this->discountRate;
    }

    /**
     * @param int $discountRate
     */
    public function setDiscountRate(int $discountRate): void
    {
        $this->discountRate = $discountRate;
    }


    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @return ProductEntity|null
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * @param ProductEntity|null $product
     */
    public function setProduct(?ProductEntity $product)
    {
        $this->product = $product;
    }

    /**
     * @var string
     */
    protected $expiredDate;
    /**
     * @return mixed
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * @param mixed $expiredDate
     * @return PromotionEntity
     */
    public function setExpiredDate($expiredDate)
    {
        $this->expiredDate = $expiredDate;
        return $this;
    }

    /**
     * @var string
     */
    protected $startDate;
    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     * @return PromotionEntity
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }


}
