<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230917101229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location_client (location_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_8C368C5764D218E (location_id), INDEX IDX_8C368C5719EB6921 (client_id), PRIMARY KEY(location_id, client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location_client ADD CONSTRAINT FK_8C368C5764D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE location_client ADD CONSTRAINT FK_8C368C5719EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE web_book CHANGE status status ENUM(\'Active\', \'Deleted\')');
        $this->addSql('ALTER TABLE web_user CHANGE status status ENUM(\'Active\', \'Deleted\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location_client DROP FOREIGN KEY FK_8C368C5764D218E');
        $this->addSql('ALTER TABLE location_client DROP FOREIGN KEY FK_8C368C5719EB6921');
        $this->addSql('DROP TABLE location_client');
        $this->addSql('ALTER TABLE web_book CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE web_user CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
