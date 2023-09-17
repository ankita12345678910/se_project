<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230917101038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_cart (book_id INT NOT NULL, cart_id INT NOT NULL, INDEX IDX_123EC3BF16A2B381 (book_id), INDEX IDX_123EC3BF1AD5CDBF (cart_id), PRIMARY KEY(book_id, cart_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_cart ADD CONSTRAINT FK_123EC3BF16A2B381 FOREIGN KEY (book_id) REFERENCES web_book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_cart ADD CONSTRAINT FK_123EC3BF1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE web_book CHANGE status status ENUM(\'Active\', \'Deleted\')');
        $this->addSql('ALTER TABLE web_user CHANGE status status ENUM(\'Active\', \'Deleted\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_cart DROP FOREIGN KEY FK_123EC3BF16A2B381');
        $this->addSql('ALTER TABLE book_cart DROP FOREIGN KEY FK_123EC3BF1AD5CDBF');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE book_cart');
        $this->addSql('ALTER TABLE web_book CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE web_user CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
