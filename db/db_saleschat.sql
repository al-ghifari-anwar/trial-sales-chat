-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2023 at 09:25 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_saleschat`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_contact`
--

CREATE TABLE `tb_contact` (
  `id_contact` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nomorhp` varchar(15) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `store_owner` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_contact`
--

INSERT INTO `tb_contact` (`id_contact`, `nama`, `nomorhp`, `tgl_lahir`, `store_owner`) VALUES
(109, 'Ar', '6287872342441', '2013-07-12', 'Al Ghifari'),
(110, 'UD BANGUN TANI SDA', '6281217041705', NULL, ''),
(111, 'SINAR PAGI BUNCITAN SDA', '6285103224052', NULL, ''),
(112, 'MAPAN USAHA MDN', '6285855774690', NULL, ''),
(113, 'TB CEMERLANG LWG', '6285105390908', NULL, ''),
(114, 'Makmur Putra Mandiri KDR', '6282233453699', NULL, ''),
(115, 'TB SUPRINDO KDR', '628121815707', NULL, ''),
(116, 'SARI BUMI RAYA RANGKAH SDA', '6281335533005', NULL, ''),
(117, 'TB MAJU JAYA MLG', '6287701666688', NULL, ''),
(118, 'TINA', '8615081849602', NULL, ''),
(119, 'TB SUMBER JAYA klampok LWG', '6285100778019', NULL, ''),
(120, 'Pak Hartawan', '6281808152028', NULL, ''),
(121, 'TB MUBAROK MDN', '6282331267353', NULL, ''),
(122, 'KUN JAYA MDN', '6281335845711', NULL, ''),
(123, 'UD MAKMUR JAYA KETINDAN LWG', '6285100405903', NULL, ''),
(124, 'UD TRI TUNGGAL MLG', '6285103479969', NULL, ''),
(125, 'PB Zam Zam Bgr', '6281387540999', NULL, ''),
(126, 'UD ERIK PUTRA MLG', '6282337752500', NULL, ''),
(127, 'TB PUTRA JAYA MAKMUR LWG', '6281249380283', NULL, ''),
(128, 'jess 1', '6287771136555', NULL, ''),
(129, 'W A H I D _ T O Y O T A 💯', '6285373922000', NULL, ''),
(130, 'TB RINGIN AGUNG MLG', '6282199811798', NULL, ''),
(131, 'TB DK PUTRA LWG', '6289621433314', NULL, ''),
(132, 'TB Sentosa Mapan LWG', '6281344597979', NULL, ''),
(133, 'TB BUDI JAYA LWG', '6281252588848', NULL, ''),
(134, 'TB TLOGOSARI MLG', '6285100034000', NULL, ''),
(135, 'UD KARANGLO LWG', '6281231389519', NULL, ''),
(136, 'TB NEW WOK LWG', '6285746868676', NULL, ''),
(137, 'TB UTAMA JAYA LWG', '6281803865266', NULL, ''),
(138, 'TB BERKAH JAYA MLG', '6281332683297', NULL, ''),
(139, 'TB BUMI MAS 2 LWG', '6281330338002', NULL, ''),
(140, 'TB ANUGRAH LWG', '6281216711168', NULL, ''),
(141, 'UD SUMBER BENING LWG', '6289608561118', NULL, ''),
(142, 'TB SINAR JAYA LWG', '6281231391500', NULL, ''),
(143, 'TB BAROKAH SENTONG LWG', '6285234882582', NULL, ''),
(144, 'TB RIMBA LANCAR LWG', '6281230706050', NULL, ''),
(145, 'TB DHANI JAYA ISTANA BEDALI LWG', '6285100752445', NULL, ''),
(146, 'TB SUMBER REJEKI LANCAR LWG', '6281233639444', NULL, ''),
(147, 'TB JAYA MAKMUR LWG', '6285755857583', NULL, ''),
(148, 'Unggul Jaya LWG', '6281335563330', NULL, ''),
(149, 'UD MITRA BANGUN LWG', '6281330491447', NULL, ''),
(150, 'TB TRI MAKMUR LWG', '6282257364325', NULL, ''),
(151, 'TB KARANG JATI', '628563570775', NULL, ''),
(152, 'TK MUSTIKA JAYA', '6282336006062', NULL, ''),
(153, 'sales lawang', '6281994005758', NULL, ''),
(154, 'TB AGUNG LUMINTU LWG', '6285100716677', NULL, ''),
(155, 'UD RIZKA JAYA 3', '6282245114543', NULL, ''),
(156, 'UD SUMBER JAYA Candi LWG', '6288231705310', NULL, ''),
(157, 'TB Karya Abadi LWG', '6281615879945', NULL, ''),
(158, 'UD.SUMBER PADI LWG', '6281249691078', NULL, ''),
(159, 'TB EKA JAYA LWG', '6283834375957', NULL, ''),
(160, 'TB PANGGUNG REJEKI LWG', '6287856433091', NULL, ''),
(161, 'TB DUA PUTRI LWG', '6282245383858', NULL, ''),
(162, 'TIB Lawang', '6281336144445', NULL, ''),
(163, 'TB EKA JAYA MLG', '628125285322', NULL, ''),
(164, 'TB GEMILANG MLG', '6285100012056', NULL, ''),
(165, 'TB WASKITO YOGYA', '6287834565669', NULL, ''),
(166, 'TOKO BAROKAH ORCHID SDA', '6285746390903', NULL, ''),
(167, 'TB MURAH JAYA YOGYA', '6282137360994', NULL, ''),
(168, 'TK JAYA PERKASA SDA ', '6285733506075', NULL, ''),
(169, 'TOKO DJATI MAKMUR SDA', '628123174741', NULL, ''),
(170, 'TB YONO PASIR MLG', '6281333371356', NULL, ''),
(171, 'CV REMAJA ABADI PERKASA MDN', '6281335005100', NULL, ''),
(172, 'UD TYRISA MDN', '6282338280607', NULL, ''),
(173, 'TB KARUNIA JAYA TNG', '6285245606777', NULL, ''),
(174, 'UD BUMI PERMATA HIJAU CITRA SDA', '6281331358036', NULL, ''),
(175, 'UD SARI BUMI MERDEKA SDA', '6285100693796', NULL, ''),
(176, 'BINTANG JAYA SDA', '6281553361891', NULL, ''),
(177, 'BUMI PERMATA HIJAU KALIPECABEAN SDA', '6285877540933', NULL, ''),
(178, 'TB CITRA JAYA SDA', '6285236440876', NULL, ''),
(179, 'TB ANAM MLG', '6285100324516', NULL, ''),
(180, 'TB BANJAR BARU MLG', '6285755441849', NULL, ''),
(181, 'TB SADAR JAYA MLG', '6281216708827', NULL, ''),
(182, 'SUBUR JAYA SDA', '6287853562029', NULL, ''),
(183, 'TB.BUMIREJO MLG', '6281320333536', NULL, ''),
(184, 'TB DINAR MLG', '6281333315243', NULL, ''),
(185, 'UD RINJANI', '6285101488661', NULL, ''),
(186, 'TB BAROKAH JEDONG', '6285785210481', NULL, ''),
(187, 'TB LANGGENG ARTO MLG', '6285100430777', NULL, ''),
(188, 'TB SUMBER LANCAR 1', '6281333446374', NULL, ''),
(189, 'TB RIZQI AGUNG', '6282140715959', NULL, ''),
(190, 'TOKO SARITAMA MLG ', '6285748378420', NULL, ''),
(191, 'TB JAYA MAKMUR', '6281333838320', NULL, ''),
(192, 'TB AL HIDAYAH', '6281358941350', NULL, ''),
(193, 'TB SINAR GRAHA KENCANA', '6282139284548', NULL, ''),
(194, 'TB SOBO LANGIT', '6285102587569', NULL, ''),
(195, 'TB MANDIRI', '6282130059900', NULL, ''),
(196, 'TB CAHAYA TERANG 8', '6285334124234', NULL, ''),
(197, 'TB.MITRA KARYA', '6281233660086', NULL, ''),
(198, 'TB RESTU JAYA', '628122827147', NULL, ''),
(199, 'TB RIZKI JAYA MAKMUR MLG', '6282231077888', NULL, ''),
(200, 'TB ATIKA JAYA', '6281232538698', NULL, ''),
(201, 'TK JAYA', '6285103001773', NULL, ''),
(202, 'TB RIZQY JAYA', '6285100747471', NULL, ''),
(203, 'TB SADAR JAYA MLG', '6282131740835', NULL, ''),
(204, 'TOKO LANGGENG ABADI', '6281233453755', NULL, ''),
(205, 'TB INDRA SURYA 2', '6282232828218', NULL, ''),
(206, 'TB MEKAR SARI', '6285100015877', NULL, ''),
(207, 'TB MIRA REJEKI', '6281232577769', NULL, ''),
(208, 'TK MITRA SEJATI', '6285100561964', NULL, ''),
(209, 'SINAR ABADI CILEBUT BGR', '', NULL, ''),
(210, 'SINAR ABADI CILEBUT BGR', '', NULL, ''),
(211, 'TB Makmur Bangunan KDR', '6281335555598', NULL, ''),
(212, 'TB SINAR REJEKI KDR', '6281335564423', NULL, ''),
(213, 'TOKO JOYO LWG', '6282331468793', NULL, ''),
(214, 'TB.TRD JAYA PAKIS MLG', '6281230218562', NULL, ''),
(215, 'TB RAHAYU MDN', '6282330020809', NULL, ''),
(216, 'TK MIDO JAYA 2 MLG ', '6285100694531', NULL, ''),
(217, 'CV MERCUSUAR TRUSS SDA', '6285101498803', NULL, ''),
(218, 'BAROKAH DENGKOL LWG', '6282233327574', NULL, ''),
(219, 'TB SAMIAJI MLG', '6285854959898', NULL, ''),
(220, 'UD. NAURA JAYA SDA', '6285100991698', NULL, ''),
(221, 'UD USAHA BARU-Kepanjen MLG', '6288971093601', NULL, ''),
(222, 'TB SUMBER JAYA 2 MLG', '6281230448995', NULL, ''),
(223, 'UD BUMI JOYO SUKODONO SDA', '6281515916007', NULL, ''),
(224, 'TB ALAM INDAH YOGYA ', '628990517182', NULL, ''),
(225, 'ALAM INDAH YOGYA', '6287738329541', NULL, ''),
(226, 'TB SUMBER JAYA BATURETNO LWG', '6281235410183', NULL, ''),
(227, 'TB SARI BUMI RAYA SNL SDA', '6285100386833', NULL, ''),
(228, 'TB DICKY JAYA LWG', '6282244440075', NULL, ''),
(229, 'Tk Yoga', '6287871261170', NULL, ''),
(230, 'RRS Putra SDA', '6285859400209', NULL, ''),
(231, 'UD NUSA INDAH SDA', '6281357700577', NULL, ''),
(232, 'UD JIDAN Pinang SDA', '6281333317860', NULL, ''),
(233, 'UD FAJAR JAYA SDA', '6281353636906', NULL, ''),
(234, 'TB HANURA TULANGAN SDA', '6285655498332', NULL, ''),
(235, 'UD SINAR JAYA REJO SDA', '6282131378435', NULL, ''),
(236, 'TB RESTU BANJARKEMANTREN SDA', '6281242368029', NULL, ''),
(237, 'UD FAJAR MULIA Buduran SDA', '6285107078885', NULL, ''),
(238, 'TB JAYA BERSAMA SDA', '6281330430629', NULL, ''),
(239, 'SUMBER MAKMUR WUNUT SDA', '6281230859806', NULL, ''),
(240, 'TB TANJUNG JAYA MAKMUR MLG', '6285648305039', NULL, ''),
(241, 'Baja Trikarsa Persada', '6281292873454', NULL, ''),
(242, 'pak anong TKP', '6281259703257', NULL, ''),
(243, 'UD BERKAH BERSAMA SDA', '6281331955397', NULL, ''),
(244, 'CV REMAJA ABADI PERKASA MDN', '62811331363', NULL, ''),
(245, 'BERKAH MULIA KDR', '6281234923923', NULL, ''),
(246, 'Karya Abadi Karangploso MLG', '6285100020695', NULL, ''),
(247, 'TB CAHAYA NUSA KDR', '6281216913345', NULL, ''),
(248, 'UD JOYO BOYO KDR', '628125098369', NULL, ''),
(249, 'BAROKAH SUNGON SDA', '6285851289376', NULL, ''),
(250, 'TOKO BAROKAH KEPODANG SDA', '6282245763211', NULL, ''),
(251, 'TB MOROJOYO MLG', '6281252353686', NULL, ''),
(252, 'Pak sugi  TKP', '6285237597689', NULL, ''),
(253, 'yogagepenk', '6281328195303', NULL, ''),
(254, 'TOKO AGUNG BGR', '6281316618788', NULL, ''),
(255, 'TOKO NASIONAL MDN', '6285235542013', NULL, ''),
(256, 'TB BANDAR JAYA KDR', '6285748444419', NULL, ''),
(257, 'TB REJO JAYA BARU MLG', '6281945510606', NULL, ''),
(258, 'Sumber Jaya Ngampel KDR', '6281334647128', NULL, ''),
(259, 'tb murah abadi sentul bgr', '6287770436025', NULL, ''),
(260, 'Jessica Hart', '6287771736555', NULL, ''),
(261, 'Pak Ridwan PT John', '6282274799868', NULL, ''),
(262, 'UD BUMI BERSATU SDA', '6285732569312', NULL, ''),
(263, 'UD BERKAT SUBUR JABANG KDR', '6285736243222', NULL, ''),
(264, 'Bu Tini', '6287774436555', NULL, ''),
(265, 'TB SINAR MAWAR', '6287859414899', NULL, ''),
(266, 'TB MAKMUR JAYA BEDALI LWG', '6285234590782', NULL, ''),
(267, 'TOKO WERINGIN SDA', '6281330408424', NULL, ''),
(268, 'TB SINAR JAYA BANGUNAN TNG', '6281283259598', NULL, ''),
(269, 'AR JAYA SDA', '6281333377082', NULL, ''),
(270, 'UD TEGAR JAYA SDA', '6282337426334', NULL, ''),
(271, 'UD HM SHALEH SDA', '6282257043236', NULL, ''),
(272, 'TB DANAN JAYA MLG', '6285706830825', NULL, ''),
(273, 'TB BAROKAH JAYA PAKIS MLG', '6281333477740', NULL, ''),
(274, 'TB DICK MLG', '6281338132019', NULL, ''),
(275, 'SANJAYA II SDA', '6281249215130', NULL, ''),
(276, 'ALDI  JAYA SDA', '6285784377586', NULL, ''),
(277, 'TB LANCAR-Mojoroto KDR', '6281233218335', NULL, ''),
(278, 'TB Berkat Subur KDR', '6285791548246', NULL, ''),
(279, 'UD USAHA BARU MLG', '6285100444420', NULL, ''),
(280, 'Leonardo', '6281221685551', NULL, ''),
(282, 'Trial Al', '6285546112267', '2013-07-11', 'Al');

-- --------------------------------------------------------

--
-- Table structure for table `tb_messages`
--

CREATE TABLE `tb_messages` (
  `id_message` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `message_body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_messages`
--

INSERT INTO `tb_messages` (`id_message`, `id_contact`, `message_body`) VALUES
(1, 6, 'Kami sedang ada promo nih'),
(2, 281, 'Kami sedang ada promo nih'),
(3, 282, 'Kami sedang ada promo nih'),
(4, 283, 'Test ');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `level_user` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_contact`
--
ALTER TABLE `tb_contact`
  ADD PRIMARY KEY (`id_contact`);

--
-- Indexes for table `tb_messages`
--
ALTER TABLE `tb_messages`
  ADD PRIMARY KEY (`id_message`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_contact`
--
ALTER TABLE `tb_contact`
  MODIFY `id_contact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `tb_messages`
--
ALTER TABLE `tb_messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;