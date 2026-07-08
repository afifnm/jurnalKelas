<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruJadwalNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_dan_jadwal_guru_hanya_menggunakan_guru_yang_login(): void
    {
        Role::create(['name' => 'guru', 'guard_name' => 'web']);

        $guruLogin = $this->buatGuru('guru-login', 'login@example.test');
        $guruLain = $this->buatGuru('guru-lain', 'lain@example.test');
        $guruLogin->assignRole('guru');
        $guruLain->assignRole('guru');

        $response = $this->actingAs($guruLogin)->get(route('guru.jadwal.index', [
            'guru_id' => $guruLain->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('guruId', $guruLogin->id);
        $response->assertViewHas('guruList', function ($guruList) use ($guruLogin, $guruLain) {
            return $guruList->pluck('id')->all() === [$guruLogin->id]
                && ! $guruList->pluck('id')->contains($guruLain->id);
        });
        $response->assertSeeInOrder([
            'Dashboard',
            'Jadwal Mengajar',
            'Isi Jurnal',
            'Jurnal',
            'Logout',
        ]);
    }

    private function buatGuru(string $username, string $email): User
    {
        return User::create([
            'nama' => $username,
            'username' => $username,
            'email' => $email,
            'password' => 'password',
            'is_active' => true,
        ]);
    }
}
