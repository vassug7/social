<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231123023526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user (followers INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F7129A808408FDA7 (followers), INDEX IDX_F7129A80A76ED395 (user_id), PRIMARY KEY(followers, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A808408FDA7 FOREIGN KEY (followers) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A808408FDA7');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80A76ED395');
        $this->addSql('DROP TABLE user_user');
    }
}
