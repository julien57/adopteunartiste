<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200417133546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment_post (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_post_post (comment_post_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_7AA1EAD0B8FA613C (comment_post_id), INDEX IDX_7AA1EAD04B89032C (post_id), PRIMARY KEY(comment_post_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment_post_post ADD CONSTRAINT FK_7AA1EAD0B8FA613C FOREIGN KEY (comment_post_id) REFERENCES comment_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_post_post ADD CONSTRAINT FK_7AA1EAD04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment_post_post DROP FOREIGN KEY FK_7AA1EAD0B8FA613C');
        $this->addSql('DROP TABLE comment_post');
        $this->addSql('DROP TABLE comment_post_post');
    }
}
