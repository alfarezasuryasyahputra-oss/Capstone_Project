<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
  exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '') {
  http_response_code(422);
  echo json_encode(['success' => false, 'message' => 'Nama dan nomor telepon wajib diisi.']);
  exit;
}

$stmt = $pdo->prepare(
  'INSERT INTO appointments (name,phone,service,requested_date,requested_time,message,status)
  VALUES (?,?,?,?,?,?,?)'
);
$stmt->execute([$name, $phone, $service, $date !== '' ? $date : null, $time !== '' ? $time : null, $message, 'Menunggu']);

echo json_encode(['success' => true]);
