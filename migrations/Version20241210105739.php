<?php

declare(strict_types=1);

namespace EventSourcingMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210105739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE eventstore (id BIGINT GENERATED BY DEFAULT AS IDENTITY NOT NULL, aggregate VARCHAR(255) NOT NULL, aggregate_id UUID NOT NULL, playhead INT NOT NULL, event VARCHAR(255) NOT NULL, payload JSON NOT NULL, recorded_on TIMESTAMP(0) WITH TIME ZONE NOT NULL, new_stream_start BOOLEAN DEFAULT false NOT NULL, archived BOOLEAN DEFAULT false NOT NULL, custom_headers JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDB964C7B77949FFD0BBCCBE34B91FA9 ON eventstore (aggregate, aggregate_id, playhead)');
        $this->addSql('CREATE INDEX IDX_BDB964C7B77949FFD0BBCCBE34B91FA961B169FE ON eventstore (aggregate, aggregate_id, playhead, archived)');
        $this->addSql('CREATE TABLE subscriptions (id VARCHAR(255) NOT NULL, group_name VARCHAR(32) NOT NULL, run_mode VARCHAR(16) NOT NULL, position INT NOT NULL, status VARCHAR(32) NOT NULL, error_message TEXT DEFAULT NULL, error_previous_status VARCHAR(32) DEFAULT NULL, error_context JSON DEFAULT NULL, retry_attempt INT NOT NULL, last_saved_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4778A0177792576 ON subscriptions (group_name)');
        $this->addSql('CREATE INDEX IDX_4778A017B00651C ON subscriptions (status)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE eventstore');
        $this->addSql('DROP TABLE subscriptions');
    }
}
