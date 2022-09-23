<?php

namespace App\Controllers;

use App\Models\DesaModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\MahasiswaModel;
use App\Models\Sekolah;

class WilayahController extends BaseController
{
	function valid()
	{
		$mahasiswa = new MahasiswaModel();
		$mahasiswa1 = new MahasiswaModel();

		if ($mahasiswa->where('email', $this->request->getVar('email'))->first()) {
			if ($mahasiswa1->where(['email' => $this->request->getVar('email'), 'nim' => $this->request->getVar('nim')])->first()) {
				$response['validate'] = '';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			} else if ($mahasiswa->where('email', $this->request->getVar('email'))->first()) {
				$response['validate'] = 'email tidak boleh sama';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			} else {
				$response['validate'] = '';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			}
		} else if ($mahasiswa->where('NISN', $this->request->getVar('nisn'))->first()) {
			if ($mahasiswa1->where(['NISN' => $this->request->getVar('nisn'), 'nim' => $this->request->getVar('nim')])->first()) {
				$response['validate'] = '';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			} else if ($mahasiswa->where('NISN', $this->request->getVar('nisn'))->first()) {
				$response['validate'] = 'nisn tidak boleh sama';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			} else {
				$response['validate'] = '';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			}
		} else if ($mahasiswa->where('no_telepon', $this->request->getPost('no_telepon'))->first()) {
			if ($mahasiswa1->where(['no_telepon' => $this->request->getVar('no_telepon'), 'nim' => $this->request->getVar('nim')])->first()) {
				$response['validate'] = '';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			} else if ($mahasiswa->where('no_telepon', $this->request->getPost('no_telepon'))->first()) {
				$response['validate'] = 'no telepon tidak boleh sama';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			} else {
				$response['validate'] = '';
				$response['token'] = csrf_hash();
				echo json_encode($response);
			}
		} else {
			$response['validate'] = '';
			$response['token'] = csrf_hash();
			echo json_encode($response);
		}
	}

	function action()
	{
		if ($this->request->getVar('action')) {
			$action = $this->request->getVar('action');

			if ($action == 'get_kabupaten') {
				$kabupaten = new KabupatenModel();
				$kabupatenData['kabupaten'] = $kabupaten->where('provinsi_id', $this->request->getVar('provinsi_id'))->findAll();
				$kabupatenData['token'] = csrf_hash();
				return $this->response->setJSON($kabupatenData);
			}

			if ($action == 'get_kecamatan') {
				$kecamatan = new KecamatanModel();

				$kecamatanData['kecamatan'] = $kecamatan->where('kabupaten_id', $this->request->getVar('kabupaten_id'))->findAll();
				$kecamatanData['token'] = csrf_hash();
				echo json_encode($kecamatanData);
			}

			if ($action == 'get_desa') {
				$desa = new DesaModel();
				$desaData['desa'] = $desa->where('kecamatan_id', $this->request->getVar('kecamatan_id'))->findAll();
				$desaData['token'] = csrf_hash();
				echo json_encode($desaData);
			}
		}
	}

	function sekolah()
	{
		$sklh = new Sekolah();

		if ($sklh->where('npsn', $this->request->getVar('npsn'))->first()) {
			$response['validate'] = '';
			$response['token'] = csrf_hash();
			$data = $sklh->where('npsn', $this->request->getVar('npsn'))->first();
			$response['npsn'] = $data['npsn'];
			echo json_encode($response);
		} else {
			$response['validate'] = 'npsn tidak ditemukan';
			$response['token'] = csrf_hash();
			echo json_encode($response);
		}
	}
}
