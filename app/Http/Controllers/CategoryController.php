<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get All Categories
     */
    public function index()
    {
        $categories = Category::all();

        $response = compile_response('Success', Response::HTTP_OK, $categories);

        return response()->json($response, $response['status_code']);
    }

    /**
     * Create Category
     */
    public function store()
    {
        $input = request()->all();

        $validator = Validator::make($input, [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = Category::create($input);

        if ($category) {
            $message = "Success";
            $status_code = Response::HTTP_CREATED;
        } else {
            $message = "Failed";
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = compile_response($message, $status_code, $category);

        return response()->json($response, $response['status_code']);
    }

    /**
     * Show Category
     */
    public function show($id)
    {
        $category = Category::find($id);

        if ($category) {
            $message = "Success";
            $status_code = Response::HTTP_OK;
        } else {
            $message = "Not Found";
            $status_code = Response::HTTP_NOT_FOUND;
        }

        $response = compile_response($message, $status_code, $category);

        return response()->json($response, $response['status_code']);
    }

    /**
     * Update Category
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $category = Category::find($id);

        $validator = Validator::make($input, [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$category) {
            $message = "Category Not Found !";
            $status_code = Response::HTTP_NOT_FOUND;
        } else {
            DB::beginTransaction();
            try {
                $category->fill($input);
                $category->save();
                DB::commit();
                $message = "Update Category Success !";
                $status_code = Response::HTTP_OK;
            } catch (\Throwable $th) {
                DB::rollBack();
                $message = "Update Category Failed !";
                $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        }

        $response = compile_response($message, $status_code, $category);

        return response()->json($response, $response['status_code']);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            DB::beginTransaction();
            try {
                $category->delete();
                DB::commit();
                $message = "Category deleted successfully !";
                $status_code = Response::HTTP_OK;
            } catch (\Throwable $th) {
                DB::rollBack();
                $message = "Failed to delete category !";
                $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        } else {
            $message = "Category Not Found !";
            $status_code = Response::HTTP_NOT_FOUND;
        }

        $response = compile_response($message, $status_code, $category);

        return response()->json($response, $response['status_code']);
    }
}
