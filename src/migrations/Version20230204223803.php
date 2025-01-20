<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204223803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_sequence ADD is_disabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE market_sequence ADD is_counter BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE market_sequence ADD counter_pkg INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_sequence DROP is_disabled');
        $this->addSql('ALTER TABLE market_sequence DROP is_counter');
        $this->addSql('ALTER TABLE market_sequence DROP counter_pkg');
    }
}
