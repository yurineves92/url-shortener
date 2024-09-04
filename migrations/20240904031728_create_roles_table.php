<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolesTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('roles', ['id' => false, 'primary_key' => 'id']);
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
        $this->table('roles')->drop()->save();
    }
}
