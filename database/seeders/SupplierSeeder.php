<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'SUP001', 'nama' => 'PT Sumber Rezeki', 'telepon' => '021-5550001', 'alamat' => 'Jl. Mawar No. 10, Jakarta'],
            ['kode' => 'SUP002', 'nama' => 'CV Berkah Jaya', 'telepon' => '022-5550002', 'alamat' => 'Jl. Melati No. 22, Bandung'],
            ['kode' => 'SUP003', 'nama' => 'UD Makmur Sentosa', 'telepon' => '031-5550003', 'alamat' => 'Jl. Anggrek No. 7, Surabaya'],
        ];

        foreach ($data as $row) {
            Supplier::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}
