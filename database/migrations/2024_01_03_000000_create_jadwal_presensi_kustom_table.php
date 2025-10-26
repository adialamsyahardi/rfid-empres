<?php
// database/migrations/2024_01_03_000000_create_jadwal_presensi_kustom_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal_presensi_kustom', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Update table presensi_kustom - tambah foreign key ke jadwal
        Schema::table('presensi_kustom', function (Blueprint $table) {
            $table->foreignId('jadwal_id')->nullable()->after('user_id')->constrained('jadwal_presensi_kustom')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('presensi_kustom', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            $table->dropColumn('jadwal_id');
        });
        
        Schema::dropIfExists('jadwal_presensi_kustom');
    }
};