<?php

namespace App\Services;

use App\Models\ETicket;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function configure(): void
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized');
        Config::$is3ds = (bool) config('services.midtrans.is_3ds');
    }

    public function ensureSnapToken(Order $order): Payment
    {
        $order->loadMissing('user', 'concert', 'ticketZone', 'payment');
        $payment = $order->payment ?: $order->payment()->create([
            'total_amount' => $order->total_price,
            'payment_method' => 'midtrans',
        ]);

        if ($payment->payment_status === 'success') {
            return $payment;
        }

        if ($payment->payment_status === 'pending' && filled($payment->snap_token)) {
            return $payment;
        }

        $this->configure();
        $gatewayOrderId = $order->order_code.'-'.Str::upper(Str::random(5));
        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $gatewayOrderId,
                'gross_amount' => (int) $payment->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'item_details' => [[
                'id' => (string) $order->ticket_zone_id,
                'price' => (int) ($order->ticketZone?->price ?? $order->total_price),
                'quantity' => (int) $order->ticket_quantity,
                'name' => Str::limit($order->concert->name.' - '.($order->ticketZone?->name ?? 'Tiket'), 50, ''),
            ]],
            'callbacks' => [
                'finish' => route('midtrans.finish', ['order' => $order->order_code]),
            ],
        ]);

        $payment->update([
            'gateway_order_id' => $gatewayOrderId,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
            'snap_token' => $snapToken,
            'snap_redirect_url' => $this->snapRedirectUrl($snapToken),
        ]);

        return $payment->fresh();
    }

    public function syncFromStatusObject(object|array $status): Payment
    {
        $payload = json_decode(json_encode($status), true);
        $payment = Payment::with('order')->where('gateway_order_id', $payload['order_id'] ?? null)->firstOrFail();

        if (isset($payload['gross_amount']) && (int) round((float) $payload['gross_amount']) !== (int) $payment->total_amount) {
            throw new \RuntimeException('Midtrans gross amount does not match local payment amount.');
        }

        return DB::transaction(function () use ($payment, $payload) {
            $payment->refresh();
            $status = $this->mapPaymentStatus(
                $payload['transaction_status'] ?? null,
                $payload['fraud_status'] ?? null
            );

            $payment->update([
                'payment_method' => $payload['payment_type'] ?? $payment->payment_method,
                'payment_status' => $status,
                'payment_date' => now(),
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $payment->midtrans_transaction_id,
                'midtrans_payment_type' => $payload['payment_type'] ?? $payment->midtrans_payment_type,
                'midtrans_payload' => $payload,
            ]);

            $this->syncOrderAfterPayment($payment);

            return $payment->fresh('order.eTickets');
        });
    }

    public function syncFromMidtrans(Payment $payment): Payment
    {
        $this->configure();
        $status = Transaction::status($payment->gateway_order_id ?: $payment->order->order_code);

        return $this->syncFromStatusObject($status);
    }

    public function validSignature(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $signature = hash('sha512', ($payload['order_id'] ?? '').($payload['status_code'] ?? '').($payload['gross_amount'] ?? '').$serverKey);

        return hash_equals($signature, (string) ($payload['signature_key'] ?? ''));
    }

    private function syncOrderAfterPayment(Payment $payment): void
    {
        if ($payment->payment_status === 'success') {
            $this->issueTickets($payment->order);

            return;
        }

        if ($payment->payment_status === 'failed') {
            $payment->order->update(['order_status' => 'cancelled']);

            return;
        }

        $payment->order->update(['order_status' => 'pending']);
    }

    private function issueTickets(Order $order): void
    {
        $order->update(['order_status' => 'paid']);

        for ($i = $order->eTickets()->count(); $i < $order->ticket_quantity; $i++) {
            $code = 'TIX-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
            ETicket::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'concert_id' => $order->concert_id,
                'ticket_code' => $code,
                'ticket_qr_code' => $code,
                'issued_at' => now(),
            ]);
        }
    }

    private function mapPaymentStatus(?string $transactionStatus, ?string $fraudStatus): string
    {
        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && ($fraudStatus === null || $fraudStatus === 'accept'))) {
            return 'success';
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'expire', 'failure'], true)) {
            return 'failed';
        }

        return 'pending';
    }

    private function snapRedirectUrl(string $snapToken): string
    {
        $baseUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        return "{$baseUrl}/snap/v2/vtweb/{$snapToken}";
    }
}
