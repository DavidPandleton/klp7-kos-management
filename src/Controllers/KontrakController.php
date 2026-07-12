<?php

namespace App\Controllers;

use App\Models\Kontrak;
use App\Models\User;
use App\Models\Kamar;
use App\Middleware\Auth;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;

class KontrakController
{
    private Kontrak $kontrak;

    public function __construct()
    {
        Auth::check();
        $this->kontrak = new Kontrak();
    }

    public function index(): void
    {
        Auth::role(['admin', 'pemilik']);

        $status = $_GET['status'] ?? '';
        $filter = '';
        $params = [];

        if (!empty($status)) {
            $filter .= " AND k.status = ?";
            $params[] = $status;
        }

        $data = $this->kontrak->getAll($filter, $params);
        require_once __DIR__ . '/../../views/kontrak/index.php';
    }

    public function create(): void
    {
        $role = Auth::getUserRole();
        $kamarId = $_GET['kamar_id'] ?? null;

        if ($kamarId && $role === 'penyewa') {
            $kamarModel = new Kamar();
            $kamar = $kamarModel->find((int) $kamarId);
            if (!$kamar || $kamar['status'] !== 'tersedia') {
                Session::setFlash('error', 'Kamar tidak tersedia.');
                header('Location: /kamar/index');
                exit;
            }
            $penyewaId = Auth::getUserId();
            $penyewaList = [];
            $kamarList = [$kamar];
        } else {
            Auth::role(['admin', 'pemilik']);
            $userModel = new User();
            $kamarModel = new Kamar();
            $penyewaList = $userModel->getByRole('penyewa');
            $kamarList = $kamarModel->getAll(" AND status = 'tersedia'", []);
            $penyewaId = null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\App\Helpers\Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Token tidak valid.');
                require_once __DIR__ . '/../../views/kontrak/create.php';
                return;
            }

            $v = new Validator();
            if ($role === 'penyewa') {
                $v->required('kamar_id', $_POST['kamar_id'], 'Kamar');
            } else {
                $v->required('penyewa_id', $_POST['penyewa_id'], 'Penyewa');
                $v->required('kamar_id', $_POST['kamar_id'], 'Kamar');
            }
            $v->required('tgl_mulai', $_POST['tgl_mulai'], 'Tanggal mulai');
            $v->required('tgl_akhir', $_POST['tgl_akhir'], 'Tanggal akhir');

            if ($v->passes()) {
                $tglMulai = strtotime($_POST['tgl_mulai']);
                $tglAkhir = strtotime($_POST['tgl_akhir']);
                if ($tglMulai <= strtotime('today')) {
                    Session::setFlash('error', 'Tanggal mulai harus setelah hari ini.');
                    require_once __DIR__ . '/../../views/kontrak/create.php';
                    return;
                }
                if ($tglAkhir <= $tglMulai) {
                    Session::setFlash('error', 'Tanggal akhir harus setelah tanggal mulai.');
                    require_once __DIR__ . '/../../views/kontrak/create.php';
                    return;
                }
                if (($tglAkhir - $tglMulai) > 365 * 24 * 3600) {
                    Session::setFlash('error', 'Durasi kontrak maksimal 12 bulan.');
                    require_once __DIR__ . '/../../views/kontrak/create.php';
                    return;
                }
                $_POST['status'] = ($role === 'penyewa') ? 'menunggu' : 'aktif';
                $this->kontrak->create($_POST);
                Session::setFlash('success', 'Kontrak berhasil dibuat.');
                header('Location: ' . ($role === 'penyewa' ? '/kamar/index' : '/kontrak/index'));
                exit;
            }

            Session::setFlash('error', $v->firstError());
        }

        require_once __DIR__ . '/../../views/kontrak/create.php';
    }

    public function setujui(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        $kontrak = $this->kontrak->find($id);
        if (!$kontrak || $kontrak['status'] !== 'menunggu') {
            Session::setFlash('error', 'Kontrak tidak dapat disetujui.');
            header('Location: /kontrak/index');
            exit;
        }
        $this->kontrak->updateStatus($id, 'aktif');

        $tglMulai = new \DateTime($kontrak['tgl_mulai']);
        $tglAkhir = new \DateTime($kontrak['tgl_akhir']);
        $tglAkhir->modify('+1 day');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($tglMulai, $interval, $tglAkhir);

        $pembayaranModel = new \App\Models\Pembayaran();
        foreach ($period as $dt) {
            $pembayaranModel->create([
                'kontrak_id' => $id,
                'bulan' => (int) $dt->format('n'),
                'tahun' => (int) $dt->format('Y'),
                'jumlah' => $kontrak['harga'],
                'status' => 'belum_bayar',
            ]);
        }

        Session::setFlash('success', 'Kontrak disetujui, kamar sekarang terisi.');
        header('Location: /kontrak/detail/' . $id);
        exit;
    }

    public function selesaikan(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        $this->kontrak->updateStatus($id, 'selesai');
        Session::setFlash('success', 'Kontrak diselesaikan.');
        header('Location: /kontrak/index');
        exit;
    }

    public function batalkan(int $id): void
    {
        Auth::role(['admin', 'pemilik']);
        $this->kontrak->updateStatus($id, 'dibatalkan');
        Session::setFlash('success', 'Kontrak dibatalkan.');
        header('Location: /kontrak/index');
        exit;
    }

    public function detail(int $id): void
    {
        $kontrak = $this->kontrak->find($id);
        if (!$kontrak) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }
        if (Auth::getUserRole() === 'penyewa' && (int) $kontrak['penyewa_id'] !== Auth::getUserId()) {
            http_response_code(403);
            require_once __DIR__ . '/../../views/errors/403.php';
            return;
        }
        $pembayaranModel = new \App\Models\Pembayaran();
        $riwayatBayar = $pembayaranModel->getByKontrak($id);
        require_once __DIR__ . '/../../views/kontrak/detail.php';
    }
}
