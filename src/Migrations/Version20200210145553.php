<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200210145553 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, company_id INT DEFAULT NULL, priority INT NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, content_changed DATETIME DEFAULT NULL, INDEX IDX_97A0ADA3A76ED395 (user_id), INDEX IDX_97A0ADA3979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_user (ticket_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BF48C371700047D2 (ticket_id), INDEX IDX_BF48C371A76ED395 (user_id), PRIMARY KEY(ticket_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE fax fax VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE type type INT DEFAULT NULL, CHANGE employees employees INT DEFAULT NULL, CHANGE revenue revenue NUMERIC(10, 10) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE billing_address billing_address VARCHAR(255) DEFAULT NULL, CHANGE billing_zip billing_zip VARCHAR(255) DEFAULT NULL, CHANGE billing_town billing_town VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE work_orders CHANGE company_id company_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL, CHANGE worked_hours worked_hours NUMERIC(5, 2) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ticket_user DROP FOREIGN KEY FK_BF48C371700047D2');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_user');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE fax fax VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE type type INT DEFAULT NULL, CHANGE employees employees INT DEFAULT NULL, CHANGE revenue revenue NUMERIC(10, 10) DEFAULT \'NULL\', CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE billing_address billing_address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE billing_zip billing_zip VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE billing_town billing_town VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE work_orders CHANGE company_id company_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE worked_hours worked_hours NUMERIC(5, 2) DEFAULT \'NULL\'');
    }
}
