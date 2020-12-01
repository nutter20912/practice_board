<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201201040426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_records CHANGE diff diff NUMERIC(10, 3) NOT NULL COMMENT \'異動額度\', CHANGE current current NUMERIC(10, 3) NOT NULL COMMENT \'異動後額度\'');
        $this->addSql('ALTER TABLE user DROP version, CHANGE cash cash NUMERIC(10, 3) NOT NULL COMMENT \'額度\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_records CHANGE diff diff INT NOT NULL COMMENT \'異動額度\', CHANGE current current INT NOT NULL COMMENT \'異動後額度\'');
        $this->addSql('ALTER TABLE user ADD version INT DEFAULT 1 NOT NULL, CHANGE cash cash INT NOT NULL COMMENT \'額度\'');
    }
}
