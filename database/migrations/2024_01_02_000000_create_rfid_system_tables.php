<?php
// database/migrations/2024_01_02_000000_create_rfid_system_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update tabel users (tambah kolom RFID)
        Schema::table('users', function (Blueprint $table) {
            $table->string('rfid_card')->unique()->after('id');
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            $table->string('tempat_lahir')->nullable()->after('role');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->text('alamat')->nullable()->after('jenis_kelamin');
            $table->decimal('saldo', 15, 2)->default(0)->after('alamat');
            $table->boolean('limit_saldo_aktif')->default(true)->after('saldo');
            $table->decimal('limit_harian', 15, 2)->default(10000)->after('limit_saldo_aktif');
        });

        // Tabel Presensi Sekolah
        Schema::create('presensi_sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->boolean('terlambat_masuk')->default(false);
            $table->integer('menit_terlambat_masuk')->default(0);
            $table->time('jam_keluar')->nullable();
            $table->boolean('terlambat_keluar')->default(false);
            $table->integer('menit_terlambat_keluar')->default(0);
            $table->enum('keterangan', ['hadir', 'izin', 'sakit', 'tanpa_keterangan'])->default('hadir');
            $table->timestamps();
        });

        // Tabel Presensi Sholat
        Schema::create('presensi_sholat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('waktu_sholat', ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya']);
            $table->time('jam_presensi')->nullable();
            $table->boolean('terlambat')->default(false);
            $table->integer('menit_terlambat')->default(0);
            $table->enum('keterangan', ['hadir', 'izin', 'sakit', 'tanpa_keterangan'])->default('hadir');
            $table->timestamps();
        });

        // Tabel Presensi Kustom
        Schema::create('presensi_kustom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->time('jam_scan')->nullable();
            $table->boolean('terlambat')->default(false);
            $table->integer('menit_terlambat')->default(0);
            $table->enum('status', ['hadir', 'terlambat', 'tanpa_keterangan'])->default('tanpa_keterangan');
            $table->string('kepentingan');
            $table->enum('keterangan', ['hadir', 'izin', 'sakit', 'tanpa_keterangan'])->default('tanpa_keterangan');
            $table->timestamps();
        });

        // Tabel Transaksi Kantin
        Schema::create('transaksi_kantin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis', ['topup', 'pembayaran']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Jadwal Sholat
        Schema::create('jadwal_sholat', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('subuh');
            $table->time('dzuhur');
            $table->time('ashar');
            $table->time('maghrib');
            $table->time('isya');
            $table->timestamps();
            $table->unique('tanggal');
        });

        // Tabel Penggunaan Harian
        Schema::create('penggunaan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'tanggal']);
        });

        // Tabel Pengaturan Waktu
        Schema::create('pengaturan_waktu', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('nama')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('toleransi_keterlambatan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Insert default settings untuk pengaturan waktu
        DB::table('pengaturan_waktu')->insert([
            [
                'jenis' => 'sekolah',
                'nama' => 'Jam Masuk',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '07:15:00',
                'toleransi_keterlambatan' => 15,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'jenis' => 'sekolah',
                'nama' => 'Jam Pulang',
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '14:30:00',
                'toleransi_keterlambatan' => 30,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'jenis' => 'sholat',
                'nama' => 'Toleransi Setelah Adzan',
                'jam_mulai' => '00:00:00',
                'jam_selesai' => '00:20:00',
                'toleransi_keterlambatan' => 20,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pengaturan_waktu');
        Schema::dropIfExists('penggunaan_harian');
        Schema::dropIfExists('jadwal_sholat');
        Schema::dropIfExists('transaksi_kantin');
        Schema::dropIfExists('presensi_kustom');
        Schema::dropIfExists('presensi_sholat');
        Schema::dropIfExists('presensi_sekolah');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'rfid_card', 'role', 'tempat_lahir', 'tanggal_lahir',
                'jenis_kelamin', 'alamat', 'saldo', 'limit_saldo_aktif', 'limit_harian'
            ]);
        });
    }
};