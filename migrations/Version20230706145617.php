<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706145617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F271C3F9986C');
        $this->addSql('DROP INDEX IDX_6674F271C3F9986C ON mark');
        $this->addSql('ALTER TABLE mark CHANGE receipe_id recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F27159D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_6674F27159D8A214 ON mark (recipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F27159D8A214');
        $this->addSql('DROP INDEX IDX_6674F27159D8A214 ON mark');
        $this->addSql('ALTER TABLE mark CHANGE recipe_id receipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F271C3F9986C FOREIGN KEY (receipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_6674F271C3F9986C ON mark (receipe_id)');
    }
}
