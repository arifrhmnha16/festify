<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Concert;
use App\Models\ETicket;
use App\Models\Officer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ScanHistory;
use App\Models\TicketZone;
use App\Models\User;
use App\Models\Wristband;
use App\Notifications\FestifyResetPassword;
use App\Notifications\FestifyVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FestifyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_is_sent_to_email_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/user/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_registration_sends_email_verification_notification(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Nadia Putri',
            'email' => 'nadia@example.com',
            'phone' => '081200000001',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('user.dashboard'));

        $user = User::where('email', 'nadia@example.com')->firstOrFail();

        Notification::assertSentTo($user, FestifyVerifyEmail::class);
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'nadia@example.com']);

        $this->post(route('password.email'), ['email' => 'nadia@example.com'])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, FestifyResetPassword::class);
    }

    public function test_user_order_locks_and_decrements_concert_and_zone_stock(): void
    {
        $user = User::factory()->create();
        [$concert, $zone] = $this->concertWithZone(stock: 10, zoneStock: 6);

        $this->actingAs($user)
            ->post(route('user.orders.store', $concert), [
                'ticket_zone_id' => $zone->id,
                'ticket_quantity' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'concert_id' => $concert->id,
            'ticket_zone_id' => $zone->id,
            'ticket_quantity' => 2,
            'total_price' => 200000,
        ]);
        $this->assertSame(8, $concert->fresh()->stock);
        $this->assertSame(4, $zone->fresh()->stock);
    }

    public function test_admin_payment_verification_issues_e_ticket(): void
    {
        $admin = Admin::create(['name' => 'Admin Festify', 'username' => 'admin', 'password' => 'password']);
        $user = User::factory()->create();
        [$concert, $zone] = $this->concertWithZone();
        $order = $this->orderFor($user, $concert, $zone);
        $payment = Payment::create(['order_id' => $order->id, 'total_amount' => $order->total_price]);

        $this->actingAs($admin, 'admin')
            ->put(route('admin.payments.update', $payment), ['payment_status' => 'success'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'order_status' => 'paid']);
        $this->assertDatabaseHas('e_tickets', ['order_id' => $order->id, 'ticket_status' => 'belum_ditukar']);
    }

    public function test_loket_exchanges_paid_e_ticket_into_wristband(): void
    {
        $officer = Officer::create(['name' => 'Loket Satu', 'username' => 'loket1', 'password' => 'password', 'role' => 'loket']);
        $user = User::factory()->create();
        [$concert, $zone] = $this->concertWithZone(date: now()->toDateString());
        $order = $this->orderFor($user, $concert, $zone, 'paid');
        Payment::create(['order_id' => $order->id, 'total_amount' => $order->total_price, 'payment_status' => 'success']);
        $ticket = $this->ticketFor($user, $concert, $order);

        $this->actingAs($officer, 'officer')
            ->post(route('loket.scan.submit'), ['ticket_code' => $ticket->ticket_code])
            ->assertOk()
            ->assertSee('Penukaran berhasil');

        $this->assertDatabaseHas('e_tickets', ['id' => $ticket->id, 'ticket_status' => 'sudah_ditukar']);
        $this->assertDatabaseHas('wristbands', ['e_ticket_id' => $ticket->id, 'wristband_status' => 'aktif']);
        $this->assertDatabaseHas('scan_histories', ['scan_type' => 'scan_eticket', 'scan_result' => 'berhasil']);
    }

    public function test_gate_rejects_wristband_for_different_concert_day(): void
    {
        $officer = Officer::create(['name' => 'Gate Satu', 'username' => 'gate1', 'password' => 'password', 'role' => 'gate']);
        $user = User::factory()->create();
        [$concert, $zone] = $this->concertWithZone(date: now()->addDay()->toDateString());
        $order = $this->orderFor($user, $concert, $zone, 'paid');
        $ticket = $this->ticketFor($user, $concert, $order, 'sudah_ditukar');
        $wristband = Wristband::create([
            'e_ticket_id' => $ticket->id,
            'concert_id' => $concert->id,
            'wristband_code' => 'GLG-TEST-001',
            'wristband_qr_code' => 'GLG-TEST-001',
            'activated_at' => now(),
        ]);

        $this->actingAs($officer, 'officer')
            ->post(route('gate.scan.submit'), ['wristband_code' => $wristband->wristband_code])
            ->assertOk()
            ->assertSee('Konser tidak sesuai hari ini');

        $this->assertDatabaseHas('wristbands', ['id' => $wristband->id, 'wristband_status' => 'aktif']);
        $this->assertDatabaseHas('scan_histories', ['scan_type' => 'scan_gelang', 'scan_result' => 'gagal']);
    }

    public function test_admin_can_export_scan_report_as_csv(): void
    {
        $admin = Admin::create(['name' => 'Admin Festify', 'username' => 'admin', 'password' => 'password']);
        $officer = Officer::create(['name' => 'Gate Satu', 'username' => 'gate1', 'password' => 'password', 'role' => 'gate']);
        ScanHistory::create([
            'officer_id' => $officer->id,
            'scan_type' => 'scan_gelang',
            'scan_result' => 'berhasil',
            'message' => 'Akses diterima. Silakan masuk.',
            'scanned_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_filter_scan_report_and_export_excel(): void
    {
        $admin = Admin::create(['name' => 'Admin Festify', 'username' => 'admin', 'password' => 'password']);
        $loket = Officer::create(['name' => 'Loket Satu', 'username' => 'loket1', 'password' => 'password', 'role' => 'loket']);
        $gate = Officer::create(['name' => 'Gate Satu', 'username' => 'gate1', 'password' => 'password', 'role' => 'gate']);
        ScanHistory::create([
            'officer_id' => $loket->id,
            'scan_type' => 'scan_eticket',
            'scan_result' => 'berhasil',
            'message' => 'Penukaran berhasil. Gelang aktif.',
            'scanned_at' => now(),
        ]);
        ScanHistory::create([
            'officer_id' => $gate->id,
            'scan_type' => 'scan_gelang',
            'scan_result' => 'gagal',
            'message' => 'Gelang tidak ditemukan.',
            'scanned_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.reports', ['scan_type' => 'scan_gelang', 'scan_result' => 'gagal']))
            ->assertOk()
            ->assertSee('Gelang tidak ditemukan.')
            ->assertDontSee('Penukaran berhasil. Gelang aktif.');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.export', ['format' => 'xls', 'scan_type' => 'scan_gelang']))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel');
    }

    public function test_midtrans_notification_marks_payment_success_and_issues_e_ticket(): void
    {
        config(['services.midtrans.server_key' => 'SB-Mid-server-test']);
        $user = User::factory()->create();
        [$concert, $zone] = $this->concertWithZone();
        $order = $this->orderFor($user, $concert, $zone);
        Payment::create([
            'order_id' => $order->id,
            'gateway_order_id' => 'MIDTRANS-ORDER-001',
            'payment_method' => 'midtrans',
            'total_amount' => $order->total_price,
        ]);
        $payload = [
            'order_id' => 'MIDTRANS-ORDER-001',
            'status_code' => '200',
            'gross_amount' => number_format($order->total_price, 2, '.', ''),
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-midtrans-001',
            'payment_type' => 'bank_transfer',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'));

        $this->postJson(route('midtrans.notification'), $payload)
            ->assertOk()
            ->assertJsonPath('payment_status', 'success');

        $this->assertDatabaseHas('payments', [
            'gateway_order_id' => 'MIDTRANS-ORDER-001',
            'payment_status' => 'success',
            'midtrans_transaction_id' => 'trx-midtrans-001',
        ]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'order_status' => 'paid']);
        $this->assertDatabaseHas('e_tickets', ['order_id' => $order->id, 'ticket_status' => 'belum_ditukar']);
    }

    public function test_midtrans_notification_endpoint_can_be_checked_in_browser(): void
    {
        $this->get('/midtrans/notification')
            ->assertOk()
            ->assertSee('Midtrans notification endpoint is active');
    }

    public function test_midtrans_sync_without_transaction_redirects_instead_of_404(): void
    {
        $user = User::factory()->create();
        [$concert, $zone] = $this->concertWithZone();
        $order = $this->orderFor($user, $concert, $zone);
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'midtrans',
            'total_amount' => $order->total_price,
        ]);

        $this->actingAs($user)
            ->post(route('user.payments.sync', $order))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    private function concertWithZone(int $stock = 20, int $zoneStock = 12, ?string $date = null): array
    {
        $concert = Concert::create([
            'name' => 'Senja Live Jakarta',
            'artist' => 'Nadin Amizah',
            'description' => 'Konser demo',
            'venue' => 'Istora Senayan',
            'date' => $date ?? now()->toDateString(),
            'time' => '19:30',
            'price' => 100000,
            'stock' => $stock,
            'seat_zone' => 'Festival Tengah',
            'status' => 'aktif',
        ]);

        $zone = TicketZone::create([
            'concert_id' => $concert->id,
            'name' => 'Festival Tengah',
            'price' => 100000,
            'stock' => $zoneStock,
            'color' => '#ea580c',
            'position' => 1,
        ]);

        return [$concert, $zone];
    }

    private function orderFor(User $user, Concert $concert, TicketZone $zone, string $status = 'pending'): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'concert_id' => $concert->id,
            'ticket_zone_id' => $zone->id,
            'order_code' => 'ORD-TEST-'.uniqid(),
            'order_date' => now(),
            'ticket_quantity' => 1,
            'total_price' => $zone->price,
            'order_status' => $status,
        ]);
    }

    private function ticketFor(User $user, Concert $concert, Order $order, string $status = 'belum_ditukar'): ETicket
    {
        return ETicket::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'concert_id' => $concert->id,
            'ticket_code' => 'TIX-TEST-'.uniqid(),
            'ticket_qr_code' => 'TIX-TEST',
            'ticket_status' => $status,
            'issued_at' => now(),
        ]);
    }
}
