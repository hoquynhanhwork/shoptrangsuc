<?php

declare(strict_types=1);

$GLOBALS['db_ready'] = false;

$dbHost = '127.0.0.1';
$dbName = 'shoptrangsuc';
$dbUser = 'root';
$dbPass = '';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
	die('Khong the ket noi den co so du lieu.');
}

$conn->set_charset('utf8mb4');
$GLOBALS['db_ready'] = true;

$conn->query("CREATE TABLE IF NOT EXISTS `lienhe` (
	`MaLienHe` int(11) NOT NULL AUTO_INCREMENT,
	`HoTen` varchar(100) NOT NULL,
	`SoDienThoai` varchar(20) DEFAULT NULL,
	`Email` varchar(100) NOT NULL,
	`ChuDe` varchar(255) NOT NULL,
	`NoiDung` text NOT NULL,
	`TrangThai` enum('new','read','replied') DEFAULT 'new',
	`NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (`MaLienHe`),
	KEY `idx_lienhe_email` (`Email`),
	KEY `idx_lienhe_trangthai` (`TrangThai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

function db_escape(mysqli $conn, string $value): string
{
	return $conn->real_escape_string(trim($value));
}

function db_count(mysqli $conn, string $table, string $where = '1 = 1'): int
{
	$query = "SELECT COUNT(*) AS total FROM `{$table}` WHERE {$where}";
	$result = $conn->query($query);

	if (!$result) {
		return 0;
	}

	$row = $result->fetch_assoc();
	return (int) ($row['total'] ?? 0);
}