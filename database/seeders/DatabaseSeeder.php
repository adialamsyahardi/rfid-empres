<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin User
        User::create([
            'rfid_card' => 'ADMIN001',
            'name' => 'Administrator',
            'email' => 'admin@rfid.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'tempat_lahir' => 'Mojokerto',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Pacet, Mojokerto',
            'saldo' => 0,
            'limit_saldo_aktif' => false,
            'limit_harian' => 10000
        ]);

        // Sample Users
        User::create([
            'rfid_card' => '0123456789',
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@rfid.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'tempat_lahir' => 'Mojokerto',
            'tanggal_lahir' => '2005-03-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Raya Pacet No. 123, Mojokerto',
            'saldo' => 50000,
            'limit_saldo_aktif' => true,
            'limit_harian' => 10000
        ]);

        User::create([
            'rfid_card' => '9876543210',
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@rfid.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'tempat_lahir' => 'Mojokerto',
            'tanggal_lahir' => '2006-07-20',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Pahlawan No. 45, Pacet, Mojokerto',
            'saldo' => 75000,
            'limit_saldo_aktif' => true,
            'limit_harian' => 15000
        ]);

        User::create([
            'rfid_card' => '1122334455',
            'name' => 'Budi Santoso',
            'email' => 'budi@rfid.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'tempat_lahir' => 'Mojokerto',
            'tanggal_lahir' => '2005-11-10',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka No. 88, Pacet, Mojokerto',
            'saldo' => 30000,
            'limit_saldo_aktif' => true,
            'limit_harian' => 10000
        ]);

        $this->command->info('✓ Seeder completed successfully!');
        $this->command->info('Admin credentials: admin@rfid.com / admin123');
        $this->command->info('User credentials: ahmad@rfid.com / password');
    }
}