<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260621145758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trophy ALTER perfect_chapter SET DEFAULT 0');
        $this->addSql('ALTER TABLE trophy ALTER module_master SET DEFAULT 0');
        $this->addSql('ALTER TABLE trophy ALTER flawless_streak SET DEFAULT 0');
        $this->addSql('ALTER TABLE user_streaks ALTER longest_streak SET DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trophy ALTER perfect_chapter DROP DEFAULT');
        $this->addSql('ALTER TABLE trophy ALTER module_master DROP DEFAULT');
        $this->addSql('ALTER TABLE trophy ALTER flawless_streak DROP DEFAULT');
        $this->addSql('ALTER TABLE user_streaks ALTER longest_streak DROP DEFAULT');
    }
}
