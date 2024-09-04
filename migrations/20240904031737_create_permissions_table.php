<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('permissions', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', [
                'identity' => true,
                'null' => false
            ])
            ->addColumn('name', 'string', [
                'limit' => 50,
                'null' => false
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('permissions')->drop()->save();
    }
}
