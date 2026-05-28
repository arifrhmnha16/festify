<?php

namespace Database\Seeders;

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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Admin::create(['name' => 'Admin Festify', 'username' => 'admin', 'password' => 'password']);

        Officer::insert([
            ['name' => 'Loket Satu', 'username' => 'loket1', 'password' => bcrypt('password'), 'role' => 'loket', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loket Dua', 'username' => 'loket2', 'password' => bcrypt('password'), 'role' => 'loket', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gate Satu', 'username' => 'gate1', 'password' => bcrypt('password'), 'role' => 'gate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gate Dua', 'username' => 'gate2', 'password' => bcrypt('password'), 'role' => 'gate', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $users = collect([
            ['name' => 'Nadia Putri', 'email' => 'nadia@example.com', 'phone' => '081200000001', 'password' => 'password'],
            ['name' => 'Arka Wibowo', 'email' => 'arka@example.com', 'phone' => '081200000002', 'password' => 'password'],
            ['name' => 'Maya Lestari', 'email' => 'maya@example.com', 'phone' => '081200000003', 'password' => 'password'],
            ['name' => 'Rafi Pratama', 'email' => 'rafi@example.com', 'phone' => '081200000004', 'password' => 'password'],
            ['name' => 'Salsa Kirana', 'email' => 'salsa@example.com', 'phone' => '081200000005', 'password' => 'password'],
        ])->map(fn ($data) => User::create($data));

        $concerts = collect([
            ['name' => 'Senja Live Jakarta', 'artist' => 'Nadin Amizah', 'venue' => 'Istora Senayan', 'date' => now()->toDateString(), 'time' => '19:30', 'price' => 450000, 'stock' => 120, 'seat_zone' => 'Festival A'],
            ['name' => 'Monokrom Night', 'artist' => 'Tulus', 'venue' => 'ICE BSD', 'date' => now()->addDays(7)->toDateString(), 'time' => '20:00', 'price' => 650000, 'stock' => 180, 'seat_zone' => 'Tribune'],
            ['name' => 'Kota & Nada', 'artist' => 'Hindia', 'venue' => 'Tennis Indoor Senayan', 'date' => now()->addDays(14)->toDateString(), 'time' => '19:00', 'price' => 375000, 'stock' => 90, 'seat_zone' => 'Festival'],
            ['name' => 'Orbit Pop Fest', 'artist' => 'Juicy Luicy', 'venue' => 'JIExpo Kemayoran', 'date' => now()->addDays(21)->toDateString(), 'time' => '18:30', 'price' => 300000, 'stock' => 240, 'seat_zone' => 'Festival'],
            ['name' => 'Ruang Rindu', 'artist' => 'Letto', 'venue' => 'The Kasablanka Hall', 'date' => now()->addDays(28)->toDateString(), 'time' => '20:00', 'price' => 275000, 'stock' => 160, 'seat_zone' => 'Regular'],
            ['name' => 'After Hours Session', 'artist' => 'Reality Club', 'venue' => 'M Bloc Space', 'date' => now()->addDays(35)->toDateString(), 'time' => '19:30', 'price' => 225000, 'stock' => 80, 'seat_zone' => 'Standing'],
        ])->map(fn ($data) => Concert::create($data + [
            'admin_id' => $admin->id,
            'description' => 'Nikmati pengalaman konser dengan tiket digital, QR Code, dan validasi cepat di venue.',
            'status' => 'aktif',
        ]));

        foreach ($concerts as $concert) {
            $concert->ticketZones()->createMany([
                ['name' => 'Festival Depan', 'price' => (int) round($concert->price * 1.25), 'stock' => max(1, (int) floor($concert->stock * 0.3)), 'color' => '#dc2626', 'position' => 1],
                ['name' => 'Festival Tengah', 'price' => $concert->price, 'stock' => max(1, (int) floor($concert->stock * 0.45)), 'color' => '#ea580c', 'position' => 2],
                ['name' => 'Tribune Belakang', 'price' => (int) round($concert->price * 0.8), 'stock' => max(1, (int) ceil($concert->stock * 0.25)), 'color' => '#ca8a04', 'position' => 3],
            ]);
        }

        foreach ($users->take(3) as $index => $user) {
            $concert = $concerts[$index];
            $zone = $concert->ticketZones()->orderBy('position')->skip($index % 3)->first();
            $order = Order::create([
                'user_id' => $user->id,
                'concert_id' => $concert->id,
                'ticket_zone_id' => $zone->id,
                'order_code' => 'ORD-SEED-'.($index + 1),
                'order_date' => now()->subDays($index),
                'ticket_quantity' => 1,
                'total_price' => $zone->price,
                'order_status' => 'paid',
            ]);
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'transfer_manual',
                'total_amount' => $order->total_price,
                'payment_status' => 'success',
                'payment_date' => now()->subDays($index),
            ]);
            $ticketCode = 'TIX-SEED-'.Str::upper(Str::random(6));
            $ticket = ETicket::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'concert_id' => $concert->id,
                'ticket_code' => $ticketCode,
                'ticket_qr_code' => $ticketCode,
                'ticket_status' => $index === 0 ? 'belum_ditukar' : 'sudah_ditukar',
                'issued_at' => now()->subDays($index),
                'exchanged_at' => $index === 0 ? null : now()->subHours(2),
            ]);

            if ($index > 0) {
                $wristbandCode = 'GLG-SEED-'.Str::upper(Str::random(6));
                Wristband::create([
                    'e_ticket_id' => $ticket->id,
                    'concert_id' => $concert->id,
                    'wristband_code' => $wristbandCode,
                    'wristband_qr_code' => $wristbandCode,
                    'wristband_status' => $index === 2 ? 'sudah_masuk' : 'aktif',
                    'activated_at' => now()->subHours(2),
                    'entered_at' => $index === 2 ? now()->subHour() : null,
                ]);
            }
        }
    }
}
