<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\RestockRequest;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $owner = User::factory()->create([
            'name' => 'Pemilik Toko',
            'email' => 'pemilik@dyacom.test',
            'role' => 'owner',
        ]);

        $employee = User::factory()->create([
            'name' => 'Karyawan',
            'email' => 'karyawan@dyacom.test',
            'role' => 'employee',
        ]);

        $extraOwner = User::factory()->create(['role' => 'owner']);
        $extraEmployees = User::factory()->count(4)->create(['role' => 'employee']);

        $users = collect([$owner, $employee, $extraOwner])->merge($extraEmployees);

        $suppliers = Supplier::factory()->count(5)->create();

        $products = Product::factory()
            ->count(5)
            ->create([
                'supplier_id' => fn () => $suppliers->random()->id,
            ]);

        DB::transaction(function () use ($suppliers, $products, $users, $owner) {
            foreach (range(1, 5) as $i) {
                $product = $products->random();
                $type = fake()->randomElement(['in', 'out']);
                $qty = fake()->numberBetween(1, 25);

                $product->refresh();
                if ($type === 'out' && $qty > $product->stock) {
                    $type = 'in';
                }

                $newStock = $type === 'in' ? $product->stock + $qty : $product->stock - $qty;
                $product->update(['stock' => $newStock]);

                StockMovement::factory()->create([
                    'product_id' => $product->id,
                    'supplier_id' => $type === 'in' ? ($product->supplier_id ?? $suppliers->random()->id) : null,
                    'user_id' => $users->random()->id,
                    'type' => $type,
                    'quantity' => $qty,
                    'created_at' => now()->subDays(fake()->numberBetween(0, 6))->setTime(fake()->numberBetween(8, 17), fake()->randomElement([0, 10, 20, 30, 40, 50])),
                    'updated_at' => now(),
                ]);
            }

            foreach (range(1, 5) as $i) {
                $product = $products->random();
                $qty = fake()->numberBetween(5, 60);
                $status = fake()->randomElement(['pending', 'approved', 'rejected']);
                $requester = $users->where('role', 'employee')->random();

                $payload = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'note' => fake()->optional()->sentence(),
                    'status' => $status,
                    'requested_by' => $requester->id,
                    'created_at' => now()->subDays(fake()->numberBetween(0, 10))->setTime(fake()->numberBetween(8, 17), fake()->randomElement([0, 15, 30, 45])),
                    'updated_at' => now(),
                ];

                if ($status !== 'pending') {
                    $payload['decided_by'] = $owner->id;
                    $payload['decided_at'] = now()->subDays(fake()->numberBetween(0, 6));
                    $payload['decision_note'] = fake()->optional()->sentence();
                }

                $restock = RestockRequest::create($payload);

                if ($status === 'approved') {
                    $product = Product::query()->lockForUpdate()->findOrFail($restock->product_id);
                    $product->update(['stock' => $product->stock + $restock->quantity]);

                    StockMovement::factory()->create([
                        'product_id' => $product->id,
                        'supplier_id' => $product->supplier_id,
                        'user_id' => $owner->id,
                        'restock_request_id' => $restock->id,
                        'type' => 'in',
                        'quantity' => $restock->quantity,
                        'note' => 'Restok disetujui (Seeder)',
                        'created_at' => $restock->decided_at ?? now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        AppNotification::factory()
            ->count(5)
            ->create([
                'user_id' => fn () => $users->random()->id,
                'link' => fn () => fake()->optional()->randomElement([
                    route('dashboard'),
                    route('products.index'),
                    route('restock.index'),
                    route('notifications.index'),
                ]),
            ]);
    }
}
