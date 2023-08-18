<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818170121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD price VARCHAR(255) NOT NULL, ADD publisher VARCHAR(255) NOT NULL, ADD pages VARCHAR(255) NOT NULL, ADD edition VARCHAR(255) NOT NULL, ADD author VARCHAR(255) NOT NULL, ADD language VARCHAR(255) NOT NULL, ADD binding VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE web_user CHANGE status status ENUM(\'Active\', \'Deleted\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP price, DROP publisher, DROP pages, DROP edition, DROP author, DROP language, DROP binding');
        $this->addSql('ALTER TABLE web_user CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
