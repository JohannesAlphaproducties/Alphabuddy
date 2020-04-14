<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312131018 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE fax fax VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE type type INT DEFAULT NULL, CHANGE employees employees INT DEFAULT NULL, CHANGE revenue revenue NUMERIC(10, 10) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE billing_address billing_address VARCHAR(255) DEFAULT NULL, CHANGE billing_zip billing_zip VARCHAR(255) DEFAULT NULL, CHANGE billing_town billing_town VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE hours CHANGE workorder_id workorder_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket CHANGE user_id user_id INT DEFAULT NULL, CHANGE company_id company_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD subscribed_responsible_ticket TINYINT(1) DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE subsribed_work_order subsribed_work_order TINYINT(1) DEFAULT NULL, CHANGE subscribed_ticket subscribed_ticket TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE work_orders CHANGE company_id company_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL, CHANGE signed_by signed_by VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE fax fax VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE type type INT DEFAULT NULL, CHANGE employees employees INT DEFAULT NULL, CHANGE revenue revenue NUMERIC(10, 10) DEFAULT \'NULL\', CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE billing_address billing_address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE billing_zip billing_zip VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE billing_town billing_town VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE hours CHANGE workorder_id workorder_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket CHANGE user_id user_id INT DEFAULT NULL, CHANGE company_id company_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user DROP subscribed_responsible_ticket, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE subsribed_work_order subsribed_work_order TINYINT(1) DEFAULT \'NULL\', CHANGE subscribed_ticket subscribed_ticket TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE work_orders CHANGE company_id company_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE signed_by signed_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
