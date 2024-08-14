<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240814065055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, address_id INT DEFAULT NULL, order_no VARCHAR(255) NOT NULL, total_price VARCHAR(255) NOT NULL, status ENUM(\'Pending\', \'Shipped\',\'Delivered\'), payment_mode VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_FBEB86E1550C2277 (order_no), INDEX IDX_FBEB86E1A76ED395 (user_id), INDEX IDX_FBEB86E1F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_BA388B7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status ENUM(\'Active\', \'Deleted\'), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, book_order_id INT DEFAULT NULL, book_id INT DEFAULT NULL, price VARCHAR(255) NOT NULL, quantity VARCHAR(255) NOT NULL, INDEX IDX_52EA1F0994607B61 (book_order_id), INDEX IDX_52EA1F0916A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipping_address (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address_line1 LONGTEXT NOT NULL, address_line2 LONGTEXT NOT NULL, landmark VARCHAR(255) NOT NULL, cellphone VARCHAR(255) NOT NULL, pin INT NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, status ENUM(\'Active\', \'Deleted\'), INDEX IDX_EB066945A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_book (id INT AUTO_INCREMENT NOT NULL, isbn_no VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, page_no VARCHAR(255) NOT NULL, edition VARCHAR(255) NOT NULL, publisher VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, binding VARCHAR(255) NOT NULL, genre LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', available_book VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status ENUM(\'Active\', \'Deleted\'), created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_FE7AEC4776C5E57 (isbn_no), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_cart_item (id INT AUTO_INCREMENT NOT NULL, cart_id INT DEFAULT NULL, book_id INT DEFAULT NULL, quantity VARCHAR(255) NOT NULL, status ENUM(\'Active\', \'Deleted\'), created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_2E52E4F31AD5CDBF (cart_id), INDEX IDX_2E52E4F316A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, cellphone VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, status ENUM(\'Active\', \'Deleted\'), enable VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, profile VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4991DBBCE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_order ADD CONSTRAINT FK_FBEB86E1A76ED395 FOREIGN KEY (user_id) REFERENCES web_user (id)');
        $this->addSql('ALTER TABLE book_order ADD CONSTRAINT FK_FBEB86E1F5B7AF75 FOREIGN KEY (address_id) REFERENCES shipping_address (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES web_user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F0994607B61 FOREIGN KEY (book_order_id) REFERENCES book_order (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F0916A2B381 FOREIGN KEY (book_id) REFERENCES web_book (id)');
        $this->addSql('ALTER TABLE shipping_address ADD CONSTRAINT FK_EB066945A76ED395 FOREIGN KEY (user_id) REFERENCES web_user (id)');
        $this->addSql('ALTER TABLE web_cart_item ADD CONSTRAINT FK_2E52E4F31AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE web_cart_item ADD CONSTRAINT FK_2E52E4F316A2B381 FOREIGN KEY (book_id) REFERENCES web_book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_order DROP FOREIGN KEY FK_FBEB86E1A76ED395');
        $this->addSql('ALTER TABLE book_order DROP FOREIGN KEY FK_FBEB86E1F5B7AF75');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F0994607B61');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F0916A2B381');
        $this->addSql('ALTER TABLE shipping_address DROP FOREIGN KEY FK_EB066945A76ED395');
        $this->addSql('ALTER TABLE web_cart_item DROP FOREIGN KEY FK_2E52E4F31AD5CDBF');
        $this->addSql('ALTER TABLE web_cart_item DROP FOREIGN KEY FK_2E52E4F316A2B381');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book_order');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE shipping_address');
        $this->addSql('DROP TABLE web_book');
        $this->addSql('DROP TABLE web_cart_item');
        $this->addSql('DROP TABLE web_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
