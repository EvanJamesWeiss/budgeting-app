<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260714004534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, date DATE NOT NULL, is_placeholder TINYINT NOT NULL, split_ratio NUMERIC(3, 2) NOT NULL, template_id INT DEFAULT NULL, paid_by_id INT NOT NULL, INDEX IDX_2D3A8DA65DA0FB8 (template_id), INDEX IDX_2D3A8DA67F9BC654 (paid_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE expense_template (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, default_amount NUMERIC(10, 2) DEFAULT NULL, is_static TINYINT NOT NULL, default_split_ratio NUMERIC(3, 2) NOT NULL, paid_by_id INT NOT NULL, INDEX IDX_5101DD7A7F9BC654 (paid_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE settlement (id INT AUTO_INCREMENT NOT NULL, month_year VARCHAR(7) NOT NULL, amount NUMERIC(10, 2) NOT NULL, timestamp DATETIME NOT NULL, paid_by_id INT NOT NULL, received_by_id INT NOT NULL, INDEX IDX_DD9F1B517F9BC654 (paid_by_id), INDEX IDX_DD9F1B516F8DDD17 (received_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA65DA0FB8 FOREIGN KEY (template_id) REFERENCES expense_template (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA67F9BC654 FOREIGN KEY (paid_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE expense_template ADD CONSTRAINT FK_5101DD7A7F9BC654 FOREIGN KEY (paid_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE settlement ADD CONSTRAINT FK_DD9F1B517F9BC654 FOREIGN KEY (paid_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE settlement ADD CONSTRAINT FK_DD9F1B516F8DDD17 FOREIGN KEY (received_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA65DA0FB8');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA67F9BC654');
        $this->addSql('ALTER TABLE expense_template DROP FOREIGN KEY FK_5101DD7A7F9BC654');
        $this->addSql('ALTER TABLE settlement DROP FOREIGN KEY FK_DD9F1B517F9BC654');
        $this->addSql('ALTER TABLE settlement DROP FOREIGN KEY FK_DD9F1B516F8DDD17');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_template');
        $this->addSql('DROP TABLE settlement');
        $this->addSql('DROP TABLE `user`');
    }
}
