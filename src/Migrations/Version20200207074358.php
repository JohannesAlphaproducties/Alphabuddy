<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200207074358 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company ADD fax VARCHAR(255) DEFAULT NULL, ADD website VARCHAR(255) DEFAULT NULL, ADD type INT DEFAULT NULL, ADD employees INT DEFAULT NULL, ADD revenue NUMERIC(10, 10) DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD billing_address VARCHAR(255) DEFAULT NULL, ADD billing_zip VARCHAR(255) DEFAULT NULL, ADD billing_town VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD tl_id INT NOT NULL, DROP address, DROP postalcode, DROP location, CHANGE phone phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F2280E58B ON company (tl_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE work_orders CHANGE company_id company_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_4FBF094F2280E58B ON company');
        $this->addSql('ALTER TABLE company ADD address VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD postalcode VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD location VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP fax, DROP website, DROP type, DROP employees, DROP revenue, DROP description, DROP billing_address, DROP billing_zip, DROP billing_town, DROP email, DROP tl_id, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE work_orders CHANGE company_id company_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
