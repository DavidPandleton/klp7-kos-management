<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private static string $host = 'smtp.gmail.com';
    private static int $port = 587;
    private static string $username = 'kosku.notif@gmail.com';
    private static string $password = 'your-app-password';
    private static string $fromName = 'KosKu Management';

    public static function send(string $to, string $toName, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = self::$host;
            $mail->SMTPAuth = true;
            $mail->Username = self::$username;
            $mail->Password = self::$password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = self::$port;

            $mail->setFrom(self::$username, self::$fromName);
            $mail->addAddress($to, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function notifJatuhTempo(string $nama, string $kamar, int $jumlah, string $batas): string
    {
        return "
        <h2>Pengingat Pembayaran Kos</h2>
        <p>Halo <b>{$nama}</b>,</p>
        <p>Pembayaran sewa kamar <b>{$kamar}</b> sebesar <b>Rp " . number_format($jumlah, 0, ',', '.') . "</b> akan jatuh tempo pada <b>{$batas}</b>.</p>
        <p>Silakan melakukan pembayaran tepat waktu untuk menghindari denda.</p>
        <br>
        <p>Terima kasih,</p>
        <p><b>KosKu Management</b></p>
        ";
    }

    public static function notifKonfirmasi(string $nama, string $kamar, int $jumlah, string $status): string
    {
        return "
        <h2>Konfirmasi Pembayaran</h2>
        <p>Halo <b>{$nama}</b>,</p>
        <p>Pembayaran sewa kamar <b>{$kamar}</b> sebesar <b>Rp " . number_format($jumlah, 0, ',', '.') . "</b> telah <b>{$status}</b>.</p>
        <br>
        <p>Terima kasih,</p>
        <p><b>KosKu Management</b></p>
        ";
    }
}
