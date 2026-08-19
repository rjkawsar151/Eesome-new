<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop FK that relies on the user_product index (user_id FK)
        DB::statement('ALTER TABLE cart_items DROP FOREIGN KEY cart_items_ibfk_1');
        
        // Step 2: Add a standalone index on user_id so the FK can be re-added after
        DB::statement('ALTER TABLE cart_items ADD INDEX cart_items_user_id_idx (user_id)');
        
        // Step 3: Now safely drop the old (user_id, product_id) unique key
        DB::statement('DROP INDEX user_product ON cart_items');
        
        // Step 4: Re-add the user_id FK using the new standalone index
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT cart_items_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        
        // Step 5: Add the correct composite unique key (user_id, product_id, variant_id)
        DB::statement('ALTER TABLE cart_items ADD UNIQUE INDEX cart_user_product_variant_unique (user_id, product_id, variant_id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX cart_user_product_variant_unique ON cart_items');
        DB::statement('ALTER TABLE cart_items DROP FOREIGN KEY cart_items_ibfk_1');
        DB::statement('DROP INDEX cart_items_user_id_idx ON cart_items');
        DB::statement('ALTER TABLE cart_items ADD UNIQUE INDEX user_product (user_id, product_id)');
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT cart_items_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }
};
