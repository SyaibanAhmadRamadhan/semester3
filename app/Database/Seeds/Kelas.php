<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Kelas extends Seeder
{
    public function run()
    {
        $admin = [
            [
                'name_kelas' => '19.2B.01',
                'semester' => 'semester1'
            ],
            [
                'name_kelas' => '19.3B.12',
                'semester' => 'semester3'
            ]
        ];

        foreach ($admin as $x) {
            $this->db->table('daftar_kelas')->insert($x);
        }
    }
}
