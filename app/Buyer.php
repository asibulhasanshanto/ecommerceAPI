<?php

namespace App;

use App\Scopes\BuyerScope;
use App\Transaction;


class Buyer extends User
{
    //booting scopes
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BuyerScope);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
