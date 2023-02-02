<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        $data_raw = Transaction::query()
            ->when(Auth::user()->role == 'member', function ($query) {
                $query->where('transactions.user_id', Auth::user()->id);
            })
            ->with('user:id,name')
            ->with('book:id,title')
            ->paginate(10);

        $data = [
            'current_page' => $data_raw->currentPage(),
            'total' => $data_raw->total(),
            'total_page' => $data_raw->lastPage(),
            'items' => $data_raw->items()
        ];

        $response = compile_response('Success', Response::HTTP_OK, $data);

        return response()->json($response, $response['status_code']);
    }

    public function show($id)
    {
        $transaction = Transaction::query()
            ->where('id', $id)
            ->when(Auth::user()->role == 'member', function ($query) {
                $query->where('transactions.user_id', Auth::user()->id);
            })
            ->first();

        $message = "Not Found";
        $status_code = Response::HTTP_NOT_FOUND;

        if ($transaction) {
            $transaction->load('user:id,name');
            $transaction->load('book:id,title');
            $message = "Success";
            $status_code = Response::HTTP_OK;
        }

        $response = compile_response($message, $status_code, $transaction);

        return response()->json($response, $response['status_code']);
    }

    public function store()
    {
        $res = validate_role(['admin', 'librarian']);
        if ($res)
            return $res;

        $msg = 'Create Transaction Failed !';
        $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;

        $input = request()->all();
        $validator = Validator::make($input, [
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'days' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $days = $input['days'];

        $transaction = new Transaction();
        $transaction->book_id = $input['book_id'];
        $transaction->user_id = $input['user_id'];
        $transaction->returned_date = date('Y-m-d H:i:s', strtotime("+$days days"));
        $transaction->status = "ONLOAN";

        if ($transaction->save()) {
            $msg = 'Create Book Success !';
            $status_code = Response::HTTP_CREATED;
            $transaction->load('user:id,name');
            $transaction->load('book:id,title');
        } else
            $transaction = null;

        $response = compile_response($msg, $status_code, $transaction);

        return response()->json($response, $response['status_code']);
    }

    public function update(Request $request, $id)
    {
        $res = validate_role(['admin', 'librarian']);
        if ($res)
            return $res;

        $msg = 'Create Transaction Failed !';
        $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;

        $transaction = Transaction::find($id);

        if (!$transaction) {
            $response = compile_response('Transaction Not Found', Response::HTTP_NOT_FOUND);

            return response()->json($response, $response['status_code']);
        }

        $transaction->status = "RETURNED";
        if ($transaction->save()) {
            $msg = 'Update Book Success !';
            $status_code = Response::HTTP_OK;
        }

        $response = compile_response($msg, $status_code, $transaction);

        return response()->json($response, $response['status_code']);
    }
}
