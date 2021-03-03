<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        list($sort, $type, $query, $item) = getParams($request,'title,price,created_at');
        $products = Product::select(['id','title','price','image','description','created_at'])
            ->when($query, function ($sql) use ($query) {
                $sql->where('title', 'LIKE', "%$query%")
                    ->orWhere('price', 'LIKE', "%$query%")
                    ->orWhere('description', 'LIKE', "%$query%");
            })
            ->orderBy($sort,$type)
            ->paginate($item);
        return response()->json($products, Response::HTTP_PARTIAL_CONTENT);
    }

    /**
     * @param Product $product
     * @return Product
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $values = $request->validate([
            'title' => 'required|string|min:4|max:100|unique:'.Product::getTableName(),
            'price' => 'required|numeric|min:0|max:999999',
            'description' => 'nullable|string|min:2|max:999',
            'file' => 'nullable|image|mimes:jpg,jpeg,png|min:10|max:4096'
        ]);
        if($request->hasFile('file')) {
            $values['image'] = fileUpload($request->file('file'));
        }
        $product = Product::create($values);
        return response()->json($product, 201);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        $values = $request->validate([
            'title' => 'required|string|min:4|max:100|unique:'.Product::getTableName().',title,'.$product->id,
            'price' => 'required|numeric|min:0|max:999999',
            'description' => 'nullable|string|min:2|max:999',
            'file' => 'nullable|image|mimes:jpg,jpeg,png|min:10|max:4096'
        ]);
        if($request->hasFile('file')) {
            $values['image'] = fileUpload($request->file('file'), $product);
        }
        $product->update($values);
        return response()->json($product,Response::HTTP_OK);
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|int
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            if($product->image) {
                fileDelete($product->image);
            }
            return response()->json([
                'success' => $product->title . ' has been deleted'
            ],Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $product->name . ' is not delete'
            ],Response::HTTP_IM_USED);
        }
    }
}
