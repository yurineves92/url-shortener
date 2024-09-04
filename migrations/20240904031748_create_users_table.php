<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', [
                'identity' => true,
                'null' => false
            ])
            ->addColumn('email', 'string', [
                'limit' => 100,
                'null' => false
            ])
            ->addColumn('password', 'string', [
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('active', 'boolean', [
                'default' => true,
                'null' => false
            ])
            ->addColumn('role_id', 'integer', [
                'null' => false
            ])
            ->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION'
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}
