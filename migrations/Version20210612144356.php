<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210612144356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE public.project ADD CONSTRAINT project_client_company_check CHECK (client_id IS NOT NULL OR company_id IS NOT NULL)');
        $this->addSql("COMMENT ON CONSTRAINT project_client_company_check ON public.project IS 'Check than any of the client or company are set (NOT NULL)'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE public.project DROP CONSTRAINT project_pkey');
    }
}
