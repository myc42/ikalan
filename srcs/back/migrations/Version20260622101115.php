<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622101115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trophy DROP CONSTRAINT fk_112afae99d86650f');
        $this->addSql('DROP INDEX uniq_112afae99d86650f');
        $this->addSql('ALTER TABLE trophy RENAME COLUMN user_id_id TO user_id');
        $this->addSql('ALTER TABLE trophy ADD CONSTRAINT FK_112AFAE9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_112AFAE9A76ED395 ON trophy (user_id)');
        $this->addSql('ALTER TABLE user_streaks DROP CONSTRAINT fk_1a8c3769d86650f');
        $this->addSql('DROP INDEX uniq_1a8c3769d86650f');
        $this->addSql('ALTER TABLE user_streaks RENAME COLUMN user_id_id TO user_id');
        $this->addSql('ALTER TABLE user_streaks ADD CONSTRAINT FK_1A8C376A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1A8C376A76ED395 ON user_streaks (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trophy DROP CONSTRAINT FK_112AFAE9A76ED395');
        $this->addSql('DROP INDEX UNIQ_112AFAE9A76ED395');
        $this->addSql('ALTER TABLE trophy RENAME COLUMN user_id TO user_id_id');
        $this->addSql('ALTER TABLE trophy ADD CONSTRAINT fk_112afae99d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_112afae99d86650f ON trophy (user_id_id)');
        $this->addSql('ALTER TABLE user_streaks DROP CONSTRAINT FK_1A8C376A76ED395');
        $this->addSql('DROP INDEX UNIQ_1A8C376A76ED395');
        $this->addSql('ALTER TABLE user_streaks RENAME COLUMN user_id TO user_id_id');
        $this->addSql('ALTER TABLE user_streaks ADD CONSTRAINT fk_1a8c3769d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_1a8c3769d86650f ON user_streaks (user_id_id)');
    }
}
