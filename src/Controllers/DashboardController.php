<?php

namespace App\Controllers;

use App\Models\Kamar;
use App\Models\Kontrak;
use App\Models\Pembayaran;
use App\Models\Pengaduan;
use App\Models\User;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;

class DashboardController
{
    public function __construct()
    {
        Auth::check();
    }

    public function index(): void
    {
        $role = Auth::getUserRole();
        $data = [];

        if ($role === 'admin' || $role === 'pemilik') {
            $kamarModel = new Kamar();
            $kontrakModel = new Kontrak();
            $pembayaranModel = new Pembayaran();
            $pengaduanModel = new Pengaduan();

            $data['total_kamar'] = $kamarModel->totalKamar();
            $data['kamar_terisi'] = $kamarModel->countByStatus('terisi');
            $data['kamar_kosong'] = $kamarModel->countByStatus('tersedia');
            $data['penyewa_aktif'] = $kontrakModel->countActive();
            $data['pendapatan_bulan_ini'] = $pembayaranModel->getMonthlyRevenue((int)date('n'), (int)date('Y'));
            $data['pembayaran_menunggu'] = $pembayaranModel->getUnconfirmed();
            $data['pengaduan_aktif'] = $pengaduanModel->getActive();
            $data['kontrak_menunggu'] = $kontrakModel->getMenunggu();
        }

        require_once __DIR__ . '/../../views/dashboard/' . $role . '.php';
    }
}
