<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130204243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE directory_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE directory_meter_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE directory_specification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE directory_storage_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin (id SERIAL NOT NULL, login VARCHAR(60) NOT NULL, pass VARCHAR(60) NOT NULL, email VARCHAR(60) DEFAULT NULL, protection INT DEFAULT NULL, role INT DEFAULT NULL, is_active BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE attribute (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, product_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE auth (id SERIAL NOT NULL, session VARCHAR(255) NOT NULL, prefix VARCHAR(255) DEFAULT NULL, object TEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, key VARCHAR(255) DEFAULT NULL, type VARCHAR(40) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id SERIAL NOT NULL, phone VARCHAR(16) NOT NULL, email VARCHAR(60) NOT NULL, city INT DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE collection (id SERIAL NOT NULL, in_product_id INT NOT NULL, out_product_id INT NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE directory_log (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE directory_meter (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE directory_specification (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE directory_storage (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE field (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, param TEXT DEFAULT NULL, max INT NOT NULL, is_made BOOLEAN NOT NULL, export TEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE images (id SERIAL NOT NULL, dir VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, original_url VARCHAR(255) NOT NULL, type BOOLEAN NOT NULL, product_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mail_template (id SERIAL NOT NULL, title VARCHAR(255) DEFAULT NULL, message TEXT DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE market_sequence (id SERIAL NOT NULL, mid VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF37C2E341AEF4CE ON market_sequence (mid)');
        $this->addSql('CREATE TABLE options (id SERIAL NOT NULL, value VARCHAR(255) NOT NULL, info VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "order" (id SERIAL NOT NULL, client_id INT DEFAULT NULL, presents_id INT DEFAULT NULL, status INT NOT NULL, type INT NOT NULL, uuid VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5299398D17F50A6 ON "order" (uuid)');
        $this->addSql('CREATE INDEX IDX_F529939819EB6921 ON "order" (client_id)');
        $this->addSql('CREATE INDEX IDX_F5299398549ABC15 ON "order" (presents_id)');
        $this->addSql('CREATE TABLE "order_field" (id SERIAL NOT NULL, order_id INT DEFAULT NULL, key VARCHAR(15) NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D86B31A58D9F6D38 ON "order_field" (order_id)');
        $this->addSql('CREATE TABLE order_to_carpet (id SERIAL NOT NULL, client_id INT DEFAULT NULL, product_id INT DEFAULT NULL, status INT NOT NULL, price DOUBLE PRECISION NOT NULL, width VARCHAR(10) NOT NULL, height VARCHAR(10) NOT NULL, count INT NOT NULL, uuid VARCHAR(255) NOT NULL, price_uuid VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DB80DF4FD17F50A6 ON order_to_carpet (uuid)');
        $this->addSql('CREATE INDEX IDX_DB80DF4F19EB6921 ON order_to_carpet (client_id)');
        $this->addSql('CREATE INDEX IDX_DB80DF4F4584665A ON order_to_carpet (product_id)');
        $this->addSql('CREATE TABLE presents (id SERIAL NOT NULL, key VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE price (id SERIAL NOT NULL, width VARCHAR(255) NOT NULL, height VARCHAR(255) NOT NULL, count INT NOT NULL, price DOUBLE PRECISION NOT NULL, meter INT NOT NULL, old_price DOUBLE PRECISION NOT NULL, uuid VARCHAR(255) NOT NULL, mid VARCHAR(255) NOT NULL, storage INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC822D9D17F50A6 ON price (uuid)');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, is_active BOOLEAN NOT NULL, article VARCHAR(255) NOT NULL, uuid VARCHAR(255) NOT NULL, full_price VARCHAR(255) NOT NULL, factor INT NOT NULL, original_url VARCHAR(255) NOT NULL, is_product_lock BOOLEAN DEFAULT NULL, is_price_lock BOOLEAN DEFAULT NULL, is_specification_lock BOOLEAN DEFAULT NULL, is_images_lock BOOLEAN DEFAULT NULL, is_attribute_lock BOOLEAN DEFAULT NULL, is_global_update_lock BOOLEAN DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD23A0E66 ON product (article)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADD17F50A6 ON product (uuid)');
        $this->addSql('CREATE TABLE sender (id SERIAL NOT NULL, client_id INT DEFAULT NULL, mail_template_id INT DEFAULT NULL, is_send BOOLEAN NOT NULL, uuid VARCHAR(255) NOT NULL, type INT NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F004ACF19EB6921 ON sender (client_id)');
        $this->addSql('CREATE INDEX IDX_5F004ACFB1057265 ON sender (mail_template_id)');
        $this->addSql('CREATE TABLE showcases (id SERIAL NOT NULL, admin_id INT DEFAULT NULL, order_id INT DEFAULT NULL, uuid VARCHAR(60) NOT NULL, is_active BOOLEAN NOT NULL, price_list JSON DEFAULT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9DC9A553D17F50A6 ON showcases (uuid)');
        $this->addSql('CREATE INDEX IDX_9DC9A553642B8210 ON showcases (admin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9DC9A5538D9F6D38 ON showcases (order_id)');
        $this->addSql('CREATE TABLE specification (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, product_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE stop_list (id SERIAL NOT NULL, ip VARCHAR(20) NOT NULL, type INT NOT NULL, session VARCHAR(60) DEFAULT NULL, unlock_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, try INT NOT NULL, name VARCHAR(255) NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398549ABC15 FOREIGN KEY (presents_id) REFERENCES presents (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order_field" ADD CONSTRAINT FK_D86B31A58D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_to_carpet ADD CONSTRAINT FK_DB80DF4F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_to_carpet ADD CONSTRAINT FK_DB80DF4F4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sender ADD CONSTRAINT FK_5F004ACF19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sender ADD CONSTRAINT FK_5F004ACFB1057265 FOREIGN KEY (mail_template_id) REFERENCES mail_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE showcases ADD CONSTRAINT FK_9DC9A553642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE showcases ADD CONSTRAINT FK_9DC9A5538D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE showcases DROP CONSTRAINT FK_9DC9A553642B8210');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F529939819EB6921');
        $this->addSql('ALTER TABLE order_to_carpet DROP CONSTRAINT FK_DB80DF4F19EB6921');
        $this->addSql('ALTER TABLE sender DROP CONSTRAINT FK_5F004ACF19EB6921');
        $this->addSql('ALTER TABLE sender DROP CONSTRAINT FK_5F004ACFB1057265');
        $this->addSql('ALTER TABLE "order_field" DROP CONSTRAINT FK_D86B31A58D9F6D38');
        $this->addSql('ALTER TABLE showcases DROP CONSTRAINT FK_9DC9A5538D9F6D38');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F5299398549ABC15');
        $this->addSql('ALTER TABLE order_to_carpet DROP CONSTRAINT FK_DB80DF4F4584665A');
        $this->addSql('DROP SEQUENCE directory_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE directory_meter_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE directory_specification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE directory_storage_id_seq CASCADE');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE auth');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE collection');
        $this->addSql('DROP TABLE directory_log');
        $this->addSql('DROP TABLE directory_meter');
        $this->addSql('DROP TABLE directory_specification');
        $this->addSql('DROP TABLE directory_storage');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE mail_template');
        $this->addSql('DROP TABLE market_sequence');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE "order_field"');
        $this->addSql('DROP TABLE order_to_carpet');
        $this->addSql('DROP TABLE presents');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE sender');
        $this->addSql('DROP TABLE showcases');
        $this->addSql('DROP TABLE specification');
        $this->addSql('DROP TABLE stop_list');
    }
}
