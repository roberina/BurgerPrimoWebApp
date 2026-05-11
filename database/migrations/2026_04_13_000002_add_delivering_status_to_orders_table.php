<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildForSqlite(['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled', 'rejected']);
            return;
        }

        // MySQL: lisa 'delivering' enum väärtus
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','ready','delivering','completed','cancelled','rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildForSqlite(['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled', 'rejected']);
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','ready','completed','cancelled','rejected') DEFAULT 'pending'");
    }

    private function rebuildForSqlite(array $statuses): void
    {
        $statusList = implode(', ', array_map(fn($s) => "'$s'", $statuses));

        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('DROP TABLE IF EXISTS "orders_new"');

        DB::statement("
            CREATE TABLE \"orders_new\" (
                \"id\" integer primary key autoincrement not null,
                \"user_id\" integer not null,
                \"order_number\" varchar not null,
                \"total_amount\" numeric not null,
                \"status\" varchar check (\"status\" in ({$statusList})) not null default 'pending',
                \"customer_notes\" text,
                \"admin_notes\" text,
                \"confirmed_at\" datetime,
                \"confirmed_by\" integer,
                \"created_at\" datetime,
                \"updated_at\" datetime,
                \"delivery_method\" varchar not null default 'takeaway',
                \"payment_intent_id\" varchar,
                \"payment_status\" varchar,
                \"payment_method\" varchar,
                \"paid_at\" datetime,
                \"courier_lat\" numeric,
                \"courier_lng\" numeric,
                \"courier_updated_at\" datetime,
                \"courier_token\" varchar,
                foreign key(\"user_id\") references \"users\"(\"id\") on delete cascade,
                foreign key(\"confirmed_by\") references \"users\"(\"id\")
            )
        ");

        DB::statement('DROP INDEX IF EXISTS "orders_new_order_number_unique"');
        DB::statement('DROP INDEX IF EXISTS "orders_new_courier_token_unique"');
        DB::statement('CREATE UNIQUE INDEX "orders_new_order_number_unique" ON "orders_new" ("order_number")');
        DB::statement('CREATE UNIQUE INDEX "orders_new_courier_token_unique" ON "orders_new" ("courier_token")');

        DB::statement('
            INSERT INTO "orders_new"
            SELECT id, user_id, order_number, total_amount, status,
                   customer_notes, admin_notes, confirmed_at, confirmed_by,
                   created_at, updated_at, delivery_method,
                   payment_intent_id, payment_status, payment_method, paid_at,
                   courier_lat, courier_lng, courier_updated_at, courier_token
            FROM "orders"
        ');

        DB::statement('DROP TABLE "orders"');
        DB::statement('ALTER TABLE "orders_new" RENAME TO "orders"');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
