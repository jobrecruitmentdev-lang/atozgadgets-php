<?php
$models = ['Cart', 'Wishlist', 'Coupon', 'ProductReview', 'Banner', 'Offer', 'Payment', 'Shipment'];
foreach ($models as $model) {
    $path = __DIR__ . '/app/Models/' . $model . '.php';
    if(file_exists($path)) {
        $content = file_get_contents($path);
        if(strpos($content, '$guarded') === false) {
            $content = str_replace('use HasFactory;', "use HasFactory;\n    protected \$guarded = ['id'];", $content);
            file_put_contents($path, $content);
        }
    }
}

// Update migrations
$files = glob(__DIR__ . '/database/migrations/*_create_carts_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->foreignId("user_id")->constrained("users"); $table->foreignId("product_id")->constrained("products"); $table->integer("quantity")->default(1);', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_wishlists_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->foreignId("user_id")->constrained("users"); $table->foreignId("product_id")->constrained("products");', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_coupons_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->string("code")->unique(); $table->string("type"); $table->decimal("value", 8, 2); $table->date("expiry_date")->nullable();', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_product_reviews_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->foreignId("user_id")->constrained("users"); $table->foreignId("product_id")->constrained("products"); $table->integer("rating"); $table->text("review")->nullable();', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_banners_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->string("image_url"); $table->string("link")->nullable(); $table->boolean("is_active")->default(true);', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_offers_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->string("title"); $table->text("description")->nullable(); $table->dateTime("start_date"); $table->dateTime("end_date");', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_payments_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    // Added Payoneer fields
    $content = str_replace('$table->id();', '$table->id(); $table->foreignId("order_id")->constrained("orders"); $table->decimal("amount", 10, 2); $table->string("payment_method")->default("payoneer"); $table->string("payoneer_transaction_id")->nullable(); $table->string("status");', $content);
    file_put_contents($files[0], $content);
}

$files = glob(__DIR__ . '/database/migrations/*_create_shipments_table.php');
if($files) {
    $content = file_get_contents($files[0]);
    $content = str_replace('$table->id();', '$table->id(); $table->foreignId("order_id")->constrained("orders"); $table->string("tracking_number")->nullable(); $table->string("carrier")->nullable(); $table->string("status");', $content);
    file_put_contents($files[0], $content);
}

echo "Models and migrations updated.";
