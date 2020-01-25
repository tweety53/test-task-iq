<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124184908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('-- Create syntax for TABLE \'account\'
        CREATE TABLE `account` (
          `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `amount` decimal(10,2) unsigned NOT NULL DEFAULT \'0.00\',
          PRIMARY KEY (`user_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
        
        -- Create syntax for TABLE \'operation_history\'
        CREATE TABLE `operation_history` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `type` smallint(1) NOT NULL,
          `user_from` int(11) unsigned DEFAULT NULL,
          `user_to` int(11) unsigned DEFAULT NULL,
          `amount` decimal(10,2) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `user_from` (`user_from`),
          KEY `user_to` (`user_to`),
          CONSTRAINT `operation_history_ibfk_1` FOREIGN KEY (`user_from`) REFERENCES `account` (`user_id`),
          CONSTRAINT `operation_history_ibfk_2` FOREIGN KEY (`user_to`) REFERENCES `account` (`user_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;');

    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('operation_history');
        $schema->dropTable('account');
    }
}
