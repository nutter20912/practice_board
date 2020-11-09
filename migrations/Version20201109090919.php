<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201109090919 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_records (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL COMMENT \'帳號id\', operator VARCHAR(50) NOT NULL COMMENT \'操作者\', diff INT NOT NULL COMMENT \'異動額度\', current INT NOT NULL COMMENT \'異動後額度\', ip VARCHAR(255) NOT NULL COMMENT \'ip\', created_at DATETIME NOT NULL COMMENT \'新增時間\', INDEX IDX_C2714A1AA76ED395 (user_id), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, version INT DEFAULT 1 NOT NULL, account VARCHAR(30) NOT NULL COMMENT \'帳號\', cash INT NOT NULL COMMENT \'額度\', created_at DATETIME NOT NULL COMMENT \'新增時間\', updated_at DATETIME on update CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_8D93D6497D3656A4 (account), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE cash_records ADD CONSTRAINT FK_C2714A1AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message CHANGE updated_at updated_at DATETIME on update CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_records DROP FOREIGN KEY FK_C2714A1AA76ED395');
        $this->addSql('DROP TABLE cash_records');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE message CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }
}
