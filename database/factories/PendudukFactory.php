<?php
namespace Database\Factories;

use App\Models\Keluarga;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendudukFactory extends Factory
{
    public function definition(): array
    {
        return [
            'keluarga_id' => Keluarga::factory(),
            'nik' => $this->faker->unique()->numerify('3217##########'),
            'nama_lengkap' => $this->faker->name(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date(),
            'agama' => 'Islam',
            'pekerjaan' => $this->faker->jobTitle(),
            'status_hubungan_keluarga' => $this->faker->randomElement(['Kepala Keluarga', 'Istri', 'Anak']),
            'status_perkawinan' => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai']),
        ];
    }
}