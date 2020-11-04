<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201030032037 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE message_id message_id INT NOT NULL COMMENT \'訊息id\', CHANGE name name VARCHAR(30) NOT NULL COMMENT \'名字\', CHANGE content content VARCHAR(255) NOT NULL COMMENT \'內容\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'新增時間\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'更新時間\'');
        $this->addSql('ALTER TABLE message CHANGE author author VARCHAR(30) NOT NULL COMMENT \'作者\', CHANGE title title VARCHAR(50) NOT NULL COMMENT \'標題\', CHANGE content content LONGTEXT NOT NULL COMMENT \'內容\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'新增時間\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'更新時間\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE message_id message_id INT NOT NULL, CHANGE name name VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE content content VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE author author VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE title title VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE content content LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
