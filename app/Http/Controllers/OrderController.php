<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Jobs\SendOrderNotification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;

class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Order::class);
            $orders = Order::with('products')->orderByDesc('id')->paginate(10);
            return response()->json($orders);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'You are not authorized to this actions.'
            ], 403);
        }
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(OrderRequest $request)
    {
        $totalPricePerOrder = 0;
        try {
            $this->authorize('create', Order::class);
            $user = Auth::user();
            Feature::activate('order-total-price');

            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'total_amount' => 0,
                'user_id' => $user->id
            ]);


            foreach ($request->products as $product) {
                $productModel = Product::findOrFail($product['id']);
                $totalPrice = $productModel->price * $product['quantity'];
                $order->products()->attach(
                    $product['id'],
                    ['quantity' => $product['quantity'], 'price' => $totalPrice]
                );
            }

            if (Feature::active('order-total-price')) {
                $totalPricePerOrder = $this->getTotalPricePerOrder($order);

                $order->total_amount =  $totalPricePerOrder;

                $order->save();
            }
            SendOrderNotification::dispatch($order,'created',auth()->user());
            return response()->json($order->load('products'), 201);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'You are not authorized to create products.'
            ], 403);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        try {
            if ($order) {
                $order = $order?->load('products');
                $this->authorize('view', $order);
            } else {
                // Handle the case where the product is not found.
                return response()->json(['error' => 'Order not found'], 404);
            }

            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'You are not authorized to view this order.'
            ], 403);
        }
    }

    /**
     * Update the specified order in storage.
     */
    public function update(OrderRequest $request, Order $order)
    {
        $totalPricePerOrder = 0;
        try {
            Feature::activate('order-total-price');

            $order = $order->load('products');

            $this->authorize('update', $order);

            $order->update($request->only('customer_name', 'customer_email'));

            if ($request->has('products')) {
                $order->products()->detach();
                foreach ($request->products as $product) {
                    $productModel = Product::findOrFail($product['id']);
                    $totalPrice = $productModel->price * $product['quantity'];
                    $order->products()->attach(
                        $product['id'],
                        ['quantity' => $product['quantity'], 'price' => $totalPrice]
                    );
                }
            }

            $productModel = Product::findOrFail($product['id']);
            $totalPrice = $productModel->price * $product['quantity'];
            $order->products()->updateExistingPivot(
                $product['id'],
                ['quantity' => $product['quantity'], 'price' => $totalPrice]
            );

            if (Feature::active('order-total-price')) {
                $totalPricePerOrder = $this->getTotalPricePerOrder($order);
                $order->total_amount = $totalPricePerOrder;
                $order->save();
            }
            SendOrderNotification::dispatch($order,'updated',auth()->user());
            return response()->json($order->load('products'));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'You are not authorized to view this order.'
            ], 403);
        }
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $this->authorize('delete', $order);
            $order->products()->detach();
            $order->delete();
            SendOrderNotification::dispatch($order,'deleted',auth()->user());
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'This action is unauthorized.'
            ], 403);
        }
    }

    private function getTotalPricePerOrder($order)
    {
        $totalPrice = $order->products->sum(function ($product) {
            return $product->pivot->price;
        });
        return $totalPrice;
    }
}
