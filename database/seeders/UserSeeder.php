<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Setup 3 demo users dengan role berbeda

        // User 1: Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password123'),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
        echo "✅ User: {$superAdmin->name} → Role: super_admin\n";

        // User 2: Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
            ]
        );
        $admin->syncRoles(['admin']);
        echo "✅ User: {$admin->name} → Role: admin\n";

        // User 3: Kitchen (Dapur)
        $kitchen = User::firstOrCreate(
            ['email' => 'kitchen@gmail.com'],
            [
                'name' => 'Kitchen Staff',
                'password' => bcrypt('password123'),
            ]
        );
        $kitchen->syncRoles(['kitchen']);
        echo "✅ User: {$kitchen->name} → Role: kitchen\n";

        echo "\n✅ Setup Complete! 3 users dengan role sudah siap untuk testing.\n";
        echo "═══════════════════════════════════════════════════════════\n";
        echo "Email Login untuk Testing:\n";
        echo "  1️⃣  superadmin@gmail.com (password: password123)\n";
        echo "  2️⃣  admin@gmail.com      (password: password123)\n";
        echo "  3️⃣  kitchen@gmail.com    (password: password123)\n";
        echo "═══════════════════════════════════════════════════════════\n";
    }
}
