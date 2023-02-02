<?php

namespace App\Http\Controllers;

use App\Models\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            $book->categories()->sync($input['categories']);
            $book->categories->makeHidden('pivot');

            $msg = 'Create Book Success !';
            $status_code = Response::HTTP_CREATED;
        } else {
            $msg = 'Create Book Failed !';
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;;
            $book = null;
        }

        $response = compile_response($msg, $status_code, $book);

        return response()->json($response, $response['status_code']);
    }

    /**
     * * Get Book
     */
    public function show($id)
    {
        $book = Book::with('categories:id,name')->find($id);

        if ($book) {
            $msg = 'Get Book Success !';
            $status_code = Response::HTTP_OK;
        } else {
            $msg = 'Book Not Found !';
            $status_code = Response::HTTP_NOT_FOUND;
            $book = null;
        }

        $response = compile_response($msg, $status_code, $book);

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

        $book->categories()->sync($input['categories']);

        if ($book->save()) {
            $book->refresh();
            $book->categories->makeHidden('pivot');

            $msg = 'Update Book Success !';
            $status_code = Response::HTTP_OK;
        } else {
            $msg = 'Update Book Failed !';
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $book = null;
        }

        $response = compile_response($msg, $status_code, $book);

        return response()->json($response, $response['status_code']);
    }

    /**
     * * Delete Book
     */
    public function destroy($id)
    {
        $book = Book::with('categories:id,name')->find($id);

        if (!$book) {
            $msg = 'Book Not Found !';
            $status_code = Response::HTTP_NOT_FOUND;
        } else {
            $book->categories()->detach();

            if ($book->delete()) {
                $msg = 'Delete Book Success !';
                $status_code = Response::HTTP_OK;
            } else {
                $msg = 'Delete Book Failed';
                $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        }
        $response = compile_response($msg, $status_code);

        return response()->json($response, $response['status_code']);
    }
}
