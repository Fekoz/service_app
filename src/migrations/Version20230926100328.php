<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926100328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE directory_marketplace_format_import (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "market_mapping" (id SERIAL NOT NULL, directory_marketplace_import_id INT DEFAULT NULL, market_mapping_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4070C40AC693B893 ON "market_mapping" (directory_marketplace_import_id)');
        $this->addSql('CREATE INDEX IDX_4070C40A944523D0 ON "market_mapping" (market_mapping_id)');
        $this->addSql('CREATE TABLE "market_mapping_property" (id INT NOT NULL, value VARCHAR(255) DEFAULT NULL, info VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "market_mapping" ADD CONSTRAINT FK_4070C40AC693B893 FOREIGN KEY (directory_marketplace_import_id) REFERENCES directory_marketplace_format_import (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "market_mapping" ADD CONSTRAINT FK_4070C40A944523D0 FOREIGN KEY (market_mapping_id) REFERENCES "market_mapping_property" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "market_mapping" DROP CONSTRAINT FK_4070C40AC693B893');
        $this->addSql('ALTER TABLE "market_mapping" DROP CONSTRAINT FK_4070C40A944523D0');
        $this->addSql('DROP TABLE directory_marketplace_format_import');
        $this->addSql('DROP TABLE "market_mapping"');
        $this->addSql('DROP TABLE "market_mapping_property"');
    }
}
