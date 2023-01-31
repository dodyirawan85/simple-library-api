<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Request;

use App\Models\Category;
use Illuminate\Http\Response;
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
        //
    }

    /**
     * * Get All Categories
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        $response = [
            'message' => 'Get categories success',
            'status_code' => Response::HTTP_OK,
            'data' => $categories
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * * Create Category
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = Category::create($input);

        if ($category) {
            $response = [
                'message' => 'Create category success',
                'status_code' => Response::HTTP_CREATED,
                'data' => $category
            ];

            return response()->json($response, Response::HTTP_OK);
        }

        $response = [
            'message' => 'Create category failed ',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * * Show Category
     */
    public function show(Request $request, $id)
    {
        $category = Category::find($id);

        if ($category) {
            $response = [
                'message' => 'Get category success',
                'status_code' => Response::HTTP_OK,
                'data' => $category
            ];

            return response()->json($response, Response::HTTP_OK);
        }

        $response = [
            'message' => 'Category not found',
            'status_code' => Response::HTTP_NOT_FOUND
        ];

        return response()->json($response, Response::HTTP_NOT_FOUND);
    }
}
