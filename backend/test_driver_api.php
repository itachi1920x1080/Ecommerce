<?php
// 1. Create a fake order
 = App\Models\User::where('role', 'user')->first();
 = App\Models\User::where('role', 'driver')->first();

 = App\Models\Order::create([
    'user_id' => ->id,
    'total_price' => 15.50,
    'status' => 'finding_driver',
    'payment_method' => 'cod'
]);

echo 'Created Order #' . ->id . PHP_EOL;

// 2. Generate a token for driver
 = ->createToken('test')->plainTextToken;
echo 'Driver Token: ' .  . PHP_EOL;

