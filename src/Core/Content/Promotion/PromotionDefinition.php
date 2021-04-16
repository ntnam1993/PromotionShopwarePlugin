<?php declare(strict_types=1);

namespace SwagPromotion\Core\Content\Promotion;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PromotionDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'swag_promotion';
    }

    public function getEntityClass(): string
    {
        return PromotionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return PromotionCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new IntField('discount_rate', 'discountRate'))->addFlags(new Required()),
            (new DateField('start_date', 'startDate'))->addFlags(new Required()),
            (new DateField('expired_date', 'expiredDate'))->addFlags(new Required()),

            new FkField('product_id',  'productId', ProductDefinition::class),
            new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class,
                'id',
                false
            ),

            (new ReferenceVersionField(ProductDefinition::class))->addFlags(new Required()),
        ]);
    }
}
