<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolePermissionsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('role_permissions', ['id' => false]);
        $table
            ->addColumn('role_id', 'integer', [
                'null' => false
            ])
            ->addColumn('permission_id', 'integer', [
                'null' => false
            ])
            ->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION'
            ])
            ->addForeignKey('permission_id', 'permissions', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION'
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('role_permissions')->drop()->save();
    }
}
