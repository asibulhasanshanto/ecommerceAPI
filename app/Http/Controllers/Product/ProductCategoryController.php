<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);
        $this->middleware('auth:api')->except(['index']);
        $this->middleware('scope:manage-products')->except(['index']);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */

     //this methode will add a category with this product
    public function update(Request $request, Product $product, Category $category)
    {
        /* we can use attach,sync,and syncWithoutDetaching method to interact with many to many
        relationship
        1. attach=>can attache duplicate values
        2.sync=> deletes all the values and adds the current value
        3. syncWithoutDetaching=> doesn't attach duplicate value and also doesn't delete any of them */

        $product->categories()->syncWithoutDetaching([$category->id]);
        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product,Category $category)
    {
        if(!$product->categories()->find($category->id))
        {
            return $this->errorResponse('the specified category is not a category of that product',404);
        }
        $product->categories()->detach($category->id);
        return $this->showAll($product->categories);
    }
}
