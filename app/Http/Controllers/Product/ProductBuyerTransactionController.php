<?php

namespace App\Http\Controllers\Product;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Product;
use App\Seller;
use App\Transaction;
use App\Transformers\ProductTransformer;
use App\User;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store']);
        $this->middleware('scope:purchase-product')->only(['store']);
        $this->middleware('can:purchase,buyer')->only('store');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request, $rules);
        if (!$buyer->id == $product->seller_id) {
            return $this->errorResponse('the buyer must be different from seller', 409);
        }
        if (!$buyer->isVerified()) {
            return $this->errorResponse('the buyer must be a verified user', 409);
        }
        if (!$product->seller->isVerified()) {
            return $this->errorResponse('the seller must be a verified user', 409);
        }
        if (!$product->isAvailable()) {
            return $this->errorResponse('the product is not available', 409);
        }
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('this product does not have enough units for this transaction', 409);
        }
        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);
            return $this->showOne($transaction, 201);
        });
    }
}
