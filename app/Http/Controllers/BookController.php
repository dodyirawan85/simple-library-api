<?php

namespace App\Http\Controllers;

use App\Models\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
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
     * * Get Books
     */
    public function index()
    {
        $books = Book::with('categories:id,name')->get();

        $response = compile_response('Get Books Success', Response::HTTP_OK, $books);

        return response()->json($response, $response['status_code']);
    }

    /**
     * * Store Book
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $rules = [
            'title' => 'required|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'release_year' => 'required|date_format:Y',
            'categories' => 'required|array',
            'categories.*' => 'required|exists:categories,id'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book = new Book();
        $book->title = $input['title'];
        $book->author = $input['author'];
        $book->publisher = $input['publisher'];
        $book->release_year = $input['release_year'];

        if ($book->save()) {
            $pivotData = [];

            foreach ($input['categories'] as $key => $value) {
                $row = [
                    'book_id' => $book->id,
                    'category_id' => $value
                ];

                array_push($pivotData, $row);
            }

            $book->categories()->attach($pivotData);

            foreach ($book->categories as $category) {
                # code...
                $category->pivot;
            }

            $response = compile_response('Create Book Success', Response::HTTP_CREATED, $book);

            return response()->json($response, $response['status_code']);
        }

        $response = compile_response('Create Book Failed', Response::HTTP_INTERNAL_SERVER_ERROR);

        return response()->json($response, $response['status_code']);
    }

    /**
     * * Get Book
     */
    public function show (Request $request, $id)
    {
        $book = Book::with('categories:id,name')->find($id);

        if ($book) {
            $response = compile_response('Get Book Success', Response::HTTP_OK, $book);

            return response()->json($response, $response['status_code']);
        }

        $response = compile_response('Book Not Found', Response::HTTP_NOT_FOUND);

        return response()->json($response, $response['status_code']);
    }

    /**
     * * Update Book
     */
    public function update(Request $request, $id)
    {
        $book = Book::with('categories:id,name')->find($id);

        if (!$book) {
            $response = compile_response('Book Not Found', Response::HTTP_NOT_FOUND);

            return response()->json($response, $response['status_code']);
        }

        $input = $request->all();

        $rules = [
            'title' => 'required|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'release_year' => 'required|date_format:Y',
            'categories' => 'required|array',
            'categories.*' => 'required|exists:categories,id'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book->title = $input['title'];
        $book->author = $input['author'];
        $book->publisher = $input['publisher'];
        $book->release_year = $input['release_year'];

        $bookCat = [];
        foreach ($book->categories as $category) {
            $bookCat = array_merge($bookCat, [$category->id]);
        }

        // dd($bookCat);

        $removedCat = array_filter($bookCat, function($cat) use ($input) {
            return !in_array($cat, $input['categories']);
        });

        if ($removedCat && count($removedCat)) {
            $book->categories()->detach($removedCat);

            $addedCat = array_filter($input['categories'], function($cat) use ($removedCat, $bookCat) {
                return !in_array($cat, $removedCat) && !in_array($cat, $bookCat);
            });

            $book->categories()->attach($addedCat);
        } else {
            $addedCat = array_filter($input['categories'], function($cat) use ($bookCat) {
                return !in_array(intval($cat), $bookCat);
            });

            $book->categories()->attach($addedCat);
        }

        if ($book->save()) {
            $book->refresh();

            $response = compile_response('Update Book Success', Response::HTTP_OK, $book);

            return response()->json($response, $response['status_code']);
        }

        $response = compile_response('Update Book Failed', Response::HTTP_INTERNAL_SERVER_ERROR);

        return response()->json($response, $response['status_code']);
    }

    /**
     * * Delete Book
     */
    public function destroy($id)
    {
        $book = Book::with('categories:id,name')->find($id);

        if (!$book) {
            $response = compile_response('Book Not Found', Response::HTTP_NOT_FOUND);

            return response()->json($response, $response['status_code']);
        }

        $book->categories()->detach();

        if ($book->delete()) {
            $response = compile_response('Delete Book Success', Response::HTTP_OK);

            return response()->json($response, $response['status_code']);
        }

        $response = compile_response('Delete Book Failed', Response::HTTP_INTERNAL_SERVER_ERROR);

        return response()->json($response, $response['status_code']);
    }
}
