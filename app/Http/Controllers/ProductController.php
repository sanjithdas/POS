<?php

namespace App\Http\Controllers;

/**
 * @author [sanjith]
 * @email [example@mail.com]
 * @create date 2024-06-13 21:36:51
 * @modify date 2024-06-13 21:38:40
 * @desc [description]
 */

use App\Jobs\SendProductNotification;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        try {

            $this->authorize('viewAny', Product::class);

            // Check if a search query is provided
            $searchTerm = $request->query('search');

            $productsQuery = Product::select('id', 'name', 'price', 'description', 'image') // Select specific fields
            ->orderBy('id', 'desc');

            // If a search term exists, filter products by name or other attributes
            if ($searchTerm) {
                $productsQuery->where('name', 'LIKE', '%' . $searchTerm . '%')
                              ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            }

            // Paginate the results
            $products = $productsQuery->paginate(10);

            return response()->json($products);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized for this action.'], 403);
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {

        $loggedInUser = Auth::id();
        try {
            $this->authorize('create', Product::class);

            $data = $request->validate([
                "name" => "required|string|max:255",
                "price" => "required|numeric|min:0",
                "description" => "required|string",
                "stock" => "required|integer|min:0",
                "image" => "required|image|max:2048", // Validate as an image file with a max size of 2MB
                "user_id" => [
                    'required',
                    'integer',
                    Rule::exists('users', 'id')->where(function ($query) use ($loggedInUser) {
                        return $query->where('id', $loggedInUser);
                    }),
                ],
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $data['image'] = url('images/' . $imageName); // Store the filename as a string.
            }


            $product = Product::create($data);
           SendProductNotification::dispatch($product,'created',auth()->user());
            return response()->json(["data" => $product], 201);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized to create products.'], 403);
        }
    }

    public function show(Product $product)
    {

        try{

        $product = Product::findOrFail($product->id);
        $this->authorize('view', $product);

        return response()->json($product);
        } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Product not found'], 404);
        }catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized to create products.'], 403);
        }
    }
    public function edit(Product $product)
    {
    }
    public function update(Request $request, Product $product)
    {
        try {

            $product = Product::findOrFail($product->id);

            // if (auth->user()->cannot('update', $product)) {
            //     abort(403);
            // }

            //Gate::authorize('update', $post);

            $this->authorize('update', $product);

            $n = $request->all();

            $data = $request->validate([
                "name" => "required|string|max:255",
                "price" => "required|numeric|min:0",
                "description" => "required|string",
                "stock" => "required|integer|min:0",
                "image" => "required|image|max:2048", // Validate as an image file with a max size of 2MB
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $data['image'] = url('storage/images/' . $imageName);  // Store the filename as a string.
            }

            if ($product) {
                $product->update($data);
                SendProductNotification::dispatch($product,'updated',auth()->user());
                return response()->json(['data' => $product]);
            } else {
                // Handle the case where the product is not found.
                return response()->json(['error' => 'Product not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized to create products.'], 403);
        }
    }
    public function destroy(Product $product)
    {
        try {
            $product = Product::findOrFail($product->id);
            $this->authorize('delete', $product);

            // If authorization is  , proceed with deletion
            $product =  $product->delete();
            SendProductNotification::dispatch($product,'deleted',auth()->user());
            return response()->json("Product deleted successfully", 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }
}
