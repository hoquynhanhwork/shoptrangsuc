-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 11:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoptrangsuc`
--

-- --------------------------------------------------------

--
-- Table structure for table `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `MaChiTiet` int(11) NOT NULL,
  `MaDonHang` int(11) DEFAULT NULL,
  `id_size` int(11) DEFAULT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(10,2) NOT NULL,
  `ThanhTien` decimal(10,2) GENERATED ALWAYS AS (`SoLuong` * `DonGia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`MaChiTiet`, `MaDonHang`, `id_size`, `SoLuong`, `DonGia`) VALUES
(22, 19, 206, 1, 5130000.00),
(23, 20, 255, 1, 720000.00);

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

CREATE TABLE `danhgia` (
  `MaDanhGia` int(11) NOT NULL,
  `MaSanPham` int(11) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  `SoSao` int(11) DEFAULT NULL CHECK (`SoSao` between 1 and 5),
  `NoiDung` text DEFAULT NULL,
  `NgayDanhGia` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`MaDanhGia`, `MaSanPham`, `idUser`, `SoSao`, `NoiDung`, `NgayDanhGia`) VALUES
(1, 12, 6, 5, 'hhuhu', '2026-04-17 12:56:48'),
(10, 10, 5, 5, 'hi', '2026-04-18 02:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc`
--

CREATE TABLE `danhmuc` (
  `MaDanhMuc` int(11) NOT NULL,
  `TenDanhMuc` varchar(255) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `TrangThai` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `danhmuc`
--

INSERT INTO `danhmuc` (`MaDanhMuc`, `TenDanhMuc`, `MoTa`, `TrangThai`) VALUES
(1, 'Nhẫn', '', 1),
(2, 'Vòng tay', '', 1),
(3, 'Dây chuyền', '', 1),
(4, 'Mặt dây chuyền', '', 1),
(5, 'Hoa tai', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

CREATE TABLE `donhang` (
  `MaDonHang` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `TenKhachHang` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `DiaChiGiaoHang` text DEFAULT NULL,
  `TongTien` decimal(10,2) DEFAULT NULL,
  `PhiShip` int(11) DEFAULT 0,
  `PhuongThucThanhToan` enum('cod','momo','bank') DEFAULT NULL,
  `MaTrangThai` int(11) DEFAULT NULL,
  `GhiChu` text DEFAULT NULL,
  `HinhAnhTT` varchar(255) DEFAULT NULL,
  `NgayDatHang` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`MaDonHang`, `idUser`, `TenKhachHang`, `Email`, `SoDienThoai`, `DiaChiGiaoHang`, `TongTien`, `PhiShip`, `PhuongThucThanhToan`, `MaTrangThai`, `GhiChu`, `HinhAnhTT`, `NgayDatHang`) VALUES
(19, 5, 'Sanh Nguyễn Văn', NULL, '22', 'Phường Ngọc Hà, Thành phố Hà Nội', 5155000.00, 25000, 'cod', 5, '', NULL, '2026-04-18 10:33:15'),
(20, 5, 'Sanh Nguyễn Văn', NULL, 's', 'Xã Tứ Mỹ, Tỉnh Hà Tĩnh', 755000.00, 35000, 'momo', 1, '', NULL, '2026-04-18 10:39:17');

-- --------------------------------------------------------

--
-- Table structure for table `giohang`
--

CREATE TABLE `giohang` (
  `MaGioHang` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idSize` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `NgayThem` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giohang`
--

INSERT INTO `giohang` (`MaGioHang`, `idUser`, `idSize`, `SoLuong`, `NgayThem`) VALUES
(42, 5, 262, 2, '2026-04-18 10:44:55'),
(43, 5, 261, 1, '2026-04-18 10:45:01'),
(45, 5, 276, 1, '2026-04-18 10:47:35'),
(46, 5, 234, 2, '2026-04-18 10:48:57'),
(48, 5, 436, 1, '2026-04-18 10:52:19'),
(49, 6, 262, 1, '2026-04-18 10:57:54'),
(50, 6, 269, 1, '2026-04-18 10:58:07'),
(51, 6, 337, 1, '2026-04-18 11:10:32'),
(52, 4, 319, 1, '2026-04-19 08:36:00'),
(53, 4, 276, 1, '2026-04-19 08:36:09'),
(54, 2, 489, 1, '2026-04-19 08:40:25'),
(55, 2, 506, 2, '2026-04-19 08:40:31'),
(57, 2, 436, 1, '2026-04-19 08:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `lienhe`
--

CREATE TABLE `lienhe` (
  `MaLienHe` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `ChuDe` varchar(255) NOT NULL,
  `NoiDung` text NOT NULL,
  `TrangThai` enum('new','read','replied') DEFAULT 'new',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `idUser` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `DiaChi` text DEFAULT NULL,
  `VaiTro` enum('admin','khachhang') DEFAULT 'khachhang',
  `id_google` varchar(100) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `TrangThai` tinyint(1) DEFAULT 1,
  `DelAt` tinyint(1) DEFAULT 0,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`idUser`, `HoTen`, `TenDangNhap`, `MatKhau`, `Email`, `SoDienThoai`, `DiaChi`, `VaiTro`, `id_google`, `remember_token`, `TrangThai`, `DelAt`, `NgayTao`) VALUES
(1, 'Admin', 'admin12345', '$2y$10$Cu6ENc5qOpIGWPqICcMVLOhUF3b1QBGSAZCq2sLpquP.wMq7hijsK', 'admin@gmail.com', '0900000001', NULL, 'admin', NULL, NULL, 1, 0, '2026-04-01 11:21:19'),
(2, 'Hồ Quỳnh Anh', 'hoquynhanh.work', NULL, 'hoquynhanh.work@gmail.com', NULL, NULL, 'khachhang', '100078097122449145909', NULL, 1, 0, '2026-04-12 13:12:44'),
(3, 'Nguyệt', 'nguyet123', '$2y$10$VOYLPUCnB6JY70gsAzBt8.p4rFRA4hPa98UBALdF7GB2gT4dE9Haa', 'hoqunhanh160605@gmail.com', '0356552786', '', 'khachhang', NULL, NULL, 1, 0, '2026-04-15 10:50:36'),
(4, 'Anh Hồ Quỳnh', 'anhhq3639', NULL, 'anhhq3639@ut.edu.vn', NULL, NULL, 'khachhang', '111010437241329881539', NULL, 1, 0, '2026-04-15 11:03:08'),
(5, 'Sanh Nguyễn Văn', 'sanhnv6546', NULL, 'sanhnv6546@ut.edu.vn', NULL, NULL, 'khachhang', '117979529964244498321', NULL, 1, 0, '2026-04-17 12:34:44'),
(6, 'Sanh Nguyễn', 'sanhn8033', '$2y$10$08TBSnPDWT5pUEe9pyY6zOau4eLO.6QN2zuSSk0zieFClzeu3u8UK', 'sanhn8033@gmail.com', '113', 'Xã Tuy Phước Tây, Tỉnh Gia Lai', 'khachhang', '113171396394855381180', NULL, 1, 0, '2026-04-17 12:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE `sanpham` (
  `MaSanPham` int(11) NOT NULL,
  `MaDanhMuc` int(11) DEFAULT NULL,
  `TenSanPham` varchar(255) NOT NULL,
  `DuongDan` varchar(255) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `ChiTietSanPham` text DEFAULT NULL,
  `NoiBat` tinyint(1) DEFAULT 0,
  `LuotXem` int(11) DEFAULT 0,
  `LuotMua` int(11) DEFAULT 0,
  `TrangThai` tinyint(1) DEFAULT 1,
  `DelAt` tinyint(1) NOT NULL DEFAULT 0,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `Gia` decimal(10,2) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`MaSanPham`, `MaDanhMuc`, `TenSanPham`, `DuongDan`, `MoTa`, `ChiTietSanPham`, `NoiBat`, `LuotXem`, `LuotMua`, `TrangThai`, `DelAt`, `HinhAnh`, `Gia`, `NgayTao`) VALUES
(1, 1, 'Nhẫn Bạc Đính Đá Sang Trọng Nữ Tính', 'nhan-bac-dinh-da-nu', 'Thiết kế nhẫn bạc nổi bật với viên đá lấp lánh, tạo cảm giác sang trọng và thu hút khi đeo.', 'Dòng sản phẩm: Trang sức bạc cao cấp\r\nChất liệu: Bạc 925\r\nTrọng lượng: 3.443 phân\r\n\r\nNhẫn được chế tác từ bạc 925 cao cấp, bề mặt được đánh bóng kỹ lưỡng giúp giữ độ sáng lâu dài. Viên đá trung tâm được cắt gọt tinh xảo, phản chiếu ánh sáng tốt, tạo hiệu ứng lấp lánh nổi bật.\r\n\r\nThiết kế mang phong cách hiện đại, phù hợp với các bạn nữ yêu thích sự sang trọng nhưng không quá cầu kỳ. Có thể sử dụng khi đi làm, đi chơi hoặc dự tiệc.\r\n\r\nSản phẩm không chỉ mang giá trị thẩm mỹ mà còn giúp tôn lên vẻ đẹp thanh lịch và tự tin của người đeo.', 1, 0, 0, 1, 0, 'nhan1.png', 855000.00, '2026-04-17 12:41:55'),
(2, 1, 'Nhẫn Vàng 14K Đính CZ Nữ Tính', 'nhan-vang-14k-dinh-da-cz', 'Nhẫn vàng 14K với viên CZ sáng rực, giúp tôn lên vẻ đẹp sang trọng và quý phái.', 'Dòng sản phẩm: Trang sức vàng CZ\nChất liệu: Vàng 14K\nTrọng lượng: 2.95331 phân\n\nNhẫn được chế tác từ vàng 14K đạt chuẩn, đảm bảo độ bền cao và giữ màu tốt theo thời gian. Viên đá CZ trung tâm được gắn chắc chắn, tạo độ sáng lấp lánh nổi bật.\n\nThiết kế mang phong cách sang trọng, phù hợp với các dịp quan trọng như tiệc, sự kiện hoặc làm quà tặng. Bề mặt được xử lý bóng giúp tăng khả năng phản chiếu ánh sáng.\n\nĐây là lựa chọn lý tưởng cho những ai yêu thích sự quý phái nhưng vẫn tinh tế.', 1, 0, 0, 1, 0, 'nhan2.png', 5130000.00, '2026-04-17 12:41:55'),
(3, 1, 'Nhẫn Vàng Trắng 10K Đính ECZ Hiện Đại', 'nhan-vang-trang-10k-dinh-da-ecz', 'Nhẫn vàng trắng 10K mang thiết kế hiện đại, viên ECZ tạo hiệu ứng ánh sáng nổi bật.', 'Dòng sản phẩm: Trang sức ECZ\nChất liệu: Vàng trắng 10K\nTrọng lượng: 5.00359 phân\n\nSản phẩm sử dụng vàng trắng 10K mang vẻ đẹp hiện đại, dễ phối với nhiều phong cách trang phục. Viên ECZ có độ trong suốt cao, tạo hiệu ứng lấp lánh khi có ánh sáng.\n\nThiết kế hướng đến sự trẻ trung, phù hợp với phụ nữ hiện đại. Có thể sử dụng hằng ngày hoặc trong các buổi tiệc nhẹ.\n\nĐây là mẫu trang sức cân bằng giữa tính thẩm mỹ và sự tiện dụng.', 1, 0, 0, 1, 0, 'nhan3.png', 5980000.00, '2026-04-17 12:41:55'),
(4, 1, 'Nhẫn Bạc 930 Tối Giản Basic', 'nhan-bac-930-khong-da', 'Thiết kế trơn không đá, mang lại cảm giác nhẹ nhàng và dễ sử dụng hằng ngày.', 'Dòng sản phẩm: Trang sức bạc basic\nChất liệu: Bạc 930\n\nNhẫn bạc 930 được thiết kế theo phong cách tối giản, không đính đá, mang lại cảm giác nhẹ nhàng khi đeo. Bề mặt được xử lý mịn giúp hạn chế trầy xước.\n\nSản phẩm phù hợp với người thích phong cách đơn giản, dễ phối đồ và sử dụng hằng ngày.\n\nĐây là mẫu nhẫn cơ bản nhưng vẫn giữ được sự tinh tế.', 0, 0, 0, 1, 0, 'nhan4.png', 555000.00, '2026-04-17 12:41:55'),
(5, 1, 'Nhẫn Bạc Hình Trái Tim Dễ Thương', 'nhan-bac-hinh-trai-tim', 'Nhẫn bạc thiết kế trái tim nhỏ gọn, mang phong cách đáng yêu và nữ tính.', 'Dòng sản phẩm: Trang sức bạc nữ\nChất liệu: Bạc 925\n\nNhẫn được thiết kế hình trái tim tượng trưng cho tình yêu và sự ngọt ngào. Chất liệu bạc 925 giúp sản phẩm sáng đẹp và an toàn cho da.\n\nThiết kế trẻ trung, phù hợp làm quà tặng cho người thân hoặc người yêu.\n\nCó thể sử dụng hằng ngày như một phụ kiện thời trang dễ thương.', 0, 0, 0, 1, 0, 'nhan5.png', 650000.00, '2026-04-17 12:41:55'),
(6, 1, 'Nhẫn Vàng Ý 18K Tối Giản', 'nhan-vang-y-18k', 'Nhẫn vàng Ý 18K thiết kế trơn sang trọng, phù hợp phong cách thanh lịch.', 'Dòng sản phẩm: Trang sức vàng Ý\nChất liệu: Vàng 18K\nTrọng lượng: 1.94915 phân\n\nNhẫn vàng Ý 18K mang phong cách tối giản nhưng vẫn sang trọng. Chất liệu vàng 18K giúp giữ màu tốt và bền theo thời gian.\n\nThiết kế trơn giúp dễ phối đồ, phù hợp môi trường công sở hoặc sự kiện nhẹ.\n\nSản phẩm mang lại sự tinh tế và thanh lịch cho người đeo.', 0, 0, 0, 1, 0, 'nhan6.png', 2450000.00, '2026-04-17 12:41:55'),
(7, 1, 'Nhẫn Bạc Đính Ngọc Synthetic', 'nhan-bac-dinh-ngoc', 'Viên ngọc synthetic tạo điểm nhấn mềm mại, giúp sản phẩm nổi bật hơn.', 'Dòng sản phẩm: Trang sức bạc ngọc\nChất liệu: Bạc 925\n\nNhẫn được thiết kế với viên ngọc synthetic ở trung tâm, tạo điểm nhấn nhẹ nhàng. Bạc 925 giúp giữ độ sáng lâu dài.\n\nThiết kế phù hợp phong cách nữ tính, thanh lịch và nhẹ nhàng.\n\nCó thể sử dụng trong nhiều hoàn cảnh khác nhau.', 0, 0, 0, 1, 0, 'nhan7.png', 590000.00, '2026-04-17 12:41:55'),
(8, 1, 'Nhẫn Ngọc Trai Vàng Trắng 10K', 'nhan-vang-trang-ngoc-trai-freshwater', 'Ngọc trai nước ngọt kết hợp vàng trắng tạo nên vẻ đẹp tinh tế.', 'Dòng sản phẩm: Trang sức ngọc trai\nChất liệu: Vàng trắng 10K\nTrọng lượng: 3.19206 phân\n\nNgọc trai freshwater mang vẻ đẹp tự nhiên, kết hợp vàng trắng tạo sự sang trọng. Bề mặt ngọc trai bóng đẹp và tinh tế.\n\nThiết kế phù hợp các dịp quan trọng như tiệc hoặc sự kiện.\n\nMang lại vẻ đẹp quý phái và nhẹ nhàng.', 1, 0, 0, 1, 0, 'nhan8.png', 5650000.00, '2026-04-17 12:41:55'),
(9, 1, 'Nhẫn Disney Đính Đá CZ', 'nhan-bac-disney-the-jewel-of-my-heart', 'Phong cách Disney trẻ trung với đá CZ lấp lánh nổi bật.', 'Dòng sản phẩm: Trang sức Disney bạc\nChất liệu: Bạc 930\n\nNhẫn lấy cảm hứng từ phong cách Disney, mang lại cảm giác trẻ trung. Đá CZ giúp tăng độ sáng và thu hút ánh nhìn.\n\nPhù hợp với giới trẻ yêu thích phong cách dễ thương và năng động.\n\nCó thể sử dụng hằng ngày hoặc làm quà tặng.', 1, 0, 0, 1, 0, 'nhan9.png', 720000.00, '2026-04-17 12:41:55'),
(10, 1, 'Nhẫn Bông Hoa CZ Nữ Tính', 'nhan-bac-hinh-bong-hoa', 'Thiết kế hoa tinh xảo với đá CZ giúp tăng vẻ nữ tính.', 'Dòng sản phẩm: Trang sức bạc hoa\nChất liệu: Bạc 925\n\nNhẫn thiết kế hình bông hoa với chi tiết tinh xảo, tạo sự mềm mại. Đá CZ giúp tăng độ lấp lánh.\n\nPhù hợp nhiều độ tuổi và nhiều phong cách khác nhau.\n\nDễ dàng sử dụng trong cuộc sống hằng ngày.', 0, 0, 0, 1, 0, 'nhan10.png', 690000.00, '2026-04-17 12:41:55'),
(11, 1, 'Nhẫn Nơ CZ Dễ Thương', 'nhan-bac-hinh-chiec-no', 'Thiết kế hình nơ tạo cảm giác dễ thương và trẻ trung.', 'Dòng sản phẩm: Trang sức bạc nơ\nChất liệu: Bạc 925\n\nNhẫn hình chiếc nơ mang phong cách dễ thương, phù hợp giới trẻ. Bạc 925 giúp giữ độ sáng lâu.\n\nThiết kế độc đáo, dễ gây ấn tượng khi đeo.\n\nCó thể dùng làm quà tặng ý nghĩa.', 0, 0, 0, 1, 0, 'nhan11.png', 660000.00, '2026-04-17 12:41:55'),
(12, 1, 'Nhẫn Bạc Bông Hoa Synthetic', 'nhan-bac-hinh-bong-hoa-synthetic', 'Hoa cách điệu kết hợp đá synthetic tạo sự tinh tế nhẹ nhàng.', 'Dòng sản phẩm: Trang sức bạc hoa synthetic\nChất liệu: Bạc 925\n\nNhẫn thiết kế hoa cách điệu kết hợp đá synthetic tạo sự tinh tế. Bạc 925 giúp giữ độ sáng tốt.\n\nPhong cách nhẹ nhàng, thanh lịch và hiện đại.\n\nPhù hợp sử dụng hằng ngày hoặc làm quà tặng.', 0, 0, 0, 1, 0, 'nhan12.png', 2370000.00, '2026-04-17 12:41:55'),
(13, 3, 'Dây chuyền bạc đính đá trái tim lấp lánh', 'day-chuyen-bac-trai-tim-lap-lanh', 'Lãng mạn. Thiết kế hình trái tim biểu tượng của tình yêu, đính đá CZ rực rỡ.', 'Dòng sản phẩm: Trang sức bạc tình yêu\nChất liệu: Bạc cao cấp 925\n\nMẫu dây chuyền gây ấn tượng mạnh với mặt dây hình trái tim được đính kết những viên đá nhỏ li ti, tạo độ phản chiếu ánh sáng tối đa theo từng góc độ. Sản phẩm mang phong cách lãng mạn, ngọt ngào, là món quà lý tưởng để dành tặng cho những người thân yêu hoặc tự thưởng cho bản thân để tăng thêm phần nữ tính.\n\nSợi dây chuyền được thiết kế mảnh mai nhưng chắc chắn, bề mặt bạc được xử lý đánh bóng gương tạo độ sáng vượt trội. Đây là phụ kiện không thể thiếu giúp tôn lên vẻ đẹp dịu dàng và tình yêu nồng cháy của phái đẹp.', 1, 0, 0, 1, 0, 'dayChuyen_1.jpg', 650000.00, '2026-04-18 01:33:59'),
(14, 3, 'Dây chuyền bạc mặt đá tròn cổ điển', 'day-chuyen-bac-mat-da-tron-co-dien', 'Quý phái. Viên đá chủ lớn được bao quanh bởi vòng đá nhỏ, tạo vẻ đẹp vượt thời gian.', 'Dòng sản phẩm: Trang sức bạc cổ điển\nChất liệu: Bạc 925, Đá quý nhân tạo cao cấp\n\nThiết kế mang đậm phong cách hoàng gia với viên đá chủ tròn sáng bóng ở trung tâm. Sự tỉ mỉ trong kỹ thuật đính đá bao quanh giúp mặt dây chuyền trông như một đóa hoa tuyết đang rạng rỡ tỏa sáng. Phù hợp cho những quý cô yêu thích vẻ đẹp cổ điển, sang trọng và muốn tạo điểm nhấn trong các bữa tiệc tối.\n\nSản phẩm sử dụng công nghệ xi mạ tiên tiến giúp hạn chế tối đa tình trạng xỉn màu, giữ cho món trang sức luôn như mới sau thời gian dài sử dụng. Một sự lựa chọn hoàn hảo cho phong cách thanh lịch vượt thời gian.', 1, 0, 0, 1, 0, 'dayChuyen_2.jpg', 720000.00, '2026-04-18 01:33:59'),
(15, 3, 'Dây chuyền bạc cách điệu đính đá tinh xảo', 'day-chuyen-bac-cach-dieu-tinh-xao', 'Hiện đại. Đường nét uốn lượn mềm mại kết hợp cùng đá sáng, mang phong cách trẻ trung.', 'Dòng sản phẩm: Trang sức bạc thời trang\nChất liệu: Bạc 925 tinh khiết\n\nSản phẩm sở hữu mặt dây được thiết kế với những đường cong mềm mại uốn lượn đầy nghệ thuật, điểm xuyết các viên đá nhỏ dọc theo khung bạc tạo nên sự chuyển tiếp ánh sáng mượt mà. Đây là mẫu trang sức mang hơi thở hiện đại, dễ dàng phối hợp với áo sơ mi hoặc váy cổ chữ V, giúp tôn lên vẻ thanh thoát và tinh tế cho người sử dụng.\n\nThiết kế hướng đến sự năng động nhưng không kém phần quý phái, là điểm nhấn cá tính cho những cô nàng công sở hiện đại.', 0, 0, 0, 1, 0, 'dayChuyen3.jpg', 580000.00, '2026-04-18 01:33:59'),
(16, 3, 'Dây chuyền bạc cao cấp đính đá Emerald', 'day-chuyen-bac-dinh-da-emerald', 'Nổi bật. Điểm nhấn là viên đá màu sắc nổi bật mang lại sự may mắn và đẳng cấp.', 'Dòng sản phẩm: Trang sức bạc đá màu\nChất liệu: Bạc cao cấp, Đá màu chất lượng cao\n\nSự kết hợp độc đáo giữa sắc bạc trắng tinh khôi và viên đá trung tâm mang tone màu xanh Emerald huyền bí, tượng trưng cho sự tái sinh và may mắn. Thiết kế tối giản tập trung hoàn toàn vào độ rực rỡ và chiều sâu của viên đá chính, giúp chủ nhân thể hiện cá tính riêng biệt và đẳng cấp thẩm mỹ khác biệt.\n\nMặt dây chuyền được cố định bằng các chấu bạc chắc chắn, đảm bảo viên đá luôn an toàn và tỏa sáng rực rỡ nhất khi có ánh sáng chiếu vào.', 1, 0, 0, 1, 0, 'dayChuyen4.jpg', 850000.00, '2026-04-18 01:33:59'),
(17, 3, 'Dây chuyền bạc mặt hình học tối giản', 'day-chuyen-bac-mat-hinh-hoc-toi-gian', 'Tối giản. Thiết kế thanh mảnh với mặt dây khối hình học hiện đại, phong cách Basic.', 'Dòng sản phẩm: Trang sức Minimalist\nChất liệu: Bạc 925 tiêu chuẩn quốc tế\n\nDành cho những ai theo đuổi triết lý \"Less is More\", mẫu dây chuyền này sử dụng các khối hình học cơ bản được xử lý bề mặt nhẵn thín và sáng bóng. Sản phẩm mang lại cảm giác nhẹ nhàng, trẻ trung và cực kỳ dễ ứng dụng trong các bộ trang phục hằng ngày từ đi học đến đi làm.\n\nSự đơn giản trong thiết kế chính là điểm mạnh giúp món trang sức này không bao giờ lỗi mốt, dễ dàng kết hợp layer cùng các sợi dây chuyền khác để tạo nên phong cách riêng.', 0, 0, 0, 1, 0, 'dayChuyen5.jpg', 420000.00, '2026-04-18 01:33:59'),
(18, 3, 'Dây chuyền bạc đính đá dọc thanh lịch', 'day-chuyen-bac-dinh-da-doc-thanh-lich', 'Duyên dáng. Kiểu dáng thanh mảnh giúp kéo dài vùng cổ, tạo sự thon gọn và quyến rũ.', 'Dòng sản phẩm: Trang sức bạc thanh lịch\nChất liệu: Bạc cao cấp\n\nĐiểm đặc biệt của sản phẩm là các viên đá nhỏ được đính theo trục dọc thanh mảnh, tạo hiệu ứng thị giác giúp vùng cổ trông thon gọn và duyên dáng hơn. Sợi dây mảnh dẻ nhưng chắc chắn, phù hợp với phong cách trang nhã, giúp bạn tự tin tỏa sáng một cách lôi cuốn nhưng vẫn giữ được sự kín đáo.\n\nSản phẩm là lựa chọn tuyệt vời cho những buổi hẹn hò lãng mạn hoặc dạo phố cuối tuần, mang lại vẻ đẹp thanh khiết và quyến rũ.', 0, 0, 0, 1, 0, 'dayChuyen6.jpg', 590000.00, '2026-04-18 01:33:59'),
(19, 3, 'Dây chuyền bạc cỏ bốn lá may mắn', 'day-chuyen-bac-co-bon-la-may-man', 'May mắn. Biểu tượng cỏ bốn lá đính đá xanh, mang lại niềm tin và hy vọng.', 'Dòng sản phẩm: Trang sức may mắn\nChất liệu: Bạc 925, Đá màu xanh lục\n\nLấy ý tưởng từ loài cỏ mang lại sự may mắn hiếm có, mặt dây chuyền được chế tác với bốn cánh cân đối, mỗi cánh đều được nạm đá tinh xảo tạo độ sâu cho thiết kế. Màu xanh của đá không chỉ làm nổi bật làn da mà còn tạo cảm giác tươi mới, tràn đầy năng lượng tích cực cho người sở hữu.\n\nMỗi cánh cỏ đại diện cho một thông điệp: Niềm tin, Hy vọng, Tình yêu và Sự may mắn. Đây là món quà hoàn hảo thay cho những lời chúc tốt đẹp nhất dành cho người phụ nữ quan trọng.', 1, 0, 0, 1, 0, 'dayChuyen7.jpg', 620000.00, '2026-04-18 01:33:59'),
(20, 3, 'Dây chuyền bạc mặt hoa tuyết rực rỡ', 'day-chuyen-bac-hinh-hoa-tuyet', 'Tinh khôi. Thiết kế hoa tuyết sáu cánh đính đá lấp lánh, tôn vinh vẻ đẹp thuần khiết.', 'Dòng sản phẩm: Trang sức bạc cao cấp\nChất liệu: Bạc 925, Đá CZ cao cấp\n\nMặt dây chuyền mô phỏng hình ảnh bông tuyết với các đường nét đối xứng hoàn hảo, gợi lên sự thuần khiết và mỏng manh. Mỗi cánh hoa đều được đính đá lấp lánh như những tinh thể băng thật sự đang tỏa sáng rực rỡ dưới ánh mặt trời mùa đông. Sản phẩm mang lại vẻ đẹp trong trẻo, tinh khôi và cực kỳ thu hút ánh nhìn.\n\nĐây là mẫu thiết kế được ưa chuộng nhất trong các mùa lễ hội, giúp phái đẹp rạng rỡ như một nàng công chúa tuyết trong các buổi tiệc cuối năm.', 1, 0, 0, 1, 0, 'dayChuyen8.jpg', 780000.00, '2026-04-18 01:33:59'),
(21, 3, 'Dây chuyền bạc mặt giọt nước đính đá', 'day-chuyen-bac-hinh-giot-nuoc', 'Thanh thoát. Hình dáng giọt nước mềm mại, đính viên đá chủ lớn sang trọng.', 'Dòng sản phẩm: Trang sức bạc sang trọng\nChất liệu: Bạc cao cấp 925\n\nThiết kế giọt nước luôn là biểu tượng của sự nữ tính, thanh thoát và dịu dàng. Với viên đá chủ lớn được cắt gọt tinh xảo với nhiều mặt cắt phản chiếu đặt chính giữa, sản phẩm tạo nên một vẻ đẹp hài hòa, nhẹ nhàng nhưng vẫn đủ đẳng cấp để bạn tự tin xuất hiện tại các buổi tiệc quan trọng.\n\nĐường nét chế tác mượt mà bao bọc lấy viên đá tạo cảm giác êm ái khi tiếp xúc với da, đồng thời tôn vinh vẻ đẹp quý phái, sành điệu của người phụ nữ hiện đại.', 0, 0, 0, 1, 0, 'dayChuyen9.jpg', 690000.00, '2026-04-18 01:33:59'),
(22, 3, 'Dây chuyền bạc mặt thiên nga quý phái', 'day-chuyen-bac-hinh-thien-nga', 'Kiêu kỳ. Hình tượng chim thiên nga đính đá đen-trắng đối lập tạo nét cá tính.', 'Dòng sản phẩm: Trang sức bạc thời thượng\nChất liệu: Bạc 925, Đá đen & trắng\n\nThiết kế lấy cảm hứng từ vẻ đẹp kiêu sa và sự trung thủy của chim thiên nga. Sự kết hợp tài tình giữa những viên đá đen huyền bí nạm trên thân và đá trắng tinh khiết tạo nên một tổng thể đối lập đầy cuốn hút và bí ẩn. Đây là món phụ kiện dành cho những cô nàng yêu thích sự độc đáo và mong muốn khẳng định phong cách riêng.\n\nHình ảnh thiên nga không chỉ mang giá trị thẩm mỹ cao mà còn là biểu tượng của vẻ đẹp tâm hồn và sự thanh cao, giúp bạn nổi bật trong mọi không gian.', 1, 0, 0, 1, 0, 'dayChuyen10.jpg', 820000.00, '2026-04-18 01:33:59'),
(23, 3, 'Dây chuyền bạc mặt ngôi sao nhỏ xinh', 'day-chuyen-bac-hinh-ngoi-sao', 'Trẻ trung. Ngôi sao năm cánh đính đá nhỏ lấp lánh, mang vẻ đẹp năng động.', 'Dòng sản phẩm: Trang sức bạc trẻ trung\nChất liệu: Bạc 925 chuẩn\n\nMẫu dây chuyền hướng tới sự trẻ trung và tràn đầy sức sống với mặt dây hình ngôi sao nhỏ gọn được cách điệu tinh tế. Chất liệu bạc cao cấp được đánh bóng kỹ lưỡng kết hợp với các viên đá bắt sáng li ti giúp bạn luôn nổi bật một cách nhẹ nhàng trong mọi hoạt động hằng ngày.\n\nThiết kế nhỏ nhắn phù hợp để phối cùng các loại trang phục từ năng động đến nữ tính, mang lại cảm giác vui tươi và hy vọng như những vì sao lấp lánh trên bầu trời đêm.', 0, 0, 0, 1, 0, 'dayChuyen11.jpg', 450000.00, '2026-04-18 01:33:59'),
(24, 3, 'Dây chuyền bạc mặt nguyệt quế đính đá', 'day-chuyen-bac-hinh-nguyet-que', 'Vinh quang. Vòng nguyệt quế cách điệu mang ý nghĩa của sự thành công và rạng rỡ.', 'Dòng sản phẩm: Trang sức bạc ý nghĩa\nChất liệu: Bạc cao cấp 925\n\nThiết kế vòng nguyệt quế uốn lượn một cách tinh tế bao quanh những viên đá nhỏ lấp lánh, tượng trưng cho chiến thắng và sự vinh hiển. Sản phẩm không chỉ đơn thuần là phụ kiện thời trang mà còn mang ý nghĩa sâu sắc về sự nỗ lực và thành quả rạng rỡ. Rất thích hợp làm quà tặng trong những dịp thăng tiến hoặc tốt nghiệp.\n\nTừng chi tiết lá nhỏ được chạm khắc tỉ mỉ, kết hợp cùng sợi dây chuyền thanh mảnh tạo nên tổng thể vừa quyền lực vừa mềm mại, giúp tôn vinh trí tuệ và vẻ đẹp của phái nữ.', 0, 0, 0, 1, 0, 'dayChuyen12.jpg', 750000.00, '2026-04-18 01:33:59'),
(25, 2, 'Lắc Tay Bạc S925 Hoa Mẫu Đơn & Ngọc Trai Quý Phái', 'lac-tay-hoa-mau-don-ngoc-trai', 'Sự kết hợp hoàn hảo giữa nét chạm khắc hoa mẫu đơn cổ điển và viên ngọc trai trắng mịn, mang lại vẻ đẹp vương giả.', 'Chất liệu: Bạc cao cấp S925 sáng bóng; Đá chính: Ngọc trai nhân tạo; Thiết kế: Hoa mẫu đơn 3 lớp cánh; Khóa: Lobster clasp kèm dây xích mở rộng 3cm.', 1, 0, 0, 1, 0, 'vt1.jpg', 350000.00, '2026-04-17 17:00:00'),
(26, 2, 'Lắc Tay Silver Heart & Sparkle Charm Minimalist', 'lac-tay-tim-doi-dinh-da', 'Vẻ đẹp tinh tế từ sự tối giản, điểm xuyết bởi charm trái tim đôi và các viên đá Zirconia bắt sáng rực rỡ.', 'Chất liệu: Bạc Ý S925; Đá: Cubic Zirconia (CZ) cao cấp; Kiểu dây: O-chain thanh mảnh; Charm: Trái tim nạm đá và trái tim trơn lồng ghép.', 1, 0, 0, 1, 0, 'vt2.jpg', 280000.00, '2026-04-17 17:00:00'),
(27, 2, 'Lắc Tay Paperclip Chain Mạ Vàng 18K Mix Pearl', 'lac-tay-xich-paperclip-ma-vang', 'Đón đầu xu hướng với thiết kế kẹp giấy (paperclip) cá tính, phối cùng ngọc trai nước ngọt sang trọng.', 'Chất liệu: Cốt bạc S925 mạ vàng 18K; Đá: Ngọc trai nước ngọt tự nhiên; Điểm nhấn: Tag chữ Oval khắc S925; Kiểu dáng: Hiện đại, Unisex.', 1, 0, 0, 1, 0, 'vt3.jpg', 420000.00, '2026-04-17 17:00:00'),
(28, 2, 'Kiềng Tay Bangle Silver \"Time Is Not Old\" Edition', 'kieng-tay-khac-chu-time-is-not-old', 'Món quà kỷ niệm ý nghĩa với thông điệp về thời gian và sự gắn kết được khắc laser tinh xảo trên nền bạc gương.', 'Chất liệu: Bạc S925 đánh bóng gương; Họa tiết: Khắc chìm \"Time is not old\"; Đá: Zirconia trắng đính chìm; Loại vòng: Kiềng cứng (Bangle).', 1, 0, 0, 1, 0, 'vt4.jpg', 550000.00, '2026-04-17 17:00:00'),
(29, 2, 'Lắc Tay Infinity Rose Gold - Biểu Tượng Vô Cực', 'lac-tay-vo-cuc-infinity-dinh-da', 'Biểu tượng của tình yêu bất diệt với thiết kế vô cực mềm mại, nạm đá lấp lánh trên tông màu vàng hồng thời thượng.', 'Chất liệu: Bạc S925 mạ vàng hồng (Rose Gold); Đá: Kim cương nhân tạo CZ; Kiểu dây: Cable chain siêu mảnh 0.8mm; Ý nghĩa: Tình yêu vĩnh cửu.', 1, 0, 0, 1, 0, 'vt5.jpg', 320000.00, '2026-04-17 17:00:00'),
(30, 2, 'Lắc Tay Heart of Ocean Blue - Trái Tim Đại Dương', 'lac-tay-trai-tim-dai-duong-xanh', 'Mang cả bầu trời xanh mát lên cổ tay bạn với viên đá chủ Blue Topaz rực rỡ nằm trong trái tim bạc thuần khiết.', 'Chất liệu: Bạc S925 mạ Rhodium; Đá chính: Zirconia Blue Topaz 5mm; Dây: Thiết kế đốt bạc hình ống trụ; Phong cách: Sang trọng, nổi bật.', 1, 0, 0, 1, 0, 'vt6.jpg', 380000.00, '2026-04-17 17:00:00'),
(31, 2, 'Lắc Tay Double Layer Silver Beads - Dây Kép Hạt Bi', 'lac-tay-day-kep-hat-bi-bac', 'Thiết kế layer hai tầng thanh thoát, tạo hiệu ứng chuyển động sống động với các hạt bi bạc di động.', 'Chất liệu: Bạc S925 cao cấp; Thiết kế: Dây kép (Double line); Phụ kiện: Hạt bi bạc trơn 3mm; Ưu điểm: Không vướng víu, dễ phối đồng hồ.', 1, 0, 0, 1, 0, 'vt7.jpg', 250000.00, '2026-04-17 17:00:00'),
(32, 2, 'Lắc Tay Slider Box Chain Ngọc Bích Thanh Khiết', 'lac-tay-day-rut-da-cam-thach', 'Sự giao thoa tuyệt vời giữa ngọc bích phương Đông và thiết kế dây rút slider hiện đại, mang lại may mắn.', 'Chất liệu: Hợp kim mạ vàng 24K; Đá: Ngọc Bích/Cẩm Thạch tự nhiên; Khóa: Smart Slider (tùy chỉnh mọi cỡ tay); Dây: Box chain vuông.', 1, 0, 0, 1, 0, 'vt8.jpg', 450000.00, '2026-04-17 17:00:00'),
(33, 2, 'Lắc Tay Silver Snowflake - Bông Tuyết Mùa Đông', 'lac-tay-bong-tuyet-snowflake', 'Lấy cảm hứng từ sự tinh khôi của những bông tuyết đầu mùa, mang lại nét duyên dáng, lãng mạn cho phái đẹp.', 'Chất liệu: Bạc chuẩn S925; Họa tiết: Bông tuyết cắt CNC 3D; Khóa: Móc chữ S truyền thống; Phù hợp: Làm quà tặng Noel, sinh nhật.', 1, 0, 0, 1, 0, 'vt9.jpg', 290000.00, '2026-04-17 17:00:00'),
(34, 2, 'Vòng Tay Đá Mắt Mèo & Xà Cừ Aura Healing', 'vong-tay-da-mat-meo-trang', 'Dòng đá phong thủy giúp cân bằng năng lượng, sở hữu ánh kim huyền ảo độc đáo từ đá Mắt Mèo trắng.', 'Chất liệu: Đá Mắt Mèo trắng 8mm, đá Labradorite; Charm: Bạc S925 tròn; Dây: Chun tàng hình co giãn 4 chiều; Ý nghĩa: Bình an, minh mẫn.', 1, 0, 0, 1, 0, 'vt10.jpg', 220000.00, '2026-04-17 17:00:00'),
(35, 2, 'Vòng Ngọc Trai Baroque \"Good Luck\" Gold Charm', 'vong-tay-ngoc-trai-good-luck', 'Sự phá cách đầy cá tính khi kết hợp ngọc trai baroque tự nhiên với charm thẻ bài khắc thông điệp may mắn.', 'Chất liệu: Ngọc trai nước ngọt thật; Charm: Thẻ bài \"Good Luck\" & Trái tim mạ vàng; Thiết kế: Mix dây bi kim loại; Phong cách: Urban Chic.', 1, 0, 0, 1, 0, 'vt11.jpg', 480000.00, '2026-04-17 17:00:00'),
(36, 2, 'Lắc Tay Double Chain Blue Topaz & Pearl Tinh Khôi', 'lac-tay-kep-topaz-va-ngoc-trai', 'Một thiết kế đẳng cấp kết hợp giữa đá quý màu xanh trong trẻo và ngọc trai dịu dàng trên nền dây kép.', 'Chất liệu: Bạc S925; Đá: Blue Topaz & Ngọc trai mini 4mm; Dây: Double Box chain; Phù hợp: Trang phục tiệc nhẹ, váy lụa.', 1, 0, 0, 1, 0, 'vt12.jpg', 360000.00, '2026-04-17 17:00:00'),
(37, 5, 'Khuyên tai bạc nút thắt', 'khuyen-tai-bac-nut-that', 'Ý nghĩa. Thiết kế nút thắt vô cực tượng trưng cho sự gắn kết bền chặt.', 'Dòng sản phẩm: Trang sức bạc Basic\r\nChất liệu: Bạc cao cấp 925\r\n\r\nMẫu khuyên tai với tạo hình nút thắt mềm mại, bề mặt bạc bóng loáng tạo hiệu ứng thị giác thú vị. Đây là món phụ kiện mang thông điệp về sự kết nối, rất phù hợp để làm quà tặng hoặc đeo hàng ngày như một biểu tượng của sự may mắn và gắn bó.', 1, 0, 0, 1, 0, '1.png', 180000.00, '2026-04-18 10:08:37'),
(38, 5, 'Khuyên tai bạc hoop nhỏ', 'khuyen-tai-bac-hoop-nho', 'Năng động. Kiểu dáng vòng tròn xoắn nhẹ, tạo vẻ ngoài trẻ trung, hiện đại.', 'Dòng sản phẩm: Khuyên tai vòng (Hoop)\nChất liệu: Bạc 925 tinh khiết\n\nThiết kế hoop nhỏ với các đường vân xoắn tinh tế giúp bắt sáng tốt hơn các loại vòng trơn thông thường. Sản phẩm ôm sát thùy tai, tạo cảm giác gọn gàng nhưng vẫn rất thời thượng, phù hợp với mọi kiểu khuôn mặt.', 1, 0, 0, 1, 0, '2.png', 165000.00, '2026-04-18 10:08:37'),
(39, 5, 'Khuyên tai bạc hình ống xếp tầng', 'khuyen-tai-bac-hinh-ong-xep-tang', 'Độc đáo. Họa tiết rãnh ngang tạo chiều sâu và phong cách mạnh mẽ.', 'Dòng sản phẩm: Trang sức bạc cá tính\r\nChất liệu: Bạc cao cấp 925\r\n\r\nLấy cảm hứng từ những kiến trúc hiện đại, mẫu khuyên tai này sử dụng các đường rãnh xếp tầng song song, mang lại cảm giác chắc chắn và phá cách. Một sự lựa chọn hoàn hảo cho những bạn trẻ yêu thích sự khác biệt.', 0, 0, 0, 1, 0, '3.png', 195000.00, '2026-04-18 10:08:37'),
(40, 5, 'Khuyên tai bạc thanh tạ trừu tượng', 'khuyen-tai-bac-thanh-ta-truu-tuong', 'Nghệ thuật. Đường nét uốn lượn tự do tạo nên vẻ đẹp lạ mắt và lôi cuốn.', 'Dòng sản phẩm: Trang sức Minimalist\r\nChất liệu: Bạc 925\r\n\r\nKhông đi theo những hình khối quy chuẩn, mẫu khuyên tai này sở hữu những đường cong tự nhiên như dòng nước chảy. Thiết kế mang hơi hướng nghệ thuật đương đại, giúp tôn vinh gu thẩm mỹ tinh tế của người sở hữu.', 0, 0, 0, 1, 0, '4.png', 175000.00, '2026-04-18 10:08:37'),
(41, 5, 'Khuyên tai bạc hình học', 'khuyen-tai-bac-hinh-hoc', 'Góc cạnh. Các mặt cắt đa diện giúp sản phẩm phản chiếu ánh sáng đa chiều.', 'Dòng sản phẩm: Trang sức bạc Geometric\r\nChất liệu: Bạc cao cấp 925\r\n\r\nThiết kế tập trung vào các khối đa diện với đường nét dứt khoát. Dưới mỗi góc nhìn, sản phẩm lại tỏa sáng theo một cách riêng nhờ kỹ thuật đánh bóng gương trên các mặt cắt, mang lại vẻ sang trọng và hiện đại.', 0, 0, 0, 1, 0, '5.png', 190000.00, '2026-04-18 10:08:37'),
(42, 5, 'Khuyên tai bạc hình trái tim và xích', 'khuyen-tai-bac-trai-tim-va-xich', 'Ngọt ngào. Sự kết hợp giữa nụ trái tim và dây xích rơi tạo nét duyên dáng.', 'Dòng sản phẩm: Khuyên tai dáng dài\r\nChất liệu: Bạc 925\r\n\r\nMẫu khuyên tai không đối xứng đầy phá cách với một bên là nụ trái tim và một bên là dây xích mảnh. Thiết kế mang lại vẻ quyến rũ, nữ tính và giúp vùng cổ trông thon gọn hơn trong các buổi tiệc tối.', 1, 0, 0, 1, 0, '6.png', 210000.00, '2026-04-18 10:08:37'),
(43, 5, 'Nhẫn bạc xoắn (Bộ khuyên tai tròn)', 'bo-khuyen-tai-bac-tron-xoan', 'Thanh lịch. Set vòng tròn xoắn mảnh mai cho vẻ ngoài tinh tế.', 'Dòng sản phẩm: Trang sức bạc vòng\r\nChất liệu: Bạc 925 chuẩn\r\n\r\nSản phẩm là sự kết hợp giữa kiểu dáng vòng truyền thống và kỹ thuật xoắn bạc tinh xảo. Các vòng tròn đan xen tạo hiệu ứng thị giác mềm mại, dễ dàng phối hợp với nhiều phong cách trang phục khác nhau.', 0, 0, 0, 1, 0, '7.png', 155000.00, '2026-04-18 10:08:37'),
(44, 5, 'Khuyên tai bạc hoop vuông nhỏ', 'khuyen-tai-bac-hoop-vuong-nho', 'Ấn tượng. Họa tiết lưới dập nổi trên khung vuông hiện đại.', 'Dòng sản phẩm: Khuyên tai Urban\r\nChất liệu: Bạc cao cấp 925\r\n\r\nThay vì vòng tròn, thiết kế vuông mang lại nét cứng cáp và cá tính hơn. Họa tiết dập nổi dạng ô lưới trên bề mặt giúp đôi khuyên tai trông cao cấp và có điểm nhấn hơn hẳn các mẫu trơn thông thường.', 0, 0, 0, 1, 0, '8.png', 185000.00, '2026-04-18 10:08:37'),
(45, 5, 'Khuyên tai bạc hình ngôi sao và la bàn', 'khuyen-tai-bac-ngoi-sao-la-ban', 'Tự do. Họa tiết lá/ngôi sao cách điệu mang hơi hướng thiên nhiên.', 'Dòng sản phẩm: Trang sức bạc Boho\nChất liệu: Bạc 925\n\nMặt khuyên tai được chạm khắc tỉ mỉ hình ảnh đối xứng như một ngôi sao may mắn hoặc kim la bàn. Sản phẩm mang lại cảm giác bình yên và phóng khoáng, rất hợp với những trang phục dạo phố nhẹ nhàng.', 0, 0, 0, 1, 0, '9.png', 160000.00, '2026-04-18 10:08:37'),
(46, 5, 'Khuyên tai bạc hình đĩa rỗng', 'khuyen-tai-bac-hinh-dia-rong', 'Tinh tế. Họa tiết tia sáng hướng tâm quanh tâm rỗng độc đáo.', 'Dòng sản phẩm: Trang sức Vintage\r\nChất liệu: Bạc 925\r\n\r\nLấy cảm hứng từ những đồng tiền cổ hoặc bánh xe pháp luân, mẫu thiết kế này mang lại vẻ đẹp cổ điển và bí ẩn. Các tia sáng được phay xước tỉ mỉ tạo độ bắt sáng lung linh dù không đính đá.', 0, 0, 0, 1, 0, '10.png', 170000.00, '2026-04-18 10:08:37'),
(47, 5, 'Khuyên tai bạc đính đá (Mô phỏng vân)', 'khuyen-tai-bac-dinh-da-van', 'Sang trọng. Bề mặt bạc dập vân đá tạo độ lấp lánh như kim cương.', 'Dòng sản phẩm: Trang sức bạc cao cấp\r\nChất liệu: Bạc 925\r\n\r\nDù không đính đá thật, nhưng với kỹ thuật dập vân kim cương (diamond cut), bề mặt bạc phản chiếu ánh sáng cực tốt, tạo hiệu ứng lấp lánh rạng rỡ, giúp bạn nổi bật trong mọi bữa tiệc.', 1, 0, 0, 1, 0, '11.png', 220000.00, '2026-04-18 10:08:37'),
(48, 5, 'Khuyên tai bạc hình ngôi sao 4 cánh', 'khuyen-tai-bac-ngoi-sao-4-canh', 'Hiện đại. Biểu tượng ngôi sao 4 cánh (North Star) thanh thoát.', 'Dòng sản phẩm: Trang sức Minimalist\r\nChất liệu: Bạc 925 tinh khiết\r\n\r\nHình tượng ngôi sao bốn cánh tối giản mang phong cách phương Tây hiện đại. Thiết kế nhỏ gọn, tinh tế, phù hợp cho các cô nàng yêu thích sự đơn giản nhưng vẫn muốn có một điểm nhấn sáng giá trên khuôn mặt.', 1, 0, 0, 1, 0, '12.png', 150000.00, '2026-04-18 10:08:37'),
(49, 4, 'Mặt dây chuyền Trái tim CZ', 'trai-tim-cz', 'Thiết kế trái tim tinh tế, phù hợp làm quà tặng.', 'Chất liệu: Bạc 925 cao cấp, không gây kích ứng da\r\nĐính đá: CZ sáng bóng, độ lấp lánh cao\r\nThiết kế: Hình trái tim biểu tượng của tình yêu vĩnh cửu\r\nGia công: Tỉ mỉ từng chi tiết, bề mặt đánh bóng cao cấp\r\nPhong cách: Nữ tính, nhẹ nhàng, thanh lịch\r\nPhù hợp: Đi chơi, dự tiệc, làm quà tặng người yêu\r\nBảo quản: Tránh tiếp xúc hóa chất, lau bằng khăn mềm sau khi sử dụng', 1, 0, 0, 1, 0, 'matdaychuyen1.jpg', 650000.00, '2026-04-18 13:09:24'),
(50, 4, 'Mặt dây chuyền Ngôi sao', 'ngoi-sao', 'Mặt dây chuyền hình ngôi sao trẻ trung, năng động.', 'Chất liệu: Bạc 925 cao cấp\r\nThiết kế: Ngôi sao nhỏ xinh, tinh tế\r\nBề mặt: Đánh bóng sáng, chống xỉn màu\r\nPhong cách: Trẻ trung, năng động, hiện đại\r\nPhù hợp: Đeo hàng ngày, đi học, đi chơi\r\nƯu điểm: Nhẹ, dễ phối đồ, phù hợp nhiều phong cách\r\nBảo quản: Tránh nước hoa và mỹ phẩm tiếp xúc trực tiếp', 0, 0, 0, 1, 0, 'matdaychuyen2.jpg', 590000.00, '2026-04-18 13:09:24'),
(51, 4, 'Mặt dây chuyền Giọt nước', 'giot-nuoc', 'Thiết kế giọt nước sang trọng, nổi bật.', 'Chất liệu: Bạc 925 kết hợp đá CZ cao cấp\r\nThiết kế: Dáng giọt nước mềm mại, thanh thoát\r\nĐính đá: Viên đá chủ sáng, phản chiếu ánh sáng tốt\r\nPhong cách: Sang trọng, quý phái\r\nPhù hợp: Dự tiệc, sự kiện, gặp gỡ quan trọng\r\nGia công: Chi tiết sắc nét, không góc cạnh gây khó chịu\r\nBảo quản: Lau sạch sau khi sử dụng để giữ độ sáng', 1, 0, 0, 1, 0, 'matday3.jpg', 720000.00, '2026-04-18 13:09:24'),
(52, 4, 'Mặt dây chuyền Hoa nhỏ', 'hoa-nho', 'Mặt dây hoa nhỏ xinh, dễ thương.', 'Chất liệu: Bạc 925\r\nThiết kế: Hoa nhỏ tinh xảo, nữ tính\r\nPhong cách: Dịu dàng, đáng yêu\r\nKích thước: Nhỏ gọn, nhẹ nhàng khi đeo\r\nPhù hợp: Đi học, đi chơi, phong cách hằng ngày\r\nƯu điểm: Dễ phối trang phục, phù hợp nhiều độ tuổi\r\nBảo quản: Tránh va đập mạnh', 0, 0, 0, 1, 0, 'matday4.jpg', 680000.00, '2026-04-18 13:09:24'),
(53, 4, 'Mặt dây chuyền Vương miện', 'vuong-mien', 'Thiết kế vương miện sang trọng, quý phái.', 'Chất liệu: Bạc 925 cao cấp\r\nThiết kế: Vương miện biểu tượng quyền lực\r\nĐính đá: Nhiều viên đá nhỏ tinh xảo\r\nPhong cách: Sang trọng, quý phái\r\nPhù hợp: Tiệc, sự kiện, quà tặng cao cấp\r\nGia công: Độ hoàn thiện cao, chi tiết sắc nét\r\nBảo quản: Cất trong hộp khi không sử dụng', 1, 0, 0, 1, 0, 'matday5.jpg', 890000.00, '2026-04-18 13:09:24'),
(54, 4, 'Mặt dây chuyền Love', 'love', 'Biểu tượng tình yêu đơn giản nhưng ý nghĩa.', 'Chất liệu: Bạc 925\r\nThiết kế: Chữ Love cách điệu\r\nPhong cách: Lãng mạn, trẻ trung\r\nPhù hợp: Quà tặng người yêu, kỷ niệm\r\nƯu điểm: Thiết kế đơn giản nhưng nổi bật\r\nDễ phối: Phù hợp nhiều kiểu trang phục\r\nBảo quản: Tránh nước và hóa chất', 0, 0, 0, 1, 0, 'matday6.jpg', 750000.00, '2026-04-18 13:09:24'),
(55, 4, 'Mặt dây chuyền Hình tròn đá', 'hinh-tron-da', 'Thiết kế tròn đính đá tinh xảo.', 'Chất liệu: Bạc 925\r\nĐính đá: Đá CZ cao cấp\r\nThiết kế: Hình tròn cổ điển\r\nPhong cách: Hiện đại, thanh lịch\r\nPhù hợp: Công sở, đi làm\r\nƯu điểm: Không lỗi mốt theo thời gian\r\nBảo quản: Lau bằng khăn mềm', 1, 0, 0, 1, 0, 'matday7.jpg', 810000.00, '2026-04-18 13:09:24'),
(56, 4, 'Mặt dây chuyền Chiếc lá', 'chiec-la', 'Mặt dây chiếc lá nhẹ nhàng, tự nhiên.', 'Chất liệu: Bạc 925\r\nThiết kế: Hình lá uốn cong mềm mại\r\nPhong cách: Tự nhiên, thanh thoát\r\nPhù hợp: Hàng ngày, đi chơi\r\nƯu điểm: Nhẹ, thoải mái khi đeo\r\nTính ứng dụng: Phù hợp mọi độ tuổi\r\nBảo quản: Tránh trầy xước', 0, 0, 0, 1, 0, 'matday8.jpg', 670000.00, '2026-04-18 13:09:24'),
(57, 4, 'Mặt dây chuyền Cánh bướm', 'canh-buom', 'Thiết kế cánh bướm bay bổng, nữ tính.', 'Chất liệu: Bạc 925\r\nThiết kế: Cánh bướm tinh xảo\r\nĐính đá: Nhẹ nhàng, tạo điểm nhấn\r\nPhong cách: Dễ thương, nữ tính\r\nPhù hợp: Đi chơi, hẹn hò\r\nƯu điểm: Nổi bật nhưng không quá cầu kỳ\r\nBảo quản: Lau sạch sau khi dùng', 1, 0, 0, 1, 0, 'matday9.jpg', 780000.00, '2026-04-18 13:09:24'),
(58, 4, 'Mặt dây chuyền Hình vuông', 'hinh-vuong', 'Phong cách hình học đơn giản, hiện đại.', 'Chất liệu: Bạc 925\r\nThiết kế: Hình vuông tối giản\r\nPhong cách: Hiện đại, cá tính\r\nPhù hợp: Công sở, thời trang basic\r\nƯu điểm: Dễ phối đồ, không lỗi thời\r\nGia công: Bề mặt bóng đẹp\r\nBảo quản: Tránh va chạm mạnh', 0, 0, 0, 1, 0, 'matday10.jpg', 730000.00, '2026-04-18 13:09:24'),
(59, 4, 'Mặt dây chuyền Ngọc trai', 'ngoc-trai', 'Mặt dây ngọc trai thanh lịch, sang trọng.', 'Chất liệu: Bạc kết hợp ngọc trai tự nhiên\r\nThiết kế: Ngọc trai trung tâm nổi bật\r\nPhong cách: Quý phái, thanh lịch\r\nPhù hợp: Dự tiệc, sự kiện quan trọng\r\nƯu điểm: Tôn lên vẻ sang trọng người đeo\r\nĐộ bền: Cao, ít bị lỗi thời\r\nBảo quản: Tránh nước và mỹ phẩm', 1, 0, 0, 1, 0, 'matday11.jpg', 1200000.00, '2026-04-18 13:09:24'),
(60, 4, 'Mặt dây chuyền Đính đá', 'dinh-da', 'Thiết kế đính đá nổi bật, thu hút.', 'Chất liệu: Bạc 925\r\nĐính đá: Nhiều viên đá nhỏ lấp lánh\r\nThiết kế: Sang trọng, bắt sáng tốt\r\nPhong cách: Nổi bật, thời trang\r\nPhù hợp: Dự tiệc, sự kiện\r\nƯu điểm: Thu hút ánh nhìn\r\nBảo quản: Lau nhẹ sau khi sử dụng', 0, 0, 0, 1, 0, 'matday12.jpg', 950000.00, '2026-04-18 13:09:24');

-- --------------------------------------------------------

--
-- Table structure for table `size`
--

CREATE TABLE `size` (
  `id` int(11) NOT NULL,
  `MaSanPham` int(11) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `SoLuongTon` int(11) NOT NULL DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `size`
--

INSERT INTO `size` (`id`, `MaSanPham`, `size`, `SoLuongTon`, `sku`) VALUES
(206, 2, 5, 8, 'SP2-S5'),
(207, 2, 6, 10, 'SP2-S6'),
(208, 2, 7, 10, 'SP2-S7'),
(209, 2, 8, 10, 'SP2-S8'),
(210, 2, 9, 10, 'SP2-S9'),
(211, 2, 10, 10, 'SP2-S10'),
(212, 2, 11, 10, 'SP2-S11'),
(213, 3, 5, 10, 'SP3-S5'),
(214, 3, 6, 10, 'SP3-S6'),
(215, 3, 7, 10, 'SP3-S7'),
(216, 3, 8, 10, 'SP3-S8'),
(217, 3, 9, 10, 'SP3-S9'),
(218, 3, 10, 10, 'SP3-S10'),
(219, 3, 11, 10, 'SP3-S11'),
(220, 4, 5, 10, 'SP4-S5'),
(221, 4, 6, 10, 'SP4-S6'),
(222, 4, 7, 10, 'SP4-S7'),
(223, 4, 8, 10, 'SP4-S8'),
(224, 4, 9, 10, 'SP4-S9'),
(225, 4, 10, 10, 'SP4-S10'),
(226, 4, 11, 10, 'SP4-S11'),
(227, 5, 5, 10, 'SP5-S5'),
(228, 5, 6, 10, 'SP5-S6'),
(229, 5, 7, 10, 'SP5-S7'),
(230, 5, 8, 10, 'SP5-S8'),
(231, 5, 9, 10, 'SP5-S9'),
(232, 5, 10, 10, 'SP5-S10'),
(233, 5, 11, 10, 'SP5-S11'),
(234, 6, 5, 10, 'SP6-S5'),
(235, 6, 6, 10, 'SP6-S6'),
(236, 6, 7, 10, 'SP6-S7'),
(237, 6, 8, 10, 'SP6-S8'),
(238, 6, 9, 10, 'SP6-S9'),
(239, 6, 10, 10, 'SP6-S10'),
(240, 6, 11, 10, 'SP6-S11'),
(241, 7, 5, 7, 'SP7-S5'),
(242, 7, 6, 10, 'SP7-S6'),
(243, 7, 7, 10, 'SP7-S7'),
(244, 7, 8, 10, 'SP7-S8'),
(245, 7, 9, 10, 'SP7-S9'),
(246, 7, 10, 10, 'SP7-S10'),
(247, 7, 11, 10, 'SP7-S11'),
(248, 8, 5, 10, 'SP8-S5'),
(249, 8, 6, 10, 'SP8-S6'),
(250, 8, 7, 10, 'SP8-S7'),
(251, 8, 8, 10, 'SP8-S8'),
(252, 8, 9, 10, 'SP8-S9'),
(253, 8, 10, 10, 'SP8-S10'),
(254, 8, 11, 10, 'SP8-S11'),
(255, 9, 5, 9, 'SP9-S5'),
(256, 9, 6, 10, 'SP9-S6'),
(257, 9, 7, 10, 'SP9-S7'),
(258, 9, 8, 10, 'SP9-S8'),
(259, 9, 9, 10, 'SP9-S9'),
(260, 9, 10, 10, 'SP9-S10'),
(261, 9, 11, 10, 'SP9-S11'),
(262, 10, 5, 9, 'SP10-S5'),
(263, 10, 6, 10, 'SP10-S6'),
(264, 10, 7, 10, 'SP10-S7'),
(265, 10, 8, 10, 'SP10-S8'),
(266, 10, 9, 10, 'SP10-S9'),
(267, 10, 10, 10, 'SP10-S10'),
(268, 10, 11, 10, 'SP10-S11'),
(269, 11, 5, 10, 'SP11-S5'),
(270, 11, 6, 10, 'SP11-S6'),
(271, 11, 7, 10, 'SP11-S7'),
(272, 11, 8, 10, 'SP11-S8'),
(273, 11, 9, 10, 'SP11-S9'),
(274, 11, 10, 10, 'SP11-S10'),
(275, 11, 11, 10, 'SP11-S11'),
(276, 12, 5, 10, 'SP12-S5'),
(277, 12, 6, 10, 'SP12-S6'),
(278, 12, 7, 10, 'SP12-S7'),
(279, 12, 8, 10, 'SP12-S8'),
(280, 12, 9, 10, 'SP12-S9'),
(281, 12, 10, 10, 'SP12-S10'),
(282, 12, 11, 10, 'SP12-S11'),
(283, 13, 15, 10, 'SP13-S15'),
(284, 13, 16, 10, 'SP13-S16'),
(285, 13, 17, 10, 'SP13-S17'),
(286, 13, 18, 10, 'SP13-S18'),
(287, 13, 19, 10, 'SP13-S19'),
(288, 13, 20, 10, 'SP13-S20'),
(289, 14, 15, 10, 'SP14-S15'),
(290, 14, 16, 10, 'SP14-S16'),
(291, 14, 17, 10, 'SP14-S17'),
(292, 14, 18, 10, 'SP14-S18'),
(293, 14, 19, 10, 'SP14-S19'),
(294, 14, 20, 10, 'SP14-S20'),
(295, 15, 15, 10, 'SP15-S15'),
(296, 15, 16, 10, 'SP15-S16'),
(297, 15, 17, 10, 'SP15-S17'),
(298, 15, 18, 10, 'SP15-S18'),
(299, 15, 19, 10, 'SP15-S19'),
(300, 15, 20, 10, 'SP15-S20'),
(301, 16, 15, 10, 'SP16-S15'),
(302, 16, 16, 10, 'SP16-S16'),
(303, 16, 17, 10, 'SP16-S17'),
(304, 16, 18, 10, 'SP16-S18'),
(305, 16, 19, 10, 'SP16-S19'),
(306, 16, 20, 10, 'SP16-S20'),
(307, 17, 15, 10, 'SP17-S15'),
(308, 17, 16, 10, 'SP17-S16'),
(309, 17, 17, 10, 'SP17-S17'),
(310, 17, 18, 10, 'SP17-S18'),
(311, 17, 19, 10, 'SP17-S19'),
(312, 17, 20, 10, 'SP17-S20'),
(313, 18, 15, 10, 'SP18-S15'),
(314, 18, 16, 10, 'SP18-S16'),
(315, 18, 17, 10, 'SP18-S17'),
(316, 18, 18, 10, 'SP18-S18'),
(317, 18, 19, 10, 'SP18-S19'),
(318, 18, 20, 10, 'SP18-S20'),
(319, 19, 15, 10, 'SP19-S15'),
(320, 19, 16, 10, 'SP19-S16'),
(321, 19, 17, 10, 'SP19-S17'),
(322, 19, 18, 10, 'SP19-S18'),
(323, 19, 19, 10, 'SP19-S19'),
(324, 19, 20, 10, 'SP19-S20'),
(325, 20, 15, 10, 'SP20-S15'),
(326, 20, 16, 10, 'SP20-S16'),
(327, 20, 17, 10, 'SP20-S17'),
(328, 20, 18, 10, 'SP20-S18'),
(329, 20, 19, 10, 'SP20-S19'),
(330, 20, 20, 10, 'SP20-S20'),
(331, 21, 15, 10, 'SP21-S15'),
(332, 21, 16, 10, 'SP21-S16'),
(333, 21, 17, 10, 'SP21-S17'),
(334, 21, 18, 10, 'SP21-S18'),
(335, 21, 19, 10, 'SP21-S19'),
(336, 21, 20, 10, 'SP21-S20'),
(337, 22, 15, 10, 'SP22-S15'),
(338, 22, 16, 10, 'SP22-S16'),
(339, 22, 17, 10, 'SP22-S17'),
(340, 22, 18, 10, 'SP22-S18'),
(341, 22, 19, 10, 'SP22-S19'),
(342, 22, 20, 10, 'SP22-S20'),
(343, 23, 15, 10, 'SP23-S15'),
(344, 23, 16, 10, 'SP23-S16'),
(345, 23, 17, 10, 'SP23-S17'),
(346, 23, 18, 10, 'SP23-S18'),
(347, 23, 19, 10, 'SP23-S19'),
(348, 23, 20, 10, 'SP23-S20'),
(349, 24, 15, 10, 'SP24-S15'),
(350, 24, 16, 10, 'SP24-S16'),
(351, 24, 17, 10, 'SP24-S17'),
(352, 24, 18, 10, 'SP24-S18'),
(353, 24, 19, 10, 'SP24-S19'),
(354, 24, 20, 10, 'SP24-S20'),
(355, 25, 12, 10, 'SP25-SZ12'),
(356, 25, 13, 10, 'SP25-SZ13'),
(357, 25, 14, 10, 'SP25-SZ14'),
(358, 25, 15, 10, 'SP25-SZ15'),
(359, 25, 16, 10, 'SP25-SZ16'),
(360, 25, 17, 10, 'SP25-SZ17'),
(361, 25, 18, 10, 'SP25-SZ18'),
(362, 25, 19, 10, 'SP25-SZ19'),
(363, 25, 20, 10, 'SP25-SZ20'),
(364, 26, 12, 10, 'SP26-SZ12'),
(365, 26, 13, 10, 'SP26-SZ13'),
(366, 26, 14, 10, 'SP26-SZ14'),
(367, 26, 15, 10, 'SP26-SZ15'),
(368, 26, 16, 10, 'SP26-SZ16'),
(369, 26, 17, 10, 'SP26-SZ17'),
(370, 26, 18, 10, 'SP26-SZ18'),
(371, 26, 19, 10, 'SP26-SZ19'),
(372, 26, 20, 10, 'SP26-SZ20'),
(373, 27, 12, 10, 'SP27-SZ12'),
(374, 27, 13, 10, 'SP27-SZ13'),
(375, 27, 14, 10, 'SP27-SZ14'),
(376, 27, 15, 10, 'SP27-SZ15'),
(377, 27, 16, 10, 'SP27-SZ16'),
(378, 27, 17, 10, 'SP27-SZ17'),
(379, 27, 18, 10, 'SP27-SZ18'),
(380, 27, 19, 10, 'SP27-SZ19'),
(381, 27, 20, 10, 'SP27-SZ20'),
(382, 28, 12, 10, 'SP28-SZ12'),
(383, 28, 13, 10, 'SP28-SZ13'),
(384, 28, 14, 10, 'SP28-SZ14'),
(385, 28, 15, 10, 'SP28-SZ15'),
(386, 28, 16, 10, 'SP28-SZ16'),
(387, 28, 17, 10, 'SP28-SZ17'),
(388, 28, 18, 10, 'SP28-SZ18'),
(389, 28, 19, 10, 'SP28-SZ19'),
(390, 28, 20, 10, 'SP28-SZ20'),
(391, 29, 12, 10, 'SP29-SZ12'),
(392, 29, 13, 10, 'SP29-SZ13'),
(393, 29, 14, 10, 'SP29-SZ14'),
(394, 29, 15, 10, 'SP29-SZ15'),
(395, 29, 16, 10, 'SP29-SZ16'),
(396, 29, 17, 10, 'SP29-SZ17'),
(397, 29, 18, 10, 'SP29-SZ18'),
(398, 29, 19, 10, 'SP29-SZ19'),
(399, 29, 20, 10, 'SP29-SZ20'),
(400, 30, 12, 10, 'SP30-SZ12'),
(401, 30, 13, 10, 'SP30-SZ13'),
(402, 30, 14, 10, 'SP30-SZ14'),
(403, 30, 15, 10, 'SP30-SZ15'),
(404, 30, 16, 10, 'SP30-SZ16'),
(405, 30, 17, 10, 'SP30-SZ17'),
(406, 30, 18, 10, 'SP30-SZ18'),
(407, 30, 19, 10, 'SP30-SZ19'),
(408, 30, 20, 10, 'SP30-SZ20'),
(409, 31, 12, 10, 'SP31-SZ12'),
(410, 31, 13, 10, 'SP31-SZ13'),
(411, 31, 14, 10, 'SP31-SZ14'),
(412, 31, 15, 10, 'SP31-SZ15'),
(413, 31, 16, 10, 'SP31-SZ16'),
(414, 31, 17, 10, 'SP31-SZ17'),
(415, 31, 18, 10, 'SP31-SZ18'),
(416, 31, 19, 10, 'SP31-SZ19'),
(417, 31, 20, 10, 'SP31-SZ20'),
(418, 32, 12, 10, 'SP32-SZ12'),
(419, 32, 13, 10, 'SP32-SZ13'),
(420, 32, 14, 10, 'SP32-SZ14'),
(421, 32, 15, 10, 'SP32-SZ15'),
(422, 32, 16, 10, 'SP32-SZ16'),
(423, 32, 17, 10, 'SP32-SZ17'),
(424, 32, 18, 10, 'SP32-SZ18'),
(425, 32, 19, 10, 'SP32-SZ19'),
(426, 32, 20, 10, 'SP32-SZ20'),
(427, 33, 12, 10, 'SP33-SZ12'),
(428, 33, 13, 10, 'SP33-SZ13'),
(429, 33, 14, 10, 'SP33-SZ14'),
(430, 33, 15, 10, 'SP33-SZ15'),
(431, 33, 16, 10, 'SP33-SZ16'),
(432, 33, 17, 10, 'SP33-SZ17'),
(433, 33, 18, 10, 'SP33-SZ18'),
(434, 33, 19, 10, 'SP33-SZ19'),
(435, 33, 20, 10, 'SP33-SZ20'),
(436, 34, 12, 10, 'SP34-SZ12'),
(437, 34, 13, 10, 'SP34-SZ13'),
(438, 34, 14, 10, 'SP34-SZ14'),
(439, 34, 15, 10, 'SP34-SZ15'),
(440, 34, 16, 10, 'SP34-SZ16'),
(441, 34, 17, 10, 'SP34-SZ17'),
(442, 34, 18, 10, 'SP34-SZ18'),
(443, 34, 19, 10, 'SP34-SZ19'),
(444, 34, 20, 10, 'SP34-SZ20'),
(445, 35, 12, 10, 'SP35-SZ12'),
(446, 35, 13, 10, 'SP35-SZ13'),
(447, 35, 14, 10, 'SP35-SZ14'),
(448, 35, 15, 10, 'SP35-SZ15'),
(449, 35, 16, 10, 'SP35-SZ16'),
(450, 35, 17, 10, 'SP35-SZ17'),
(451, 35, 18, 10, 'SP35-SZ18'),
(452, 35, 19, 10, 'SP35-SZ19'),
(453, 35, 20, 10, 'SP35-SZ20'),
(463, 1, 5, 10, NULL),
(464, 1, 6, 10, NULL),
(465, 1, 7, 10, NULL),
(466, 1, 8, 1, NULL),
(467, 1, 9, 10, NULL),
(468, 1, 10, 10, NULL),
(469, 1, 11, 10, NULL),
(479, 36, 12, 3, NULL),
(480, 36, 13, 0, NULL),
(481, 36, 14, 0, NULL),
(482, 36, 15, 0, NULL),
(483, 36, 16, 0, NULL),
(484, 36, 17, 0, NULL),
(485, 36, 18, 0, NULL),
(486, 36, 19, 0, NULL),
(487, 36, 20, 0, NULL),
(488, 60, 0, 17, NULL),
(489, 59, 0, 10, NULL),
(490, 58, 0, 28, NULL),
(491, 57, 0, 20, NULL),
(492, 56, 0, 35, NULL),
(493, 55, 0, 15, NULL),
(494, 54, 0, 22, NULL),
(495, 53, 0, 12, NULL),
(496, 52, 0, 40, NULL),
(497, 51, 0, 18, NULL),
(498, 50, 0, 30, NULL),
(499, 49, 0, 25, NULL),
(500, 41, 0, 0, NULL),
(502, 48, 0, 4, NULL),
(503, 47, 0, 10, NULL),
(504, 37, 0, 6, NULL),
(505, 40, 0, 8, NULL),
(506, 39, 0, 12, NULL),
(507, 46, 0, 12, NULL),
(508, 44, 0, 1, NULL),
(509, 43, 0, 12, NULL),
(510, 42, 0, 12, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tintuc`
--

CREATE TABLE `tintuc` (
  `MaTinTuc` int(11) NOT NULL,
  `TieuDe` varchar(255) DEFAULT NULL,
  `DuongDan` varchar(255) DEFAULT NULL,
  `MoTaNgan` text DEFAULT NULL,
  `NoiDung` text DEFAULT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `NgayDang` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `idUser` int(11) NOT NULL,
  `token` varchar(128) NOT NULL,
  `ThoiGianTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `HetHan` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`idUser`, `token`, `ThoiGianTao`, `HetHan`) VALUES
(3, '351623', '2026-04-15 10:57:29', '2026-04-15 11:02:29'),
(3, '514334', '2026-04-15 10:51:57', '2026-04-15 05:56:57'),
(3, '728891', '2026-04-15 10:53:19', '2026-04-15 05:58:19');

-- --------------------------------------------------------

--
-- Table structure for table `trangthaidonhang`
--

CREATE TABLE `trangthaidonhang` (
  `MaTrangThai` int(11) NOT NULL,
  `TenTrangThai` varchar(50) DEFAULT 'Đang xử lý'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trangthaidonhang`
--

INSERT INTO `trangthaidonhang` (`MaTrangThai`, `TenTrangThai`) VALUES
(1, 'Đang xử lý'),
(2, 'Đã xác nhận'),
(3, 'Đang giao hàng'),
(4, 'Đã thanh toán'),
(5, 'Hoàn thành'),
(6, 'Đã hủy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`MaChiTiet`),
  ADD KEY `MaDonHang` (`MaDonHang`),
  ADD KEY `id_size` (`id_size`);

--
-- Indexes for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`MaDanhGia`),
  ADD UNIQUE KEY `MaSanPham` (`MaSanPham`,`idUser`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`MaDanhMuc`);

--
-- Indexes for table `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`MaDonHang`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `MaTrangThai` (`MaTrangThai`);

--
-- Indexes for table `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`MaGioHang`),
  ADD UNIQUE KEY `idUser` (`idUser`,`idSize`),
  ADD KEY `id_size` (`idSize`),
  ADD KEY `idx_giohang_user` (`idUser`);

--
-- Indexes for table `lienhe`
--
ALTER TABLE `lienhe`
  ADD PRIMARY KEY (`MaLienHe`),
  ADD KEY `idx_lienhe_email` (`Email`),
  ADD KEY `idx_lienhe_trangthai` (`TrangThai`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `id_google` (`id_google`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MaSanPham`),
  ADD UNIQUE KEY `DuongDan` (`DuongDan`),
  ADD KEY `MaDanhMuc` (`MaDanhMuc`);

--
-- Indexes for table `size`
--
ALTER TABLE `size`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `MaSanPham` (`MaSanPham`,`size`);

--
-- Indexes for table `tintuc`
--
ALTER TABLE `tintuc`
  ADD PRIMARY KEY (`MaTinTuc`),
  ADD UNIQUE KEY `DuongDan` (`DuongDan`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`idUser`,`token`);

--
-- Indexes for table `trangthaidonhang`
--
ALTER TABLE `trangthaidonhang`
  ADD PRIMARY KEY (`MaTrangThai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `MaChiTiet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `danhgia`
--
ALTER TABLE `danhgia`
  MODIFY `MaDanhGia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `MaDanhMuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `donhang`
--
ALTER TABLE `donhang`
  MODIFY `MaDonHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `giohang`
--
ALTER TABLE `giohang`
  MODIFY `MaGioHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `lienhe`
--
ALTER TABLE `lienhe`
  MODIFY `MaLienHe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `MaSanPham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `size`
--
ALTER TABLE `size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=511;

--
-- AUTO_INCREMENT for table `tintuc`
--
ALTER TABLE `tintuc`
  MODIFY `MaTinTuc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trangthaidonhang`
--
ALTER TABLE `trangthaidonhang`
  MODIFY `MaTrangThai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`id_size`) REFERENCES `size` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`) ON DELETE CASCADE,
  ADD CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `nguoidung` (`idUser`) ON DELETE CASCADE;

--
-- Constraints for table `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `nguoidung` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `donhang_ibfk_2` FOREIGN KEY (`MaTrangThai`) REFERENCES `trangthaidonhang` (`MaTrangThai`) ON DELETE SET NULL;

--
-- Constraints for table `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `nguoidung` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`idSize`) REFERENCES `size` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDanhMuc`) REFERENCES `danhmuc` (`MaDanhMuc`) ON DELETE SET NULL;

--
-- Constraints for table `size`
--
ALTER TABLE `size`
  ADD CONSTRAINT `size_ibfk_1` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`) ON DELETE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `nguoidung` (`idUser`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
