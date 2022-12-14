<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AbsensiDosenModel;
use App\Models\AbsensiMahasiswaModel;
use App\Models\DaftarKelasModel;
use App\Models\JurusanModel;
use App\Models\MatakuliahMahasiswaModel;
use App\Models\MatakuliahModel;
use App\Models\RuangModel;

class DosenDashboardController extends BaseController
{
    public function index()
    {
        return view('dosen/index_dosen', [
            "title" => "dosen"
        ]);
    }
    public function jadwalDosen()
    {
        $matkul = new MatakuliahModel();
        $jurusan = new JurusanModel();
        $ruangan = new RuangModel();
        $daftarKelas = new DaftarKelasModel();

        $dataMatkul = $matkul->where('nip_dosen', session()->get('nip'))->findAll();

        if ($dataMatkul) {
            foreach ($dataMatkul as $x) {
                $dataJurusan[] = $jurusan->where('kode', $x['kode_jurusan'])->findAll();
                $dataRuangan[] = $ruangan->where('id', $x['no_ruang'])->findAll();
                $dataDaftarKelas[] = $daftarKelas->where('id', $x['id_daftar_kelas'])->findAll();
            }
        } else {
            $dataJurusan[] = '';
            $dataRuangan[] = '';
            $dataDaftarKelas[] = '';
        }

        return view('dosen/jadwal_kuliah_dosen', [
            "title" => "jadwalDosen",
            "matkul" => $dataMatkul,
            "jurusan" => $dataJurusan,
            "ruangan" => $dataRuangan,
            "daftarKelas" => $dataDaftarKelas,
        ]);
    }

    public function ruangKelas($id, $kelas)
    {
        function hari_ini1()
        {
            $hari = date("D");

            switch ($hari) {
                case 'Sun':
                    $hari_ini = "minggu";
                    break;

                case 'Mon':
                    $hari_ini = "senin";
                    break;

                case 'Tue':
                    $hari_ini = "selasa";
                    break;

                case 'Wed':
                    $hari_ini = "rabu";
                    break;

                case 'Thu':
                    $hari_ini = "kamis";
                    break;

                case 'Fri':
                    $hari_ini = "jumat";
                    break;

                case 'Sat':
                    $hari_ini = "sabtu";
                    break;

                default:
                    $hari_ini = "Tidak di ketahui";
                    break;
            }
            return $hari_ini;
        }

        $absen = new AbsensiDosenModel();
        $matkul = new MatakuliahModel();

        $dataMatkul = $matkul->where(['kode_matkul' => $id, 'id_daftar_kelas' => $kelas])->first();
        $dataAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul']])->findAll();

        foreach ($dataAbsen as $x) {
            date_default_timezone_set('Asia/Jakarta');
            if ($x['tanggal_masuk'] . ' ' . $dataMatkul['selesai'] < date('Y-m-d G:i')) {
                $absen->set([
                    'keterangan' => 'selesai'
                ]);
                $absen->where('keterangan', 'berlangsung')->first();
                $absen->update();
            }
        }
        date_default_timezone_set('Asia/Jakarta');
        if (!$dataAbsen) {
            $validateAbsen1 = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'selesai'])->first();
            $validateAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'berlangsung'])->first();
            if (!$validateAbsen && !$validateAbsen1) {
                if ($dataMatkul['hari'] == hari_ini1()) {
                    if (date('H:i') > $dataMatkul['masuk'] && date('H:i') < $dataMatkul['selesai']) {
                        $absen->insert([
                            'nip_dosen' => $dataMatkul['nip_dosen'],
                            'kode_matkul' => $dataMatkul['kode_matkul'],
                            'rangkuman' => '',
                            'tanggal_masuk' => date('Y-m-d'),
                            'status' => 'tidak hadir',
                            'pertemuan' => 'pertemuan ke - ' . count($dataAbsen) + 1,
                        ]);
                    } elseif (date('H:i') > $dataMatkul['selesai']) {
                        $absen->set([
                            'keterangan' => 'selesai'
                        ]);
                        $absen->where('keterangan', 'berlangsung')->first();
                        $absen->update();
                    }
                }
            }
        } else {
            $validateAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'berlangsung', 'tanggal_masuk' => date('Y-m-d')])->first();
            $validateAbsen1 = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'selesai', 'tanggal_masuk' => date('Y-m-d')])->first();
            if (!$validateAbsen && !$validateAbsen1) {
                if ($dataMatkul['hari'] == hari_ini1()) {
                    if (date('H:i') > $dataMatkul['masuk'] && date('H:i') < $dataMatkul['selesai']) {
                        $absen->insert([
                            'nip_dosen' => $dataMatkul['nip_dosen'],
                            'kode_matkul' => $dataMatkul['kode_matkul'],
                            'rangkuman' => '',
                            'tanggal_masuk' => date('Y-m-d'),
                            'status' => 'tidak hadir',
                            'pertemuan' => 'pertemuan ke - ' . count($dataAbsen) + 1,
                        ]);
                    } elseif (date('H:i') > $dataMatkul['selesai']) {
                        $absen->set([
                            'keterangan' => 'selesai'
                        ]);
                        $absen->where('keterangan', 'berlangsung')->first();
                        $absen->update();
                    }
                }
            }
        }


        return redirect()->to('/dosen/jadwalDosen/' . $id . '/kelas');
    }

    public function ruangKelasView($id)
    {
        function hari_ini()
        {
            $hari = date("D");

            switch ($hari) {
                case 'Sun':
                    $hari_ini = "minggu";
                    break;

                case 'Mon':
                    $hari_ini = "senin";
                    break;

                case 'Tue':
                    $hari_ini = "selasa";
                    break;

                case 'Wed':
                    $hari_ini = "rabu";
                    break;

                case 'Thu':
                    $hari_ini = "kamis";
                    break;

                case 'Fri':
                    $hari_ini = "jumat";
                    break;

                case 'Sat':
                    $hari_ini = "sabtu";
                    break;

                default:
                    $hari_ini = "Tidak di ketahui";
                    break;
            }
            return $hari_ini;
        }
        $absen = new AbsensiDosenModel();
        $matkul = new MatakuliahModel();
        $dataMatkul = $matkul->where('kode_matkul', $id)->first();
        $dataAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul']])->findAll();

        foreach ($dataAbsen as $x) {
            date_default_timezone_set('Asia/Jakarta');
            if ($x['tanggal_masuk'] . ' ' . $dataMatkul['selesai'] < date('Y-m-d G:i')) {
                $absen->set([
                    'keterangan' => 'selesai'
                ]);
                $absen->where('keterangan', 'berlangsung')->first();
                $absen->update();
            }
        }
        date_default_timezone_set('Asia/Jakarta');
        if (!$dataAbsen) {
            $validateAbsen1 = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'selesai'])->first();
            $validateAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'berlangsung'])->first();
            if (!$validateAbsen && !$validateAbsen1) {
                if ($dataMatkul['hari'] == hari_ini()) {
                    if (date('H:i') > $dataMatkul['masuk'] && date('H:i') < $dataMatkul['selesai']) {
                        $absen->insert([
                            'nip_dosen' => $dataMatkul['nip_dosen'],
                            'kode_matkul' => $dataMatkul['kode_matkul'],
                            'rangkuman' => '',
                            'tanggal_masuk' => date('Y-m-d'),
                            'status' => 'tidak hadir',
                            'pertemuan' => 'pertemuan ke - ' . count($dataAbsen) + 1,
                        ]);
                    } elseif (date('H:i') > $dataMatkul['selesai']) {
                        $absen->set([
                            'keterangan' => 'selesai'
                        ]);
                        $absen->where('keterangan', 'berlangsung')->first();
                        $absen->update();
                    }
                }
            }
        } else {
            $validateAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'berlangsung', 'tanggal_masuk' => date('Y-m-d')])->first();
            $validateAbsen1 = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul'], 'keterangan' => 'selesai', 'tanggal_masuk' => date('Y-m-d')])->first();
            if (!$validateAbsen && !$validateAbsen1) {
                if ($dataMatkul['hari'] == hari_ini()) {
                    if (date('H:i') > $dataMatkul['masuk'] && date('H:i') < $dataMatkul['selesai']) {
                        $absen->insert([
                            'nip_dosen' => $dataMatkul['nip_dosen'],
                            'kode_matkul' => $dataMatkul['kode_matkul'],
                            'rangkuman' => '',
                            'tanggal_masuk' => date('Y-m-d'),
                            'status' => 'tidak hadir',
                            'pertemuan' => 'pertemuan ke - ' . count($dataAbsen) + 1,
                        ]);
                    } elseif (date('H:i') > $dataMatkul['selesai']) {
                        $absen->set([
                            'keterangan' => 'selesai'
                        ]);
                        $absen->where('keterangan', 'berlangsung')->first();
                        $absen->update();
                    }
                }
            }
        }

        $absen = new AbsensiDosenModel();
        $matkul = new MatakuliahModel();
        $dataMatkul = $matkul->where('kode_matkul', $id)->first();
        if ($absen->where(['kode_matkul' => $id, 'keterangan' => 'berlangsung'])->first()) {
            $dataMatkulHadir = $absen->where(['kode_matkul' => $id, 'keterangan' => 'berlangsung'])->first();
        } else {
            $dataMatkulHadir = ['status' => 'tidak hadir'];
        }
        $dataAbsen = $absen->where(['kode_matkul' => $dataMatkul['kode_matkul']])->findAll();
        date_default_timezone_set('Asia/Jakarta');
        return view('dosen/kelas_dosen', [
            "title" => "kelasDosen",
            'absen' => $dataAbsen,
            'matkul' => $dataMatkul,
            'matkulHadir' => $dataMatkulHadir
        ]);
    }

    public function absensiDosen($id)
    {
        $matkulMhs = new MatakuliahMahasiswaModel();
        $absenMhs = new AbsensiMahasiswaModel();
        $absen = new AbsensiDosenModel();

        $absen = new AbsensiDosenModel();
        date_default_timezone_set('Asia/Jakarta');
        $absen->set([
            'status' => 'hadir',
            'waktu_masuk' => date('G:i')
        ]);
        $absen->where(['keterangan' => 'berlangsung', 'nip_dosen' => session()->get('nip')]);
        $absen->update();

        $dataDosen = $absen->where(['kode_matkul' => $id, 'keterangan' => 'berlangsung'])->first();
        $dataMhsMatkul = $matkulMhs->where(['kode_matkul' => $id])->findAll();
        if ($dataDosen) {
            if ($dataDosen['status'] != 'tidak hadir') {
                foreach ($dataMhsMatkul as $x) {
                    $dataAbsenMhs = $absenMhs->where(['nim_mahasiswa' => $x['nim_mahasiswa'], 'kode_matkul' => $id])->findAll();
                    $absenMhs->insert([
                        'nip_dosen' => $dataDosen['nip_dosen'],
                        'kode_matkul' => $dataDosen['kode_matkul'],
                        'absen_dosen_id' => $dataDosen['id'],
                        'status' => 'tidak hadir',
                        'keterangan' => 'berlangsung',
                        'tanggal_masuk' => date('Y-m-d'),
                        'pertemuan' => 'pertemuan ke - ' . count($dataAbsenMhs) + 1,
                        'nim_mahasiswa' => $x['nim_mahasiswa'],
                    ]);
                }
            } else {
            }
        }

        return redirect()->back();
    }

    public function rangkumanDosen()
    {
        $absen = new AbsensiDosenModel();
        date_default_timezone_set('Asia/Jakarta');
        $absen->set([
            'rangkuman' => $this->request->getPost('rangkuman'),
        ]);
        $absen->where(['keterangan' => 'berlangsung', 'kode_matkul' => $this->request->getPost('kode_matkul'), 'tanggal_masuk' => $this->request->getPost('tanggal_masuk'), 'waktu_masuk' => $this->request->getPost('waktu_masuk'), 'id' => $this->request->getPost('id_absen')]);
        $absen->update();
        return redirect()->back();
    }
}
