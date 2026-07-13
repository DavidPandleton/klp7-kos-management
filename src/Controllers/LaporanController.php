<?php

namespace App\Controllers;

use App\Models\Pembayaran;
use App\Middleware\Auth;
use App\Helpers\Session;
use Fpdf\Fpdf;

class LaporanController
{
    public function __construct()
    {
        Auth::check();
        Auth::role(['admin', 'pemilik']);
    }

    public function index(): void
    {
        require_once __DIR__ . '/../../views/laporan/index.php';
    }

    public function pdf(): void
    {
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';

        if (!ctype_digit($bulan) || $bulan < 1 || $bulan > 12) $bulan = date('n');
        if (!ctype_digit($tahun) || $tahun < 2024 || $tahun > 2099) $tahun = date('Y');

        $pembayaranModel = new Pembayaran();
        $data = $pembayaranModel->getAll(" AND MONTH(p.tgl_bayar) = ? AND YEAR(p.tgl_bayar) = ? AND p.status = 'lunas'", [(int)$bulan, (int)$tahun]);

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Helvetica', 'B', 18);
        $pdf->Cell(0, 10, 'KOSKU MANAGEMENT', 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 6, 'Laporan Pembayaran Bulanan', 0, 1, 'C');
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Periode: ' . $namaBulan[(int)$bulan] . ' ' . $tahun, 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetFillColor(52, 73, 94);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(55, 8, 'Penyewa', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Kamar', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Jumlah', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Denda', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Status', 1, 1, 'C', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Helvetica', '', 10);
        $no = 1;
        $total = 0;

        if (empty($data)) {
            $pdf->Cell(190, 10, 'Tidak ada data pembayaran.', 1, 1, 'C');
        } else {
            foreach ($data as $row) {
                $pdf->Cell(10, 7, $no++, 1, 0, 'C');
                $pdf->Cell(55, 7, $row['nama_penyewa'], 1, 0);
                $pdf->Cell(30, 7, $row['nomor_kamar'], 1, 0, 'C');
                $pdf->Cell(40, 7, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1, 0, 'R');
                $pdf->Cell(25, 7, 'Rp ' . number_format($row['denda'] ?? 0, 0, ',', '.'), 1, 0, 'R');
                $pdf->Cell(30, 7, $row['status'], 1, 1, 'C');
                $total += $row['jumlah'] + ($row['denda'] ?? 0);
            }
        }

        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(95, 8, '  Total Pendapatan', 1, 0, 'L', true);
        $pdf->Cell(95, 8, 'Rp ' . number_format($total, 0, ',', '.'), 1, 1, 'R', true);

        $pdf->Ln(15);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 6, 'Denpasar, ' . date('d ') . $namaBulan[(int)date('n')] . date(' Y'), 0, 1, 'R');
        $pdf->Ln(15);
        $pdf->Cell(0, 6, '(...............................................)', 0, 1, 'R');
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Pemilik Kos', 0, 1, 'R');

        $pdf->Output('I', 'laporan-pembayaran-' . $bulan . '-' . $tahun . '.pdf');
        exit;
    }
}
