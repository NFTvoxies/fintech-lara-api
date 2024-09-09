<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Transfer;

class PaymentController extends Controller
{
    public function deposit(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $charge = Charge::create([
            'amount' => $request->amount * 100, // Convert to cents
            'currency' => 'usd',
            'source' => $request->stripeToken,
            'description' => 'Deposit to account',
        ]);
        // Update the user's balance
        $user = $request->user();
        $user->balance += $request->amount;
        $user->save();

        // Log the transaction
        $user->transactions()->create([
            'type' => 'deposit',
            'amount' => $request->amount,
            'description' => 'Deposit via Stripe',
            'status' => 'completed',
        ]);

        return response()->json(['message' => 'Deposit successful', 'charge' => $charge]);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        // Deduct from balance
        $user->balance -= $request->amount;
        $user->save();

        // Log the transaction
        $user->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'description' => 'Withdrawal',
            'status' => 'completed',
        ]);

        return response()->json(['message' => 'Withdrawal successful']);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $recipient = User::where('email', $request->recipient_email)->first();

        // Check if the user has enough balance
        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        // Deduct from the sender's balance
        $user->balance -= $request->amount;
        $user->save();

        // Add to the recipient's balance
        $recipient->balance += $request->amount;
        $recipient->save();

        // Log the transactions
        $user->transactions()->create([
            'type' => 'transfer',
            'amount' => $request->amount,
            'description' => 'Transfer to ' . $recipient->email,
            'status' => 'completed',
        ]);

        $recipient->transactions()->create([
            'type' => 'transfer',
            'amount' => $request->amount,
            'description' => 'Transfer from ' . $user->email,
            'status' => 'completed',
        ]);

        return response()->json(['message' => 'Transfer successful']);
    }
}
