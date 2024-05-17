<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Categories::all();
        return response()->json([
            'success' => true,
            'message' => 'list name category',
            'data' => $category
        ]);
    }


    public function store(Request $request)
    {
        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Insert Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        Categories::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insert Success',
            'data' => $request->all()
        ]);
    }


    public function update(Request $request, $id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'category not found'
            ], 400);
        }

        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Insert Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Update Success',
            'data' => $request->all()
        ]);
    }


    public function destroy($id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'category not found'
            ], 400);
        }

        if ($category->products()->get()->count() > 0) {
            $category->products()->delete();
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Success'
        ], 200);
    }
}
