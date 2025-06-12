<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends Controller
{

    public function createPaymentIntent(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $transaction = Transaction::findOrFail($request->transaction_id);

            $paymentIntent = PaymentIntent::create([
                'amount' => intval($transaction->final_total * 100), // Stripe pakai satuan cent
                'currency' => 'idr',
                'metadata' => [
                    'transaction_id' => $transaction->id,
                ],
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret'); // dari dashboard Stripe

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $secret);

            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;

                $transactionId = $paymentIntent->metadata->transaction_id;

                $transaction = Transaction::find($transactionId);
                if ($transaction) {
                    $transaction->status = 'paid';
                    $transaction->save();
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
