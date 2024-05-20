<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $product = Products::all();
        return response()->json([
            'success' => true,
            'message' => 'data product',
            'data' => $product
        ]);
    }


    public function store(Request $request)
    {
        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'required|file:mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'expired_at' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Insert Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Categories::where('name', $request->category_id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'category not found'
            ], 400);
        }

        $hash_image = $request->image->hashName();
        $request->image->move(public_path('uploads/products'), $hash_image);

        Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $hash_image,
            'category_id' => $category->id,
            'expired_at' => $request->expired_at,
            'modifed_by' => auth()->user()->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insert Success',
        ]);
    }


    public function update(Request $request, $id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'product not found'
            ], 400);
        }

        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'file:mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'expired_at' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Insert Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Categories::where('name', $request->category_id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'category not found'
            ], 400);
        }

        $image = $product->image;
        // old image


        if ($request->image) {
            $hash_image = $request->image->hashName();
            $request->image->move(public_path('uploads/products'), $hash_image);
            unlink(public_path('uploads/products/') . $product->image);

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $category->id,
                'expired_at' => $request->expired_at,
                'modifed_by' => auth()->user()->email,
                'image' => $image
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Update Success and Change Image',
                'data' => $request->all()
            ]);
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $category->id,
            'expired_at' => $request->expired_at,
            'modifed_by' => auth()->user()->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Update Success',
            'data' => $request->all()
        ]);
    }


    public function destroy($id)
    {

        $product = Products::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'product not found'
            ], 400);
        }

        // soft delete
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delete Success'
        ]);
    }
}
