<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SellerScope implements Scope
{
  /*  this is adding query parameter to the buyer class..Here this scope is adding a query if the seller has a product,
    to the seller model.So when we will search for a seller in the controller , it will autometically find those users who have any product */
    public function apply(Builder $builder,Model $model)
    {
        $builder->has('products');
    }
}
