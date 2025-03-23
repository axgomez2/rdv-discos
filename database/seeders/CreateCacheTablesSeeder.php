<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateCacheTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar a tabela cache diretamente usando SQL para evitar a dependência do cache
        DB::statement("
            CREATE TABLE IF NOT EXISTS `cache` (
                `key` VARCHAR(255) NOT NULL,
                `value` MEDIUMTEXT NOT NULL,
                `expiration` INT(11) NOT NULL,
                PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Criar a tabela cache_locks
        DB::statement("
            CREATE TABLE IF NOT EXISTS `cache_locks` (
                `key` VARCHAR(255) NOT NULL,
                `owner` VARCHAR(255) NOT NULL,
                `expiration` INT(11) NOT NULL,
                PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
