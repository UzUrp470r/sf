<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210613113456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE public.user ALTER COLUMN id SET DEFAULT nextval('user_id_seq'::regclass)");  //Fix @ORM\GeneratedValue(strategy="IDENTITY")
        $this->addSql('INSERT INTO public.user(uuid, roles) VALUES (?, ?)', ['b1755603-d9ff-42aa-8d28-153bc06bcb84', '{"0":"ROLE_USER"}']);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
