<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function showBalance(Request $request)
    {
        $user = $request->user();
        return response()->json(['balance' => $user->balance]);
    }

    public function recentTransactions(Request $request)
    {
        $user = $request->user();
        $transactions = $user->transactions()->latest()->take(10)->get();

        return response()->json($transactions);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $totalDeposits = $user->transactions()->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $user->transactions()->where('type', 'withdrawal')->sum('amount');

        return response()->json([
            'balance' => $user->balance,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'recentTransactions' => $user->transactions()->latest()->take(10)->get(),
        ]);
    }
}
