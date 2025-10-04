<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BahanBakuCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsGudang(): User
    {
        $user = User::factory()->create(['role' => 'gudang']);
        $this->actingAs($user);

        return $user;
    }

    public function test_gudang_user_can_create_bahan_baku(): void
    {
        $this->actingAsGudang();

        $payload = [
            'nama' => 'Tepung Terigu',
            'kategori' => 'Karbohidrat',
            'jumlah' => 10,
            'satuan' => 'kg',
            'tanggal_masuk' => Carbon::now()->format('Y-m-d'),
            'tanggal_kadaluarsa' => Carbon::now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->postJson(route('admin.bahan_baku.store'), $payload);

        $response->assertOk();
        $this->assertDatabaseHas('bahan_baku', [
            'nama' => 'Tepung Terigu',
            'kategori' => 'Karbohidrat',
            'jumlah' => 10,
            'satuan' => 'kg',
        ]);
    }

    public function test_gudang_user_can_update_jumlah(): void
    {
        $this->actingAsGudang();

        $bahanBaku = BahanBaku::factory()->create([
            'jumlah' => 5,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(10),
        ]);

        $response = $this->putJson(route('admin.bahan_baku.update', $bahanBaku), [
            'jumlah' => 15,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahanBaku->id,
            'jumlah' => 15,
        ]);
    }

    public function test_gudang_user_can_delete_kadaluarsa_item(): void
    {
        $this->actingAsGudang();

        $bahanBaku = BahanBaku::factory()->create([
            'jumlah' => 0,
            'tanggal_kadaluarsa' => Carbon::now()->subDay(),
            'status' => 'kadaluarsa',
        ]);

        $this->assertSame('kadaluarsa', $bahanBaku->fresh()->status);

        $response = $this->deleteJson(route('admin.bahan_baku.destroy', $bahanBaku));

        $response->assertOk();
        $this->assertDatabaseMissing('bahan_baku', [
            'id' => $bahanBaku->id,
        ]);
    }

    public function test_gudang_user_cannot_update_kadaluarsa_item(): void
    {
        $this->actingAsGudang();

        $bahanBaku = BahanBaku::factory()->create([
            'jumlah' => 10,
            'tanggal_kadaluarsa' => Carbon::now()->subDays(2),
            'status' => 'kadaluarsa',
        ]);

        $response = $this->putJson(route('admin.bahan_baku.update', $bahanBaku), [
            'jumlah' => 5,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
        ]);

        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahanBaku->id,
            'jumlah' => 10,
            'status' => 'kadaluarsa',
        ]);
    }
}
