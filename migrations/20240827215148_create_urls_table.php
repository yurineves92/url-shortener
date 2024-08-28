<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUrlsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('urls', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', [
                'identity' => true,
                'null' => false
            ])
            ->addColumn('uuid', 'string', [
                'limit' => 36
            ])
            ->addColumn('user_id', 'integer', [
                'limit' => 16,
                'null' => true
            ])
            ->addColumn('long_url', 'text')
            ->addColumn('short_url_path', 'string', [
                'limit' => 15
            ])
            ->addColumn('type', 'enum', [
                'values' => ['RANDOM', 'CUSTOM'],
                'default' => 'RANDOM'
            ])
            ->addColumn('economy_rate', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => 0.00
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
            ->addIndex(['short_url_path'], [
                'unique' => true
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('urls')->drop()->save();
    }
}
