<?php
// Create a database seeder (database/seeders/AdminSeeder.php)
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone' => '12345678',
        ]);

        // Assign admin role to user
        $admin->assignRole($adminRole);

        for ($i = 0; $i < 5; $i++) {
            // Create customer user
            $customer = User::create([
                'name' => "Customer User $i",
                'email' => "customer$i@example.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '12345678',
            ]);
            $customer->assignRole($customerRole);
        }

    }
}
