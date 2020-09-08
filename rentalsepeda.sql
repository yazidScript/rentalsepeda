-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2020 at 07:25 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentalsepeda`
--

-- --------------------------------------------------------

--
-- Table structure for table `rs_unit`
--

CREATE TABLE `rs_unit` (
  `UNIT_ID` int(100) NOT NULL,
  `UNIT_KODE` varchar(255) NOT NULL,
  `UNIT_MERK` varchar(255) NOT NULL,
  `UNIT_WARNA` varchar(255) NOT NULL,
  `UNIT_GAMBAR` varchar(255) NOT NULL,
  `UNIT_HARGASEWA` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rs_unit`
--

INSERT INTO `rs_unit` (`UNIT_ID`, `UNIT_KODE`, `UNIT_MERK`, `UNIT_WARNA`, `UNIT_GAMBAR`, `UNIT_HARGASEWA`) VALUES
(1, '1234', 'BMX', 'Merah', 'sde23.jpg', '23.000');

-- --------------------------------------------------------

--
-- Table structure for table `_references`
--

CREATE TABLE `_references` (
  `R_CATEGORY` varchar(70) NOT NULL,
  `R_ID` varchar(80) NOT NULL,
  `R_INFO` varchar(100) NOT NULL,
  `R_ORDER` int(11) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `_references`
--

INSERT INTO `_references` (`R_CATEGORY`, `R_ID`, `R_INFO`, `R_ORDER`) VALUES
('ACCOUNT_STATUS', 'USER_ACTIVE', 'Active', 1),
('ACCOUNT_STATUS', 'USER_INACTIVE', 'Inactive', 2),
('ACCOUNT_STATUS', 'USER_SUSPEND', 'Suspended', 3),
('GROUP_ROLE', 'GR_ADMINISTRATOR', 'Administrator', 1),
('DEFAULT_YES_NO', 'N', 'Tidak', 2),
('NOTIFICATION_MEDIA', 'NM_EMAIL', 'Email', 2),
('NOTIFICATION_MEDIA', 'NM_SMS', 'SMS', 1),
('BOOK_STATUS', 'BS_BATAL', 'Batal', 2),
('BOOK_STATUS', 'BS_FINAL', 'Selesai', 3),
('BOOK_STATUS', 'BS_PROSES', 'Proses', 1),
('SOCIAL_MEDIA', 'SM_FACEBOOK', 'Facebook', 2),
('SOCIAL_MEDIA', 'SM_INSTAGRAM', 'Instagram', 4),
('SOCIAL_MEDIA', 'SM_TWITTER', 'Twitter', 3),
('SOCIAL_MEDIA', 'SM_YOUTUBE', 'Youtube', 1),
('VERIFICATION_ASPECT', 'VA_EMAIL', 'Verifikasi email', 4),
('VERIFICATION_ASPECT', 'VA_ID_KK', 'Verifikasi KK', 2),
('VERIFICATION_ASPECT', 'VA_ID_KTP', 'Verifikasi KTP', 1),
('VERIFICATION_ASPECT', 'VA_ID_SIM', 'Verifikasi SIM', 3),
('DEFAULT_YES_NO', 'Y', 'Ya', 1),
('NOTIFICATION_MEDIA', 'NM_APP', 'Application', 3),
('UNIT_STATUS', 'US_OPEN', 'Tersedia', 1),
('UNIT_STATUS', 'US_CLOSED', 'Tidak tersedia', 2),
('KPR_STATUS', 'KPR_PENGAJUAN', 'Pengajuan', 1),
('KPR_STATUS', 'KPR_DOKUMEN', 'Kirim dokumen', 2),
('KPR_STATUS', 'KPR_ANALISA', 'Analisa pengajuan', 3),
('KPR_STATUS', 'KPR_ACC', 'KPR disetujui', 4),
('KPR_STATUS', 'KPR_TOLAK', 'KPR ditolak', 5),
('ACCOUNT_VERIFY', 'ACCT_UNVERIFIED', 'Unverified', 1),
('ACCOUNT_VERIFY', 'ACCT_VERIFIED', 'Verified', 2),
('BASE_CALCULATION_TYPE', 'PERCENTAGE', 'Persentase', 1),
('BASE_CALCULATION_TYPE', 'VALUE', 'Nilai/nominal', 2),
('PAYMENT_CATEGORY', 'PC_TANDA_JADI', 'Tanda jadi', 1),
('PAYMENT_CATEGORY', 'PC_DP', 'Down payment', 2),
('PAYMENT_CATEGORY', 'PC_TERMIN', 'Termin', 3),
('PAYMENT_CATEGORY', 'PC_REFUND', 'Refund', 4),
('PAYMENT_CATEGORY', 'PC_KPR', 'Pelunasan KPR', 5),
('PAYMENT_CATEGORY', 'PC_OTHER', 'Lain-lain', 6),
('JENIS_KELAMIN', 'L', 'Laki-laki', 1),
('JENIS_KELAMIN', 'P', 'Perempuan', 2),
('TIPE_UNIT', 'T36/60', 'Type 36/60', 1),
('TIPE_UNIT', 'T36/90', 'Type 36/90', 2),
('GROUP_ROLE', 'GR_KONSUMEN', 'Konsumen', 2);

-- --------------------------------------------------------

--
-- Table structure for table `_settings`
--

CREATE TABLE `_settings` (
  `SET_ID` varchar(70) NOT NULL,
  `SET_VALUE` varchar(300) NOT NULL,
  `SET_INFO` varchar(300) DEFAULT NULL,
  `SET_DISPLAY_FORM` varchar(1) DEFAULT 'Y' COMMENT 'trv_references : DEFAULT_YES_NO'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `_settings`
--

INSERT INTO `_settings` (`SET_ID`, `SET_VALUE`, `SET_INFO`, `SET_DISPLAY_FORM`) VALUES
('APP_NAME_FOOTER', 'Rental Service', 'Nama aplikasi di footer', 'Y'),
('APP_NAME_HEADER', 'Rental Service', 'Nama aplikasi di header', 'Y'),
('DEFAULT_EMAIL', 'dummyrba@gmail.com', 'Email default', 'Y'),
('DEFAULT_CURRENCY', 'IDR', 'Mata uang standar', 'N'),
('REGISTER_LIMIT_MINUTE', '30', 'Batas waktu (menit) konfirmasi registrasi', 'Y'),
('DEFAULT_EMAIL_FROM', 'dummyrba@gmail.com', 'Default nama pengirim email', 'Y'),
('FB_APP_ID', '312319969186250', 'Facebook App ID', 'Y'),
('FB_APP_PERMISSIONS', 'email,public_profile,publish_actions', 'Facebook App Permissions', 'Y'),
('FB_APP_SECRET', '8789950a5daac10efb6d3e6fe6c10154', 'Facebook App Secret', 'Y'),
('ALLOWED_FILE_EXTENSIONS_CONTENT_DIGITAL', 'jpg, jpeg, png, gif, doc, docx, xls, xlsx, ppt, pptx, pdf, zip, rar, 7z', 'Ekstensi file yang diperbolehkan untuk upload konten produk digital tipe file', 'Y'),
('ALLOWED_FILE_SIZE_DIGITAL_BYTES', '10240000000', 'Ukuran file maksimal yang diperbolehkan untuk upload konten produk digital tipe file (dalam bytes)', 'Y'),
('GOOGLE_API_KEY', 'AIzaSyAAc4yh04bH0rxGhkvw__AOjWC4SiPX4ZM', 'Google API key', 'Y'),
('GOOGLE_OAUTH_CLIENT_ID', '1026901663715-gq57vgnh6isa68dh3ulf7amorkc4g0ma.apps.googleusercontent.com', 'Google OAuth Client ID', 'Y'),
('GOOGLE_OAUTH_CLIENT_SECRET', 'fGv3hcP2NzqvyLB3nqQgmTFM', 'Google OAuth Secret', 'Y'),
('APP_NAME', 'Rental Service', 'Nama aplikasi', 'Y'),
('APP_NAME_SHORT', 'Rental Service', 'Nama aplikasi versi ringkas', 'Y'),
('CLIENT_NAME_SHORT', 'Rental Service', 'Nama klien versi singkat', 'Y'),
('CUST_SERVICE_PHONE', '085740151761', 'Nomor kontak CS', 'Y'),
('CUST_SERVICE_EMAIL', 'dummyrba@gmail.com', 'Email CS', 'Y'),
('CUST_SERVICE_U_ID', '090909', 'User ID CS', 'Y'),
('DEFAULT_KPR_ANGSURAN_5_THN', '1030000', 'Angsuran bulanan KPR tenor 5 thn', 'Y'),
('DEFAULT_KPR_ANGSURAN_10_THN', '1380000', 'Angsuran bulanan KPR tenor 10 thn', 'Y'),
('DEFAULT_KPR_ANGSURAN_15_THN', '2455000', 'Angsuran bulanan KPR tenor 15 thn', 'Y'),
('DEFAULT_KPR_MULTIPLIER_ANGSURAN', '3', 'Faktor kali angsuran dibayar di muka', 'Y'),
('DEFAULT_BIAYA_BPHTB', '2500000', 'BPHTB', 'Y'),
('DEFAULT_BIAYA_NOTARIS', '2000000', 'Biaya notaris', 'Y'),
('DEFAULT_BIAYA_SHM', '850000', 'Biaya SHM', 'Y'),
('DEFAULT_BIAYA_KPR', '500000', 'Biaya KPR', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `_users`
--

CREATE TABLE `_users` (
  `U_ID` varchar(80) NOT NULL,
  `U_PASSWORD` varchar(80) NOT NULL,
  `U_PASSWORD_HASH` varchar(80) NOT NULL,
  `U_NAME` varchar(80) NOT NULL DEFAULT 'Trevalia.com Default User',
  `U_AUTHORITY_ID_1` varchar(25) DEFAULT '-',
  `U_AUTHORITY_ID_2` varchar(25) DEFAULT '-',
  `U_AUTHORITY_ID_3` varchar(25) DEFAULT '-',
  `U_EMAIL` varchar(125) DEFAULT '-',
  `U_INFO` varchar(140) DEFAULT '-',
  `U_GROUP_ROLE` varchar(30) DEFAULT 'GR_KONSUMEN' COMMENT '_references : GROUP_ROLE',
  `U_REG_DATE` date DEFAULT '0000-00-00',
  `U_DEVICE_ID` varchar(300) DEFAULT '-',
  `U_PHONE` varchar(20) DEFAULT '-',
  `U_FCM_TOKEN` varchar(255) DEFAULT '-',
  `U_ADDRESS` varchar(200) DEFAULT '-',
  `U_CITY` varchar(80) DEFAULT '-',
  `U_ZIP_CODE` varchar(7) DEFAULT '-',
  `U_PROVINCE` varchar(80) DEFAULT '-',
  `U_ACCT_VERIFY` varchar(30) DEFAULT 'ACCT_UNVERIFIED' COMMENT 'trv_references : ACCOUNT_VERIFY',
  `U_ACCT_VERIFY_IMG` varchar(250) DEFAULT '-',
  `U_ACCT_VERIFY_DATE` datetime DEFAULT '0000-00-00 00:00:00',
  `U_STATUS` varchar(30) DEFAULT 'USER_ACTIVE' COMMENT 'references : ACCOUNT_STATUS',
  `U_LOGIN_TOKEN` varchar(50) DEFAULT '-',
  `U_LOGIN_TIME` datetime DEFAULT '0000-00-00 00:00:00',
  `U_LOGOUT_TIME` datetime DEFAULT '0000-00-00 00:00:00',
  `U_FB_ID` varchar(100) DEFAULT '-' COMMENT 'Facebook ID',
  `U_FB_NAME` varchar(100) DEFAULT '-' COMMENT 'Facebook user name',
  `U_GOOGLE_ID` varchar(100) DEFAULT '-' COMMENT 'Google ID',
  `U_GOOGLE_NAME` varchar(100) DEFAULT '-' COMMENT 'Google user name',
  `U_GOOGLE_PICTURE_URL` varchar(250) DEFAULT '-',
  `U_AVATAR_PATH` varchar(250) DEFAULT 'uploads/images/profiles/no-person.jpg',
  `U_BANNER_PATH` varchar(100) DEFAULT '-',
  `U_FACEBOOK` varchar(250) DEFAULT '-',
  `U_INSTAGRAM` varchar(50) DEFAULT '-',
  `U_TWITTER` varchar(50) DEFAULT '-',
  `U_BASE_LANGUAGE` varchar(30) DEFAULT '.id' COMMENT 'rtk_references : BASE_LANGUAGE',
  `U_COUNTRY_ID` varchar(30) DEFAULT 'C_ID' COMMENT 'rtk_references : COUNTRY_ID',
  `U_BANNER_IMG_PATH` varchar(250) DEFAULT '-',
  `U_REG_CONFIRM_EMAIL_TOKEN` varchar(50) DEFAULT '-',
  `U_REG_CONFIRM_EMAIL_DATE` datetime DEFAULT '0000-00-00 00:00:00',
  `U_REG_CONFIRM_SMS_TOKEN` varchar(50) DEFAULT '-',
  `U_REG_CONFIRM_SMS_DATE` datetime DEFAULT '0000-00-00 00:00:00',
  `U_REG_CONFIRM_LIMIT_DATE` datetime DEFAULT '0000-00-00 00:00:00',
  `U_REFERAL` varchar(100) DEFAULT '-',
  `SYS_CREATE_TIME` datetime DEFAULT '0000-00-00 00:00:00',
  `SYS_CREATE_USER` varchar(80) DEFAULT '-',
  `SYS_UPDATE_TIME` datetime DEFAULT '0000-00-00 00:00:00',
  `SYS_UPDATE_USER` varchar(80) DEFAULT '-'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `_users`
--

INSERT INTO `_users` (`U_ID`, `U_PASSWORD`, `U_PASSWORD_HASH`, `U_NAME`, `U_AUTHORITY_ID_1`, `U_AUTHORITY_ID_2`, `U_AUTHORITY_ID_3`, `U_EMAIL`, `U_INFO`, `U_GROUP_ROLE`, `U_REG_DATE`, `U_DEVICE_ID`, `U_PHONE`, `U_FCM_TOKEN`, `U_ADDRESS`, `U_CITY`, `U_ZIP_CODE`, `U_PROVINCE`, `U_ACCT_VERIFY`, `U_ACCT_VERIFY_IMG`, `U_ACCT_VERIFY_DATE`, `U_STATUS`, `U_LOGIN_TOKEN`, `U_LOGIN_TIME`, `U_LOGOUT_TIME`, `U_FB_ID`, `U_FB_NAME`, `U_GOOGLE_ID`, `U_GOOGLE_NAME`, `U_GOOGLE_PICTURE_URL`, `U_AVATAR_PATH`, `U_BANNER_PATH`, `U_FACEBOOK`, `U_INSTAGRAM`, `U_TWITTER`, `U_BASE_LANGUAGE`, `U_COUNTRY_ID`, `U_BANNER_IMG_PATH`, `U_REG_CONFIRM_EMAIL_TOKEN`, `U_REG_CONFIRM_EMAIL_DATE`, `U_REG_CONFIRM_SMS_TOKEN`, `U_REG_CONFIRM_SMS_DATE`, `U_REG_CONFIRM_LIMIT_DATE`, `U_REFERAL`, `SYS_CREATE_TIME`, `SYS_CREATE_USER`, `SYS_UPDATE_TIME`, `SYS_UPDATE_USER`) VALUES
('085870205314', '12345678', '25d55ad283aa400af464c76d713c07ad', 'YAZID SHOLLAKHUDIN AINUR KHAFID', '-', '-', '-', 'yazid123@gmail.com', '-', 'GR_ADMIN', '2020-03-04', '-', '085870205314', '-', '-', '-', '-', '-', 'ACCT_UNVERIFIED', '-', '0000-00-00 00:00:00', 'USER_ACTIVE', '485ff3caee7824370ad3', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '-', '-', '-', '-', '-', 'uploads/images/profiles/no-person.jpg', '-', '-', '-', '-', '.id', 'C_ID', '-', '-', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-'),
('0123456', '121212', '93279e3308bdbbeed946fc965017f67a', 'PAIJO', '555555', '-', '-', 'paijo@gmail.com', '-', 'GR_KONSUMEN', '2020-08-22', '-', '0123456', '-', 'gebog', '-', '-', '-', 'ACCT_UNVERIFIED', '-', '0000-00-00 00:00:00', 'USER_ACTIVE', '-', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '-', '-', '-', '-', '-', 'uploads/images/profiles/no-person.jpg', '-', '-', '-', '-', '.id', 'C_ID', '-', '-', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'RENTALSEPEDA', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-'),
('083456789', '1234', '81dc9bdb52d04dc20036dbd8313ed055', 'YAZID', '121212121', '-', '-', 'coba@gmail.com', '-', 'GR_KONSUMEN', '2020-08-15', '-', '083456789', '-', 'BAE', '-', '-', '-', 'ACCT_UNVERIFIED', '-', '0000-00-00 00:00:00', 'USER_ACTIVE', '-', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '-', '-', '-', '-', '-', 'uploads/images/profiles/no-person.jpg', '-', '-', '-', '-', '.id', 'C_ID', '-', '-', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'RENTALSEPEDA', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rs_unit`
--
ALTER TABLE `rs_unit`
  ADD PRIMARY KEY (`UNIT_ID`);

--
-- Indexes for table `_references`
--
ALTER TABLE `_references`
  ADD PRIMARY KEY (`R_ID`,`R_CATEGORY`) USING BTREE;

--
-- Indexes for table `_settings`
--
ALTER TABLE `_settings`
  ADD PRIMARY KEY (`SET_ID`) USING BTREE;

--
-- Indexes for table `_users`
--
ALTER TABLE `_users`
  ADD PRIMARY KEY (`U_ID`) USING BTREE,
  ADD UNIQUE KEY `IDX_U_ID` (`U_ID`) USING BTREE COMMENT '(null)',
  ADD KEY `U_FB_ID` (`U_FB_ID`) USING BTREE COMMENT '(null)',
  ADD KEY `U_GOOGLE_ID` (`U_GOOGLE_ID`) USING BTREE COMMENT '(null)',
  ADD KEY `U_ACCT_STATUS` (`U_STATUS`) USING BTREE COMMENT '(null)',
  ADD KEY `U_PHONE` (`U_PHONE`) USING BTREE COMMENT '(null)';

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rs_unit`
--
ALTER TABLE `rs_unit`
  MODIFY `UNIT_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
