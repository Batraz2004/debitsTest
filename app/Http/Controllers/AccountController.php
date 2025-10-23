<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\DepositRequest;
use App\Http\Requests\Account\TransferRequest;
use App\Http\Requests\Account\WithdrawRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function createDeposit(DepositRequest $request)
    {
        $user = User::query()->findOrFail($request->user_id);
        $balance = 0;

        DB::transaction(function () use ($user, $request, &$balance) {
            if ($user->account()->exists() && filled($user->account)) {
                $amount = $request->amount;
                $user->account->amount += $amount;
                $user->account->save();
                $balance = $user->account->amount;
            } else {
                $deposit = Account::query()->create($request->getData());
                $balance = $deposit->amount;
            }
        });

        return response()->json([
            'blance' => $balance,
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
        $balance = 0;

        DB::transaction(function () use ($user, $request, &$balance) {
            if ($user->account()->exists() && filled($user->account)) {
                $amount = $request->amount;

                if ($amount > $user->account->amount) {
                    abort(403, 'не достаточно средств');
                }

                $user->account->amount -= $amount;
                $user->account->save();

                $balance = $user->account->amount;
            }
        });

        return response()->json([
            'blance' => $balance,
            'message' => 'снятие средств',
        ], 200);
    }

    public function createTransfer(TransferRequest $request)
    {
        $user = User::query()->findOrFail($request->user_id);

        if ($user->account()->exists() && filled($user->account)) {
            $amount = $request->amount;

            if ($amount > $user->account->amount) {
                abort(403, 'не достаточно средств');
            }

            $userAddressee = User::query()->findOrFail($request->user_to_id);

            if ($userAddressee->account()->exists()) {
                DB::transaction(function () use ($user, $userAddressee, $amount) {
                    $user->account->amount -= $amount;
                    $user->account->save();

                    $userAddressee->account->amount += $amount;
                    $userAddressee->account->save();
                });
                $message = 'совершен перевод средств';
            } else
                $message = 'счет не найден';
        } else {
            abort(403, 'не достаточно средств');
        }

        return response()->json([
            'messege' => $message
        ], 200);
    }
}
