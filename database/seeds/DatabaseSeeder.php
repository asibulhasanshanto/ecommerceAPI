<?php

use App\Category;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        //deleting all the data of all tables
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        //disabling event listener in seeder
        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        //specifying how much data we want to create
        $userQuantity = 1000;
        $categoryQuantity = 30;
        $productQuantity = 1000;
        $transactionQuantity = 1000;

        factory(User::class, $userQuantity)->create();
        factory(Category::class,$categoryQuantity)->create();

        factory(Product::class,$productQuantity)->create()->each(
            /*for each product, first we are taking a random number, then taking that much categories and assigining to this product. */
            function($product){
                $categories = Category::all()->random(mt_rand(1,5))->pluck('id');//mt_rand generates a value from 1 to 5
                /*attaching 1 to 5 categories with every product. In general word, feeding data to the pivot table */
                $product ->categories()->attach($categories);
            });

        factory(Transaction::class,$transactionQuantity)->create();

    }
}
