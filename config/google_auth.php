<?php
// Perhatikan tanda ../ (artinya mundur satu folder untuk cari vendor)
require_once __DIR__ . '/../vendor/autoload.php';

// Masukkan Client ID & Secret kamu
$clientID = 'GANTI_DENGAN_CLIENT_ID_ANDA';
$clientSecret = 'GANTI_DENGAN_CLIENT_SECRET_ANDA';

// Redirect URI (Sesuaikan lokasi file callback nanti)
// Kita akan taruh callback di folder 'auth'
$redirectUri = 'http://localhost/Web_Payroll/auth/google_callback.php';

$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

$google_login_url = $client->createAuthUrl();
?>