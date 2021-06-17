<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210613112346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('INSERT INTO public.company(name) VALUES (?)', ['Test Company']);
        $this->addSql('INSERT INTO public.company(name) VALUES (?)', ['Another Test Company']);
        $this->addSql('INSERT INTO public.company(name) VALUES (?)', ['Third Company']);

        $this->addSql('INSERT INTO public.client(first_name, last_name) VALUES (?, ?)', ['John', 'Doe']);
        $this->addSql('INSERT INTO public.client(first_name, last_name) VALUES (?, ?)', ['Jane', 'Doe']);
        $this->addSql('INSERT INTO public.client(first_name, last_name) VALUES (?, ?)', ['Doe', 'John']);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
