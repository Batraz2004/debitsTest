<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\DepositRequest;
use App\Http\Requests\Account\WithdrawRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function createDeposit(DepositRequest $request)
    {
        $user = User::query()->findOrFail($request->user_id);

        if ($user->has('account') && filled($user->account)) {
            $user->account->amount += $request->amount;
            $user->account->save();
        } else {
            $deposit = Account::create($request->getData());
        }

        return response()->json([
            'blance' => $user->account->amount ?? intval($deposit->amount),
            'message' => 'пополнение',
        ], 200);
    }

    public function getBalance($user_id)
    {
        $user = User::query()->with('account')->findOrFail($user_id);
        $balance = $user->account->amount;

        return response()->json([
            'blance' => $balance,
            'message' => 'пополнение',
        ], 200);
    }

    public function withdraw(WithdrawRequest $request)
    {
        $user = User::query()->findOrFail($request->user_id);

        if ($user->has('account') && filled($user->account)) {
            $amount = $user->account->amount;
            
            if($request->amount > $amount)
            {
                abort(403,'не достаточно средств');
            }

            $user->account->amount -= $amount;
            $user->account->save();
        }
    }
}
