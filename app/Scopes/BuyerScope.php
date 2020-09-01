<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BuyerScope implements Scope
{
    /* buyer scope adds the query parameter that if any user have a transaction ,
     then that user will be a buyer or not. so now we do not have to query that
      id a user has a transaction in the buyer controller */
    public function apply(Builder $builder,Model $model)
    {
        $builder->has('transactions');
    }
}
