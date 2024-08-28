<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUrlsLogsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('urls_logs', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', [
                'identity' => true,
                'null' => false
            ])
            ->addColumn('uuid', 'string', [
                'limit' => 36
            ])
            ->addColumn('url_id', 'integer', [
                'limit' => 16
            ])
            ->addColumn('meta', 'json', [
                'null' => true
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP'
            ])
            ->addIndex(['uuid'], [
                'unique' => true
            ])
            ->create();

        $this->table('urls_logs')
            ->addForeignKey('url_id', 'urls', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('urls_logs')
            ->dropForeignKey('url_id')
            ->drop()
            ->save();
    }
}
