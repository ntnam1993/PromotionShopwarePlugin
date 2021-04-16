<?php declare(strict_types=1);

namespace SwagPromotion\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1617060965 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1617060965;
    }

    public function update(Connection $connection): void
    {
        $connection->exec("CREATE TABLE `swag_promotion` (
            `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `discount_rate` INT(11) NOT NULL,
            `start_date` DATE NOT NULL,
            `expired_date` DATE NOT NULL,
            `product_id` BINARY(16) NULL,
            `product_version_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`),
            KEY `fk.swag_promotion.product_id` (`product_id`,`product_version_id`),
            CONSTRAINT `fk.swag_promotion.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
