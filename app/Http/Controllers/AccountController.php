<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\DepositRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function createDeposit(DepositRequest $request)
    {
        try {
            $user = User::query()->firstWhere('id', $request->user_id);

            if ($user->has('account') && filled($user->account)) {
                $user->account->amount += $request->amount;
                $user->account->save();
            } else {
                $deposit = Account::create($request->getData());
            }

            return response()->json([
                'data' => $request->getData(),
                'message' => 'пополнение',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'data' => 'произошла ошибка',
                'error_message' => $th->getMessage(),
            ], $th->getCode());
        }
    }
}
