<?php

namespace App\Providers;

use App\Buyer;
use App\Policies\BuyerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SellerPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Product;
use App\Seller;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Buyer::class => BuyerPolicy::class,
        Seller::class=>SellerPolicy::class,
        User::class => UserPolicy::class,
        Transaction::class =>TransactionPolicy::class,
        Product::class =>ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-action',function($user){
            return $user->isAdmin();
        });

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();

        Passport::tokensCan([
            'purchase-product' => 'create a new transaction for a specific product',
            'manage-products' => 'create,read, update and delete products(CRUD)',
            'manage-account' => 'Rear your account data,id,email,name,if verified,if admin(can not read password).Modify your account(Email and password).Can not delete your account',
            'read-general' => 'Read general information like purchasing categories,purchased products,selling products,selling categories,your transactions(purchases and sells)',
        ]);
    }
}
