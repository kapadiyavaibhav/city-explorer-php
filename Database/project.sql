-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2025 at 09:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL,
  `cat_image` varchar(255) DEFAULT NULL,
  `cat_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `cat_image`, `cat_name`) VALUES
(2, 'historical_place', 'HistoricalPlace'),
(3, 'restaurant', 'Restaurant'),
(4, 'school', 'School'),
(5, 'college', 'College'),
(6, 'pg', 'PG'),
(7, 'hospital', 'Hospital');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `img_src` varchar(255) NOT NULL,
  `city_name` varchar(20) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_id`, `img_src`, `city_name`, `description`) VALUES
(1, 'rajkot.jpg', 'Rajkot', 'A vibrant commercial hub of Gujarat, celebrated for its industries and cultural heritage.'),
(2, 'junagadh.jpg ', 'Junagadh', 'Historic city at the foot of Girnar hills, known as the gateway to Gir National Park.'),
(3, 'bhavnagar.jpg', 'Bhavnagar', 'Coastal city known for shipbreaking yard at Alang and its rich royal legacy.'),
(4, 'amreli.jpg', 'Amreli', 'A cultural city in Saurashtra, famous for diamond polishing and lion conservation.'),
(5, 'jetpur.jpg', 'Jetpur', 'Renowned textile town in Gujarat, famous for block printing, dyeing, and screen printing.');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(12) DEFAULT NULL,
  `courses` text DEFAULT NULL,
  `departments` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `faculty` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `scholarships` text DEFAULT NULL,
  `hostel` varchar(10) DEFAULT NULL,
  `transport` varchar(10) DEFAULT NULL,
  `library` varchar(10) DEFAULT NULL,
  `sports` varchar(10) DEFAULT NULL,
  `cafeteria` varchar(10) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`id`, `name`, `img`, `address`, `city_name`, `cat_name`, `contact`, `email`, `website`, `established`, `courses`, `departments`, `facilities`, `faculty`, `admission_process`, `gallery`, `scholarships`, `hostel`, `transport`, `library`, `sports`, `cafeteria`, `disabled_friendly`, `user_email`) VALUES
(1, 'Junagadh Agricultural University', '68ce2d781318c_cj11.jpg', 'Junagadh Agricultural University Campus, Junagadh - 362001', 'Junagadh', 'College', '0285-2672080', 'registrar@jau.in', 'https://www.jau.in/', '1972', 'Agriculture ,Veterinary ,Fisheries ,Horticulture', 'Agriculture , Engineering, Science', 'Labs, Library, Hostels, Sports', 'Qualified teaching and research faculty||With 12+ Years of experience ', 'Entrance exams and state quota', '68ce2d78135f3_cj12.jpg,68ce2d7813a6b_cj13.jpg', 'Merit based scholarships ', '', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(2, 'Dr. Subhash University', '68ce2e355061d_cj21.jpg', 'Junagadh - Rajkot Highway, Junagadh, Gujarat', 'Junagadh', 'College', '+91 97277 77444', 'info@drsubhashtech.edu.in', 'https://www.drsubhashuni.edu.in/', '2012', 'Engineering, Pharmacy ,Management ,Arts ,Commerce, Science', 'Engineering ,,Pharmacy ,Management, Arts', 'Library ,Labs, Hostel ,Sports ,Complex', 'Qualified faculty across streams||', 'Direct admission / ACPC for professional courses', '68ce2e35509d8_cj22.jpg,68ce2e3550c70_cj23.jpg', 'Merit based scholarships ', '', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(3, 'Bhakta Kavi Narsinh Mehta University', '68ce2ea2ceabf_cj31.jpg', 'Government Polytechnic Campus, Khadia, Junagadh - 362263', 'Junagadh', 'College', '0285-2681400', 'info@bkmuniversity.ac.in', 'https://www.bkmuniversity.ac.in/', '2015', 'Arts ,Commerce ,Science, Law ,Education', 'Various faculties', 'Library ,Hostels ,Labs ,IT Center', 'Recognized faculty under Gujarat Govt.||', 'Merit and government rules', '68ce2ea2cee07_cj32.jpg,68ce2ea2cf075_cj33.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', NULL),
(4, 'Saurashtra University', '68ce2fd4df798_cr11.jpg', 'Saurashtra University Campus, University Road, Rajkot - 360005', 'Rajkot', 'College', '0281-2578501', 'registrar@sauuni.ac.in', 'https://www.saurashtrauniversity.edu/', '1967', 'Arts, Science ,Commerce ,Law, Medicine, Management', 'Faculties of Arts Commerce Science Education', 'Central library ,Hostels ,Labs ,Sports Complex', 'UGC recognized teaching staff||With 12+ Years of experience ', 'Merit based / centralized admission', '68ce2fd4dfb2a_cr12.jpg,68ce2fd4e0028_cr13.jpg', 'Merit based scholarships ', '', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(5, 'Atmiya University', '68ce307c0d210_cr21.jpg', 'Kalawad Road, Rajkot - 360005', 'Rajkot', 'College', '0281-2563445', 'info@atmiyauni.ac.in', 'https://www.atmiyauni.ac.in/', '2018', 'Engineering ,Management ,Pharmacy ,Science', 'Engineering, Management ,Science', 'Labs ,Library ,Hostels ,Sports ,Auditorium', 'Qualified professors and lecturers||', 'ACPC / Direct admission', '68ce307c0d365_cr22.jpg,68ce307c0d520_cr23.jpg', 'Merit based scholarships ', '', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(6, 'Harivandana College', '68ce30df6548c_cr31.jpg', 'Munjka, Near Saurashtra University, Rajkot - 360005', 'Rajkot', 'College', '+91 281 2573801', 'info@harivandanacollege.org', 'https://www.harivandanacollege.org/', '1992', 'Arts ,Commerce ,Science', 'Arts, Commerce, Science', 'Library, Computer ,,Lab ,Sports', 'Experienced faculty||With 15+ Years of experience ', 'Merit based admission', '68ce30df658c5_cr32.jpg,68ce30df65ed2_cr33.jpg', 'Merit based scholarships ', 'Hostel', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(7, 'Shri Kamani Science & Prataprai Arts College', '68ce313ca5dfb_ca11.jpg', 'Bhavnagar Road, Amreli - 365601', 'Amreli', 'College', '02792-2220470', 'ksc_amreli@rediffmail.com', 'https://www.saurashtrauniversity.edu/affiliated-colleges/', '1960', 'Science, Arts', 'Science ,Arts', 'Library, Labs ,Sports ,Ground', 'Qualified faculty||', 'Merit based admission', '68ce313ca6124_ca12.jpg,68ce313ca65a8_ca13.jpg', 'Merit based scholarships ', '', '', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(8, 'Matushri Monghiba Mahila Arts College', '68ce31cf71d83_ca21.jpg', 'Chittal Road, Amreli - 365601', 'Amreli', 'College', '02792-2223942', 'mac_amr11@yahoo.co.in', 'https://www.saurashtrauniversity.edu/affiliated-colleges/', '1970', 'Arts', 'Arts', 'Library ,Reading Room', 'Qualified faculty||', 'Merit based', '68ce31cf72131_ca22.jpg,68ce31cf724bc_ca23.jpg', 'Merit based scholarships ', '', '', '', 'Sports', 'Cafeteria', 'Disabled Friendly', NULL),
(9, 'Smt. S. D. Kotak Law College', '68ce328a26e96_ca31.jpg', 'Chittal Road, Near Airport, Amreli - 365601', 'Amreli', 'College', '02792-2223522', 'sdkotaklaw@yahoo.com', 'https://www.saurashtrauniversity.edu/affiliated-colleges/', '1973', 'Law (LLB LLM)', 'Law', 'Library ,Moot Court', 'Qualified law faculty||', 'Merit and entrance', '68ce328a2722b_ca32.jpg,68ce328a276c4_ca33.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', 'Cafeteria', '', NULL),
(10, 'Government Medical College, Bhavnagar', '68ce32dd45472_cb11.jpg', 'Ghogha Road, Bhavnagar - 364001', 'Bhavnagar', 'College', '0278-2422011', 'dean.gmcbhav@gmail.com', 'https://gmcbvn.edu.in/', '1995', 'MBBS ,MD, MS, Nursing', 'Medical ,Paramedical', 'Hospital ,Labs ,Library ,Hostel', 'Medical faculty & doctors||', 'NEET entrance', '68ce32dd4595c_cb12.jpg,68ce32dd45e11_cb13.jpg', 'Merit based scholarships ', '', '', '', 'Sports', 'Cafeteria', 'Disabled Friendly', NULL),
(11, 'Gyanmanjari Institute of Technology', '68ce3339d1c16_cb21.jpg', 'Bhavnagar - Sidsar Road, Bhavnagar - 364060', 'Bhavnagar', 'College', '0278-2522550', 'info@gmit.org', 'https://www.gmit.org/', '2010', 'Engineering (B.E.) ,Diploma', 'Civil Mechanical ,Computer ,Electrical', 'Labs, Library, Hostel ,Workshop', 'Engineering faculty||', 'ACPC / merit', '68ce3339d1e0b_cb22.jpg,68ce3339d2074_cb23.jpg', 'Merit based scholarships ', '', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', NULL),
(12, 'Sheth H.J. Law College, Bhavnagar', '68ce339ca6e55_cb31.jpg', 'Sardar Nagar Circle, Bhavnagar - 364001', 'Bhavnagar', 'College', '0278-2421840', 'info@hjlawcollege.edu.in', 'https://hjlawcollege.edu.in/', '1955', 'Law (LLB LLM)', 'Law', 'Library ,Moot Court ,Seminar Halls', 'Qualified law faculty||', 'Merit based', '68ce339ca71f3_cb32.jpg,68ce339ca7727_cb33.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', NULL),
(13, 'Shree G.K. & C.K. Bosamia Arts & Commerce College', '68ce33ec65f8d_cj11.jpg', 'Junagadh Road, Jetpur - 360370, Rajkot District, Gujarat', 'Jetpur', 'College', '02823-227356', 'gkck.college@gmail.com', 'https://www.careerindia.com/colleges/shree-g-k-c-k-bosamia-arts-commerce-college-rajkot-gujarat-cp738/', '1965', 'B.A., B.Com,. B.B.A., B.C.A., P.G,.D.C.A., M.Sc (IT & CA)', 'Arts ,Commerce, IT, Management', 'Library, Labs, Computer ,Center', 'Experienced faculty in Arts & Commerce||', 'Merit based', '68ce33ec662e3_cj12.jpg,68ce33ec66562_cj13.jpg', 'Merit based scholarships ', '', '', '', 'Sports', 'Cafeteria', 'Disabled Friendly', NULL),
(14, 'P. S. Hirpara Mahila College', '68ce3486f1eed_cj21.jpg', 'Dhoraji Road, Near Railway Crossing, Jetpur - 360370, Rajkot District, Gujarat', 'Jetpur', 'College', '9925728828', 'pshiraparacollege@gmail.com', 'https://www.saurashtrauniversity.edu/affiliated-colleges/', 'N/A', 'Arts ,Commerce, Education (B.Ed D.El.Ed)', 'Arts ,Commerce ,Education', 'Library, Reading Room', 'Women’s college faculty||', 'Merit based', '68ce3486f2394_cj22.jpg,68ce3486f2893_cj23.jpg', 'Merit based scholarships ', '', '', '', 'Sports', 'Cafeteria', 'Disabled Friendly', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historical_place`
--

CREATE TABLE `historical_place` (
  `place_id` int(11) NOT NULL,
  `img_src` varchar(255) DEFAULT NULL,
  `place_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historical_place`
--

INSERT INTO `historical_place` (`place_id`, `img_src`, `place_name`, `description`, `category`, `address`) VALUES
(1, 'place1.jpg', 'Gir National Park', 'National park famous for Asiatic lions and diverse wildlife.', 'Wildlife Sanctuary', 'Gir Somnath, Gujarat'),
(2, 'place2.jpg', 'Dwarkadhish Temple', 'Ancient Hindu temple dedicated to Lord Krishna, major pilgrimage site.', 'Temple', 'Dwarka, Gujarat'),
(3, 'place3.jpg', 'Somnath Temple', 'One of the twelve Jyotirlingas, reconstructed temple with sea-facing view.', 'Temple', 'Somnath, Gujarat'),
(4, 'place4.jpg', 'Velavadar Blackbuck National Park', 'Grassland reserve famous for blackbucks and migratory birds.', 'Wildlife Sanctuary', 'Bhavnagar, Gujarat'),
(5, 'place5.jpg', 'Trinetreshwar mahadev temple', 'Historic Shiva temple, famous for the Tarnetar fair and cultural heritage.', 'Temple', 'Tarnetar, surendranagar, gujarat');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `emergency` varchar(100) DEFAULT NULL,
  `visiting_hours` varchar(100) DEFAULT NULL,
  `departments` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `doctors` text DEFAULT NULL,
  `beds_general` int(11) DEFAULT NULL,
  `beds_icu` int(11) DEFAULT NULL,
  `beds_private` int(11) DEFAULT NULL,
  `laboratory` varchar(20) DEFAULT NULL,
  `diagnostics` varchar(20) DEFAULT NULL,
  `pharmacy` varchar(20) DEFAULT NULL,
  `ambulance` varchar(20) DEFAULT NULL,
  `wheelchair_accessible` varchar(30) DEFAULT NULL,
  `insurance` varchar(10) DEFAULT NULL,
  `history` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `emergency_procedures` text DEFAULT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `cat_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `name`, `img`, `address`, `contact`, `email`, `opening_hours`, `emergency`, `visiting_hours`, `departments`, `services`, `doctors`, `beds_general`, `beds_icu`, `beds_private`, `laboratory`, `diagnostics`, `pharmacy`, `ambulance`, `wheelchair_accessible`, `insurance`, `history`, `gallery`, `emergency_procedures`, `city_name`, `cat_name`, `user_email`) VALUES
(1, 'Aayush Hospitals, Junagadh', '68cd6cf6c9f79_AHJ1.jpg', 'Junagadh, Gujarat', '+91 75750 88885', 'info@aayushhospitals.org', '24/7', 'Yes', '9 AM TO 8 PM', 'Cardiology,Orthopedics,Surgery', 'Multispecialty hospital', 'Puvish D Vaghasiya|Cardiolist|MBBS', 50, 10, 10, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd6cf6ca112_AHJ2.JPG,68cd6cf6ca2d7_AHJ3.jpg', '+91 75750 88885', 'Junagadh', 'Hospital', ''),
(2, 'GMERS Medical College & Hospital, Junagadh', '68cd6fd263c61_GMERS1.jpg', 'Near Majevdi Gate, Mullawada, Junagadh, Gujarat', '+91 0285-2654503', 'deangmersjunagadh@gmail.com', '24/7', 'Yes', '9:00AM to 6:00PM', 'Teaching,hospital departments', 'General and specialized services', 'Dr. Hanmant s. Amane|Pharmacology|MD Pharmacology', 56, 23, 12, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd70035afa4_GMERS2.jpg,68cd70035b191_GMERS3.jpg', '+91 0285-2654503', 'Junagadh', 'Hospital', ''),
(3, 'Life Care Hospital, Junagadh', '68cd730fadb51_HJ31.jpg', 'Lal Bahadur Shastri Society, St road, Junagadh', '07947104100', 'lifecarehospital@gmail.com', '24/7', 'Yes', '9:00AM to 6:00PM', 'General hospital', 'Basic services', 'Dr. K K Bakhlakiya|General|MBBS', 55, 15, 10, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7214eeba4_HJ3-1.jpg,68cd7214eedc2_hj3-2.jpg', '07947104100', 'Junagadh', 'Hospital', ''),
(4, 'Aayush Hospitals, Rajkot', '68cd76fe60cc2_hr21.jpg', 'Rajkot, Gujarat', '+91 75750 88885', 'info@aayushhospitals.org', '24/7', 'Yes', '9:00AM to 6:00PM', 'Multispecialty', 'General & emergency services', 'Dr. Ramesh V Kachhadia|General Physician|MBBS', 248, 86, 15, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd76fe60f55_hr22.jpg,68cd76fe6119a_hr23.jpg', '+91 75750 88885', 'Rajkot', 'Hospital', ''),
(5, 'Sterling Hospital, Rajkot', '68cd745c6caa1_HR11.jpg', 'Plot No 251, Opp Gandhigram Police Station, Raiya Circle, Rajkot', '9898987878', 'info@sterlinghospitals.com', '24/7', 'Yes', '9:00AM to 6:00PM', 'Emergency,Medicine,ICU', 'Critical care & general services', 'Dr. Gaurang Vaghani|Pharmacology|MBBS', 60, 25, 10, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd745c6ce11_hr12.jpg,68cd745c6d110_hr13.jpg', '9898987878', 'Rajkot', 'Hospital', ''),
(6, 'P.D.U. Civil Hospital, Rajkot', '68cd780c8eb80_hr31.jpg', 'Civil Hospital Campus, Jamnagar Road, Rajkot', '+91 281 2458323', 'deanrajkot@yahoo.co.in', '24/7', 'Yes', '10:00 AM To 9:00 PM', 'Teaching hospital departments', 'Public hospital services', 'Dr. Bharti K. Patel|Dermatology|M. D. Dermatology', 56, 32, 12, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd780c8ee1f_hr32.jpg,68cd780c8f080_hr33.jpg', '+91 281 2458323', 'Rajkot', 'Hospital', ''),
(7, 'Shantabaa Medical College & General Hospital, Amreli', '68cd797365338_ha11.jpg', 'Civil Hospital Campus, Lathi Road, Amreli', '02792 230240', 'info@smcgh.edu.in', '24/7', 'Yes', '9:00AM to 8:00PM', 'Teaching hospital', 'General services', 'Mr. Vasantbhai Haribhai Gajera|Pharmacology|Pharmacologiest', 65, 35, 11, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7973656d2_ha12.jpg,68cd797365974_ha13.jpg', '02792 230240', 'Amreli', 'Hospital', ''),
(8, 'Netra Chikitsa Trust Hospital, Amreli', '68cd7a827a1d3_ha21.jpg', 'Chital Road, Amreli', '+91-9978966629', 'dr.arpanjani@gmail.com', '24/7', 'Yes', '9:00AM to 8:00PM', 'Ayurvedic specialities', 'Medical & surgical', 'Meet A Makwana|General Physician|MBBS', 65, 25, 8, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7a827a4f8_ha22.jpg,68cd7a827a998_ha23.jpg', '+91-9978966629', 'Amreli', 'Hospital', ''),
(9, 'Aastha Hospital, Amreli', '68cd7bcbab286_ha31.jpg', 'Opp Sukhnath Temple, BM Chowk, Liliya Road, Amreli', '+912792222113', 'aasthahospital@gmail.com', '24/7', 'Yes', '9:00AM to 8:00PM', 'General hospital', 'Basic healthcare', 'Dr. Ashok Parmar|Cardiolist|MBBS', 68, 24, 7, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7bcbab55f_ha32.jpg,68cd7bcbab7d6_ha33.jpg', '+912792222113', 'Amreli', 'Hospital', ''),
(10, 'HCG Hospital, Bhavnagar', '68cd7d4004cd8_hb11.jpg', 'Meghani Circle, Bhavnagar', '7406499999', 'hcghospital@gmail.com', '24/7', 'Yes', '9:00AM to 11:PM', 'Multispecialty', 'General & oncology', 'Dr Manish A Pansuriya|General|MBBS', 100, 31, 9, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7d400504e_hb12.jpg,68cd7d4005508_hb13.jpg', '7406499999', 'Bhavnagar', 'Hospital', ''),
(11, 'Civil Hospital, Bhavnagar', '68cd7e00e0298_hb21.jpg', 'Jail Road, Bhavnagar', '+91 278 2423250', 'contact@sirthospital.org', '24/7', 'Yes', '9:00AM to 8:00PM', 'Teaching hospital', 'Public hospital services', 'Dr Nikunj V Ramani|General|MBBS', 50, 10, 10, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7e00e05ff_hb22.jpg,68cd7e00e0885_hb23.jpg', '+91 278 2423250', 'Bhavnagar', 'Hospital', ''),
(12, 'Saibaba Hospital, Bhavnagar', '68cd7ef23260d_hb31.jpg', 'Near Meghani Circle, Bhavnagar', '+917990943427', 'saibaba@gmail.com', '24/7', 'Yes', '700:AM To 11:00PM', 'General hospital', 'Basic healthcare', 'Dr Vijay D Bhaliya|General|MBBS', 68, 21, 11, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd7ef232920_hb32.jpg,68cd7ef232c55_hb33.jpg', '+917990943427', 'Bhavnagar', 'Hospital', ''),
(13, 'Dr. Sakhiya Hospital, Jetpur', '68cd7fcf8f4c3_hj11.jpg', 'Navagadh Road, Jetpur', '+91 0285-2654503', 'sh@gmail.com', '24/7', 'Yes', '700:AM To 11:00PM', 'General healthcare', 'Basic hospital services', '', 56, 21, 11, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'No', 'N/A', '68cd7fcf8f828_hj12.jpg,68cd7fcf8fb5c_hj13.jpg', '+91 0285-2654503', 'Jetpur', 'Hospital', ''),
(14, 'Parmeshwar Hospital, Jetpur', '68cd82675ee10_hj21.jpg', 'Kanakiya Plot, near ICICI Bank, Jetpur', '+916355205880', 'phj@gmail.com', '24/7', 'Yes', '6:00AM To 11:00PM', 'General skincare', 'Basic healthcare services', 'Dr Krishna G Viradiya|Skin Specialist|MBBS', 61, 24, 10, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd80e625a43_hj22.jpg,68cd80e625d44_hj23.jpg', '+916355205880', 'Jetpur', 'Hospital', ''),
(15, 'HCG Hospital (Branch), Jetpur', '68cd8203b4503_hj31.jpg', '150 Feet Ring Road, serving Jetpur area', '+91 0285-2654503', 'hcg@gmail.com', '24/7', 'Yes', '10:00 AM To 9:00 PM', 'Multispecialty', 'General healthcare', 'Dr Manish K Pipaliya|General Physician|Pharmacologiest', 78, 21, 11, 'Laboratory', 'Diagnostics', 'Pharmacy', 'Ambulance', 'Wheelchair Accessible', 'Yes', 'N/A', '68cd8203b4ae5_hj32.jpg,68cd8203b4d55_hj33.jpg', '+91 0285-2654503', 'Jetpur', 'Hospital', '');

-- --------------------------------------------------------

--
-- Table structure for table `h_p`
--

CREATE TABLE `h_p` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(50) DEFAULT NULL,
  `opening_hours` varchar(90) DEFAULT NULL,
  `ticket_info` varchar(200) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `guides` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `hostel` varchar(50) DEFAULT NULL,
  `transport` varchar(50) DEFAULT NULL,
  `library` varchar(50) DEFAULT NULL,
  `sports` varchar(50) DEFAULT NULL,
  `cafeteria` varchar(50) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `h_p`
--

INSERT INTO `h_p` (`id`, `name`, `img`, `address`, `city_name`, `cat_name`, `contact`, `email`, `website`, `established`, `opening_hours`, `ticket_info`, `features`, `guides`, `gallery`, `hostel`, `transport`, `library`, `sports`, `cafeteria`, `disabled_friendly`, `created_at`, `user_email`) VALUES
(1, 'Uparkot Fort', '68d24fbf56cc3_adownload.jpg', 'Uparkot Fort, Junagadh, Gujarat', 'Junagadh', 'HistoricalPlace', '+91-8735047350', 'info@uparkotfort.in', 'https://uparkotfort.in/', '319 BC', '08:00 - 18:00', '₹50 Indian, ₹200 Foreigners', 'Ancient fort with stepwells, caves and ramparts.', 'Guided tours daily 10 AM, 3 PM', '68d24fbf571d5_download (1).jpg,68d24fbf5748a_download (2).jpg,68d24fbf5f541_download (3).jpg,68d24fbf6025d_download (4).jpg', 'Help Desk', '', '', 'Drinking Water Stations', 'Seating Areas & Rest Zones', '', '2025-09-23 01:21:42', 'admin@uparkotfort.in'),
(2, 'Mahabat Maqbara', '68d24fde9e554_download (5).jpg', 'Near Taleti, Junagadh City Centre', 'Junagadh', 'HistoricalPlace', '+91-9825012345', 'contact@mahabatmaqbara.org', 'https://mahabatmaqbara.org/', '1892', '09:00 - 18:00', 'Free entry', 'Indo-Islamic mausoleum with ornate architecture.', 'Local guides available at gate', '68d2586bd53d5_download (6).jpg,68d2586bd56b7_download (7).jpg,68d2586bd597b_download (8).jpg,68d2586bdcac1_download (9).jpg', 'Help Desk', '', 'Restrooms & Toilets', 'Drinking Water Stations', '', 'First Aid & Medical Assistance', '2025-09-23 01:21:42', 'admin@mahabatmaqbara.org'),
(3, 'Watson Museum', '68d2504d92a0a_download (10).jpg', 'Jubilee Garden, Rajkot', 'Rajkot', 'HistoricalPlace', '+91-2812456789', 'info@watsonmuseum.in', 'https://watsonmuseum.in/', '1888', '10:00 - 17:30', '₹20 Indians, ₹150 Foreigners', 'Museum with sculptures, coins and ethnographic collections.', 'Guided tours at 11 AM and 2 PM', '68d24ffd4f475_download (11).jpg,68d24ffd4f75a_download (12).jpg,68d24ffd58377_download (13).jpg,68d24ffd5daf9_download (14).jpg', 'Help Desk', 'Parking Area', 'Restrooms & Toilets', 'Drinking Water Stations', '', 'First Aid & Medical Assistance', '2025-09-23 01:21:42', 'curator@watsonmuseum.in'),
(4, 'Kaba Gandhi No Delo', '68d2506ddda15_download (40).jpg', 'Gheekanta Road, Rajkot', 'Rajkot', 'HistoricalPlace', '+91-2812233445', 'visit@kabagandhi.org', 'https://kabagandhi.org/', '1880', '09:00 - 18:00', 'Free entry', 'Childhood home of Mahatma Gandhi, now a museum.', 'Guided educational tours daily', '68d2506ddebb1_download (41).jpg,68d2506ddee85_download (42).jpg,68d2506de6da5_download (43).jpg,68d2506de861a_download (44).jpg', '', '', '', '', '', '', '2025-09-23 01:21:42', 'info@kabagandhi.org'),
(5, 'Lathi Palace (Rajmahal)', '68d250a42c837_download (15).jpg', 'Lathi, Amreli District', 'Amreli', 'HistoricalPlace', '+91-2792256789', 'info@lathipalace.in', 'https://lathipalace.in/', '1895', '09:00 - 17:00', '₹30 Indians, ₹100 Foreigners', 'Historic palace associated with poet Kalapi.', 'Local guides available', '68d250a42cc09_download (16).jpg,68d250a42cf4f_download (17).jpg,68d250a434812_download (18).jpg,68d250a43682d_download (19).jpg', 'Help Desk', '', 'Restrooms & Toilets', '', 'Seating Areas & Rest Zones', 'First Aid & Medical Assistance', '2025-09-23 01:21:42', 'heritage@lathipalace.in'),
(6, 'Trimandir Amreli', '68d250f0521e9_download (20).jpg', 'Amreli City, Gujarat', 'Amreli', 'HistoricalPlace', '+91-2792234567', 'contact@trimandiramreli.org', 'https://trimandiramreli.org/', '2005', '06:00 - 20:00', 'Free entry', 'Spiritual complex with Jain, Shaiva and Vaishnava shrines.', 'Guided by temple volunteers', '68d250f05245e_download (21).jpg,68d250f052600_download (22).jpg,68d250f052b1a_download (23).jpg,68d250f052d02_download (24).jpg', 'Help Desk', '', '', 'Drinking Water Stations', 'Seating Areas & Rest Zones', '', '2025-09-23 01:21:42', 'admin@trimandiramreli.org'),
(7, 'Takhteshwar Temple', '68d251109bb4b_download (25).jpg', 'Takhteshwar Hill, Bhavnagar', 'Bhavnagar', 'HistoricalPlace', '+91-2782521234', 'info@takhteshwartemple.in', 'https://takhteshwartemple.in/', '1893', '06:00 - 20:00', 'Free entry', 'Hilltop Shiva temple with panoramic city views.', 'Temple priests available', '68d251109c142_download (26).jpg,68d251109c4e7_download (27).jpg,68d25110a36bd_download (28).jpg,68d25110a548f_download (29).jpg', '', '', '', 'Drinking Water Stations', 'Seating Areas & Rest Zones', '', '2025-09-23 01:21:42', 'contact@takhteshwartemple.in'),
(8, 'Nilambag Palace', '68d2513d0941d_download (30).jpg', 'Nilambag Circle, Bhavnagar', 'Bhavnagar', 'HistoricalPlace', '+91-2782424241', 'stay@nilambagpalace.com', 'https://nilambagpalace.com/', '1879', '24x7 (Hotel Guests)', 'Entry with booking', 'Royal heritage hotel with palace architecture.', 'Hotel staff conduct tours', '68d2513d0989a_download (31).jpg,68d2513d09bf4_download (32).jpg,68d2513d10def_download (33).jpg,68d2513d130fd_download (34).jpg', 'Help Desk', 'Parking Area', '', 'Drinking Water Stations', 'Seating Areas & Rest Zones', 'First Aid & Medical Assistance', '2025-09-23 01:21:42', 'admin@nilambagpalace.com'),
(9, 'Khambhalida Caves', '68d251724ead8_download (35).jpg', 'Khambhalida village, near Jetpur', 'Jetpur', 'HistoricalPlace', '+91-2812765432', 'info@khambhalidacaves.in', 'https://khambhalidacaves.in/', '4th Century', '08:00 - 18:00', 'Free entry', 'Ancient Buddhist caves carved in limestone.', 'Guides from ASI available', '68d251724ee66_download (36).jpg,68d251724f179_download (37).jpg,68d2517258189_download (38).jpg,68d251725b952_download (39).jpg', '', '', '', 'Drinking Water Stations', 'Seating Areas & Rest Zones', '', '2025-09-23 01:21:42', 'admin@khambhalidacaves.in'),
(10, 'Jetpur Palace Grounds', '68d251911c43e_download (45).jpg', 'Jetpur City Centre', 'Jetpur', 'HistoricalPlace', '+91-2812789999', 'contact@jetpurpalace.in', 'https://jetpurpalace.in/', '1901', '09:00 - 17:00', '₹20 Indians, ₹80 Foreigners', 'Historic palace ground used for events and fairs.', 'Local municipality guides tours', '68d251911c7f2_download (46).jpg,68d251911cacf_download (47).jpg,68d2519124f19_download (48).jpg,68d2519128544_download (49).jpg', 'Help Desk', '', '', 'Drinking Water Stations', 'Seating Areas & Rest Zones', 'First Aid & Medical Assistance', '2025-09-23 01:21:42', 'info@jetpurpalace.in');

-- --------------------------------------------------------

--
-- Table structure for table `pgs`
--

CREATE TABLE `pgs` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(12) DEFAULT NULL,
  `rooms` varchar(60) DEFAULT NULL,
  `room_types` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `faculty` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `scholarships` text DEFAULT NULL,
  `hostel` varchar(10) DEFAULT NULL,
  `transport` varchar(10) DEFAULT NULL,
  `library` varchar(10) DEFAULT NULL,
  `sports` varchar(10) DEFAULT NULL,
  `cafeteria` varchar(10) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pgs`
--

INSERT INTO `pgs` (`id`, `name`, `img`, `address`, `city_name`, `cat_name`, `contact`, `email`, `website`, `established`, `rooms`, `room_types`, `facilities`, `faculty`, `admission_process`, `gallery`, `scholarships`, `hostel`, `transport`, `library`, `sports`, `cafeteria`, `disabled_friendly`, `created_at`, `user_email`) VALUES
(1, 'RaadheMeera Girls PG', '68ce3d4b55ebf_pr11.jpg', 'Gopal Nagar, Bhakti Nagar, Rajkot', 'Rajkot', 'PG', 'N/A', 'radhameerapg@gmail.com', 'https://housing.com/paying-guests/18325254-raadhemeera-girls-pg-in-bhakti-nagar-rajkot', 'N/A', '21', 'Single,Double,3 Sharing,AC,Non AC', 'Swimmingpool', 'N/A||', 'Contact via Housing.com listing (enquire on page)', '68ce3d4b560f7_pr12.jpg,68ce3d4b562c2_pr13.jpg', 'Not Available', '', '', '', '', '', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(2, 'Natural P G Homes', '68ce43d7769bf_pr21.jpg', 'Opposite R K University, Kasturbadham, Rajkot - 360020', 'Rajkot', 'PG', '9825482650', 'naturalpghomes@yahoo.com', 'http://www.naturalpghomes.com/', 'N/A', '23', 'Single,Double,3 Sharing,AC,Non AC', 'WiFi ,Regular meals ,Housekeeping, Security ,Laundry ,Furnished', 'N/A||', 'Call / Website enquiry', '68ce43d776d0d_pr22.jpg,68ce43d777461_pr23.jpg', 'Not Available', 'Hostel', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(3, 'OM Girls PG & Boys Hostels', '68ce44cc27de2_pj11.jpg', 'Near Alfa School-3, Junagadh City, Junagadh - 362001', 'Junagadh', 'PG', 'Show Number on Justdial (enquire)', 'ompgjunagadh@gmail.com', 'https://www.justdial.com/Junagadh/Paying-Guest-Accommodations/nct-10934649', 'N/A', '31', 'Single', 'Meals, Security, Laundry, Proximity to colleges', 'N/A||', 'Call / Justdial enquiry', '68ce44cc28139_pj12.jpg,68ce44cc28435_pj13.jpg', 'Not Available', 'Hostel', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(4, 'Shivalay Boys Hostel & PG', '68ce45e79097b_pj21.jpg', 'Khamdhrol area / Zanzarda Road, Junagadh', 'Junagadh', 'PG', '+91 0285-2654503', 'shivalaypg@gmail.com', 'https://www.magicbricks.com/pg-in-junagadh-pppfr', 'N/A', '24', 'Single,Double,3 Sharing,AC,Non AC', 'Clean rooms, Mess, Security, Near colleges', 'N/A||', 'Contact via Magicbricks listing', '68ce45e790c83_pj22.jpg,68ce45e790f52_pj23.jpg', 'Not Available', 'Hostel', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(5, 'Comfortnest PG', '68ce46af841bc_pb11.jpg', 'Ghogha Road / Bhavnagar central area', 'Bhavnagar', 'PG', '+916355205880', 'comfirtnestpg@gmail.com', 'https://www.magicbricks.com/pg-in-bhavnagar-pppfr', 'N/A', '16', 'Single,Double,Non AC', 'Furnished rooms, WiFi, Meals, Housekeeping, CCTV, Security, Near colleges', 'N/A||', 'Online enquiry / phone via listing', '68ce46db9cbc1_pb12.jpg,68ce46db9cec2_pb13.jpg', 'Not Available', 'Hostel', 'Transport', '', '', '', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(6, 'Shree Griham Girls Hostel', '68ce47576c08a_pb21.jpg', 'Kaliyabid / Mahavir Nagar area, Bhavnagar - 364002', 'Bhavnagar', 'PG', '08460387348', 'grihamgirlshostel@gmail.com', 'https://www.justdial.com/Bhavnagar/Paying-Guest-Accommodations/nct-10934649', 'N/A', '33', 'Single,Double,3 Sharing,AC,Non AC', 'Mess, Security, Proximity to colleges, Furnished rooms', 'N/A||', 'Call / Justdial enquiry', '', 'Not Available', 'Hostel', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(7, 'Achievers Girls PG & Hostel', '68ce480b3a499_pa11.jpg', 'Munjka / Keriya Road area, Amreli - 365601', 'Amreli', 'PG', '+91 99138 69626', 'achieversgirlspg@gmail.com', 'https://www.olx.in/amreli_g4058678/pg-guest-houses_c1449', 'N/A', '28', 'Single,Double,3 Sharing,AC,Non AC', 'Meals, Security, Laundry (as listed)', 'N/A||', 'Phone / OLX enquiry', '68ce4821f2eb0_pa12.jpg,68ce4821f3296_pa13.jpg', 'Not Available', 'Hostel', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(8, 'Sanskar Hostel & PG Center', '68ce48b622ba2_pa21.jpg', 'Station Road, Amreli - 365601', 'Amreli', 'PG', '07411843483', 'sanskarhostel@gmail.com', 'https://www.justdial.com/Amreli/Paying-Guest-Accommodations-For-Women/nct-11273561', 'N/A', '21', 'Single,Double,3 Sharing,AC,Non AC', 'Meals, Security, Laundry service', 'N/A||', 'Call / Visit', '68ce48b62301d_pa22.jpg,68ce48b623412_pa23.jpg', 'Not Available', 'Hostel', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(9, 'Jay Residency PG', '68ce4949d7e8f_pj11.jpg', 'Near Railway Crossing / Bus Stand, Jetpur - 360370', 'Jetpur', 'PG', '+91 9106363739', 'jayresidency@gmail.com', 'https://www.justdial.com/Rajkot/Paying-Guest-Accommodations-in-Jetpur/nct-10934649', 'N/A', '28', 'Single,Double,AC,Non AC', 'Meals, WiFi (varies), Security', 'N/A||', 'Contact owner / listing', '68ce4949d819f_pj12.jpg,68ce4949d842b_pj13.jpg', 'Not Available', 'Hostel', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', '2025-09-19 23:48:42', NULL),
(10, 'Sandipani Boy Hostel', '68ce49e4eefea_pj21.jpg', 'Near Bus Stand, Jetpur - 360370', 'Jetpur', 'PG', '+91 74900 40595', 'sandipanihostel@gmail.com', 'https://www.justdial.com/Rajkot/Paying-Guest-Accommodations-in-Jetpur/nct-10934649', 'N/A', '25', 'Single,Double,Non AC', 'Mess, Security, Near colleges', 'N/A||', 'Call / Visit', '68ce49e4ef2d6_pj22.jpg,68ce49e4ef9e4_pj23.jpg', 'Not Available', 'Hostel', 'Transport', '', '', '', '', '2025-09-19 23:48:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `closed_on` varchar(80) DEFAULT NULL,
  `reservation_required` varchar(20) DEFAULT NULL,
  `special_offers` varchar(255) DEFAULT NULL,
  `cuisines` text DEFAULT NULL,
  `menu` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `chefs` text DEFAULT NULL,
  `wifi` varchar(20) DEFAULT NULL,
  `parking` varchar(20) DEFAULT NULL,
  `outdoor_seating` varchar(20) DEFAULT NULL,
  `live_music` varchar(20) DEFAULT NULL,
  `bar` varchar(20) DEFAULT NULL,
  `kids_friendly` varchar(20) DEFAULT NULL,
  `pet_friendly` varchar(20) DEFAULT NULL,
  `wheelchair_accessible` varchar(50) DEFAULT NULL,
  `payment_options` varchar(120) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `img`, `address`, `city_name`, `cat_name`, `contact`, `email`, `website`, `opening_hours`, `closed_on`, `reservation_required`, `special_offers`, `cuisines`, `menu`, `services`, `chefs`, `wifi`, `parking`, `outdoor_seating`, `live_music`, `bar`, `kids_friendly`, `pet_friendly`, `wheelchair_accessible`, `payment_options`, `description`, `gallery`, `created_at`, `user_email`) VALUES
(1, 'Sadhana Restaurant', '68cd906f82f41_rj11.jpg', 'Star Platinum Complex, Vanthali Road, Madhuram Bus Stop, Junagadh - 362001', 'Junagadh', 'Restaurant', '+91 99138 69626', 'sadhanarest@gmail.com', 'https://www.zomato.com/junagadh/sadhana-restaurant-moti-palace-township', '12:00 pm - 10:30 pm', 'Everyday Open', 'No', 'In Festivals Only', 'GujaratiNorth IndianSouth IndianChinese', 'Dosa', 'Dine-in&#10;Takeaway&#10;Delivery', 'Rajesh Patel|Kathiyawadi Cuisine|12', 'Wi-Fi', 'Parking', 'Outdoor Seating', '', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular local multi-cuisine restaurant in Junagadh.', '68cd8f921b4cf_rj12.jpg,68cd8f921b8a5_rj13.jpg', '2025-09-19 10:57:17', NULL),
(2, 'Sankalp Restaurant (Junagadh)', '68cd930c2924b_rj21.jpg', 'Near Zanzarda Chowkdi, Junagadh', 'Junagadh', 'Restaurant', '+91 74900 40595', 'deardhavalpatel@gmail.com', 'https://sankalprestaurants.com/south-indian-restaurant-near-me/junagadh/', ' 11:00 AM to 11:00 PM', 'Everyday Open', 'No', 'In Festivals Only', 'SouthIndianVegetarian', 'Mendu Vada', 'Dine-in&#10;Takeaway', 'Hiren Joshi|Street Food (Sev Usal, Dabeli)|8', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular South-Indian casual dining in Junagadh.', '68cd930c29b81_rj22.jpg,68cd930c2a4b0_rj23.jpg', '2025-09-19 10:57:17', NULL),
(3, 'Patel Restaurant & Banquet Hall', '68cd95562c89d_rj31.jpg', 'Essel Dreamplex Complex, Opp. Zoo, Junagadh - 362001', 'Junagadh', 'Restaurant', '+91 98989 96020', 'patelrest@gmail.com', 'https://www.tripadvisor.com/Restaurant_Review-g303879-d8551650-Reviews-Patel_Restaurant_Banquet_Hall-Junagadh_Junagadh_District_Gujarat.html', '10:30 AM To 11:00 PM', 'Every Day Open', 'Yes (banquet)', 'In Festivals Only', 'Indian Gujarati', 'Kathiyavadi Dish', 'Banquet,Catering,Dine-in', 'Mehul Shah|Gujarati Thali & Farsan|12', 'Wi-Fi', 'Parking', '', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Banquet hall and restaurant suitable for events.', '68cd95562cc85_rj32.jpg,68cd95562d070_rj33.jpg', '2025-09-19 10:57:17', NULL),
(4, 'TGT - The Grand Thakar', '68cd98277934b_rj22.jpg', 'Jubilee Chowk, Kothi Compound, Rajkot', 'Rajkot', 'Restaurant', '06355205880', 'tgt@gmail.com', 'https://www.zomato.com/rajkot/tgt-the-grand-thakar-jubilee-chowk', '10:30 AM To 11:00 PM', 'Everyday Open', 'No', 'In Festivals Only', 'Gujarati North Indian Chinese', 'Paneer Butter Masala', 'Dine-in,Takeaway', 'Kiran Desai|Sweets & Desserts (Mohanthal)|10', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, Card, UPI', 'Popular family restaurant in Rajkot.', '68cd982779742_rr12.jpg,68cd982779b71_rr13.jpg', '2025-09-19 10:57:17', NULL),
(5, 'Wok On Fire (Rajkot)', '68cd9c7cac437_rr21.jpg', '150 Feet Ring Road, Rajkot', 'Rajkot', 'Restaurant', '07942683066', 'wofrest@gmail.com', 'https://www.zomato.com/rajkot/wok-on-fire', '11:00 am - 11:00 pm', 'Everyday Open', 'No', 'In Festivals Only', 'Asian Chinese Thai', 'Manchurian,Noodles', 'Dine-in,Delivery', 'Alpesh Trivedi|Jain Food (No Onion/Garlic)|14', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, Card, UPI', 'Asian cuisine and popular chain in Rajkot.', '68cd9c7cac797_rr22.jpg,68cd9c7cacb8a_rr23.jpg', '2025-09-19 10:57:17', NULL),
(6, 'The Blue Oven', '68cd9d5e446c0_rr33.jpg', 'Kotecha Nagar, Rajkot', 'Rajkot', 'Restaurant', '07947135610', 'theblueoven@gmail.com', 'https://www.zomato.com/rajkot/the-blue-oven', '10:00 am - 10:00 pm', 'Everyday Open', 'No', 'In Festivals Only', 'Pizza Italian Fast Food', 'Pasta,Pizza', 'Dine-in,Delivery', 'Snehal Mehta|Fusion Gujarati-Continental|9', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, Card, UPI', 'Popular pizza and fast-food spot in Rajkot.', '68cd9d5e44b94_rr31.jpg,68cd9d5e45100_rr32.jpg', '2025-09-19 10:57:17', NULL),
(7, 'Radhika Garden Restaurant & Caterers', '68cd9efc8adb9_ra11.jpg', 'Near Gurudatt Petrol Pump, Station Road, Amreli', 'Amreli', 'Restaurant', '07947117050', 'radhikagardenrest@gmail.com', 'N/A', 'Open until 11:30 pm', 'Everyday Open', 'Yes (events)', 'In Festivals Only', 'Indian Kathiyawadi', 'Sev Tameta Nu Saak Ne Bajra No Rotlo', 'DiningCatering Events', 'Bhavesh Modi|Traditional Rotla & Shaak|8', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', 'Bar', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular garden restaurant and caterers in Amreli.', '68cd9efc8b1a2_ra12.jpg,68cd9efc8b9c5_ra13.jpg', '2025-09-19 10:57:17', NULL),
(8, 'Checkmate Family Restaurant', '68cd9ffc6a37a_ra23.jpg', 'Keriya Road Bypass Chowkdi, Amreli', 'Amreli', 'Restaurant', '07041396514', 'checkmaterest@gmail.com', 'N/A', 'Open until 11:00 pm', 'Everyday Open', 'No', 'In Festivals Only', 'Family Dining Indian', 'Pav Bhaji,Vada Pav', 'Dine-in,Takeaway', 'Chirag Bhatt|Kathiyawadi BBQ & Grill|7', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', 'Bar', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Family restaurant popular in Amreli.', '68cd9ffc6a659_ra21.jpg,68cd9ffc6ab05_ra22.jpg', '2025-09-19 10:57:17', NULL),
(9, 'Thakar Thal', '68cda10303054_ra31.jpg', 'Amreli (Tripadvisor listing)', 'Amreli', 'Restaurant', '+91 75750 88885', 'thakarthalrest@gmail.com', 'https://www.tripadvisor.ie/Restaurant_Review-g1486563-Amreli_Amreli_District_Gujarat.html', '9:00AM to 11:00PM', 'Everyday Open', 'No', 'In Festivals Only', 'Indian Thali', 'Gujarati Thali', 'Dine-in', '', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Local thali restaurant popular in Amreli.', '68cda10303346_ra32.jpg,68cda1030363f_ra33.jpg', '2025-09-19 10:57:17', NULL),
(10, 'Mango - World Cuisine Restaurant', '68cda22364e0c_rb11.jpg', 'Shop No. 103/104, Imperial Arc, Waghawadi Road, Bhavnagar - 364001', 'Bhavnagar', 'Restaurant', '+91 99138 69626', 'mangoworldcuisine@gmail.com', 'https://www.tripadvisor.com/Restaurant_Review-g679050-d26106834-Reviews-Mango_world_cuisine_restaurant-Bhavnagar_Bhavnagar_District_Gujarat.html', ' 11:00 AM to 11:00 PM', 'Everyday Open', 'No', 'In Festivals Only', 'Multi-cuisine World Cuisine', 'Spaghetti Neapolitana', 'Dine-in,Takeaway', 'Nilesh Chauhan|Surti Locho & Farsan|8', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular multi-cuisine restaurant in Bhavnagar.', '68cda223651af_rb12.jpg,68cda223654f3_rb13.jpg', '2025-09-19 10:57:17', NULL),
(11, 'RK Hotel & Garden Restaurant', '68cda3598ef93_rb21.jpg', 'Rajkot Hwy, Vartej, Bhavnagar - 364060', 'Bhavnagar', 'Restaurant', '2541010254', 'rkrest@gmail.com', 'https://www.gujjuticks.com/city/bhavnagar/restaurant', '11:00 AM To 11:30 PM', 'Everyday Open', 'Yes (events)', 'In Festivals Only', 'Local Indian', 'Dal-Dhokli , Chinese Platters', 'Dine-in,Events', 'Jignesh Parmar|Dal-Dhokli & Comfort Food|6', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Garden restaurant suitable for events.', '68cda3598f2d9_rb22.jpg,68cda3598f7bc_rb23.jpg', '2025-09-19 10:57:17', NULL),
(12, 'Rasoi Dining Hall', '68cda43204096_rb31.jpg', 'Near Madhav Jyot, Kalubha Road, Bhavnagar', 'Bhavnagar', 'Restaurant', '07947130166', 'rasoidininghall@gmail.com', 'https://www.tripadvisor.com/Restaurant_Review-g679050-d12345678-Reviews-Rasoi_Dining_Hall-Bhavnagar_Bhavnagar_District_Gujarat.html', '10:30 AM To 11:00 PM', 'Everyday Open', 'No', 'In Festivals Only', 'Kathiyawadi Gujarati', 'Khaman, Patra', 'Dining,Catering', 'Umesh Gohil|Gujarati Snacks (Khaman, Patra)|6', 'Wi-Fi', 'Parking', 'Outdoor Seating', '', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular local Kathiyawadi dining hall.', '68cda4320446a_rb32.jpg,68cda43204736_rb33.jpg', '2025-09-19 10:57:17', NULL),
(13, 'Hotel New Navjivan', '68cda4f81383b_rj13.jpg', 'Gymkhana Shopping Centre, Opposite Bus Stand, Jetpur - 360370', 'Jetpur', 'Restaurant', '+91 99138 69626', 'navjivan@gmail.com', 'https://www.justdial.com/Rajkot/Restaurants-in-Jetpur/nct-10408936', '11:00 am - 11:00 pm', 'Everyday Open', 'No', 'In Festivals Only', 'Vegetarian Indian', 'Paneer Makhni', 'Dine-in,Hotel restaurant', 'Hetal Shukla|Pickles & Chutneys|7', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', 'Bar', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular hotel restaurant near Jetpur bus stand.', '68cda4f81411c_rj11.jpg,68cda4f814620_rj12.jpg', '2025-09-19 10:57:17', NULL),
(14, 'Hotel Ankur', '68cda60578fdf_rj22.jpg', 'Near Opposite KV Sab Station, Dhareswar, Jetpur', 'Jetpur', 'Restaurant', '+91 73833 85378', 'hotelankur@gmail.com', 'https://www.justdial.com/Rajkot/Hotel-Ankur-Near-Oposite-Kv-Sab-Station-Dhareswar-Jetpur/0281PX281-X281-130401125925-J3T5_BZDET', 'Open 24 Hrs', 'Everyday Open', 'No', 'In Festivals Only', 'Gujarati Thali', 'Undhiyu', 'Dine-in,Hotel restaurant', 'Pankaj Dave|Handvo & Thepla Varieties|9', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Well-known Gujarati thali spot in Jetpur.', '68cda605793e2_rj21.jpg,68cda60579664_rj23.jpg', '2025-09-19 10:57:17', NULL),
(15, 'Hotel Utsav And Restaurant', '68cda720e55ae_rj31.jpg', 'Udhyog Nagar, Jetpur, Rajkot - 360370', 'Jetpur', 'Restaurant', '+91 79550 88885', 'utsavhotelrest@gmail.com', 'https://www.tripadvisor.com/Restaurant_Review-g2276046-d24092814-Reviews-Hotel_Utsav_and_Restaurant-Jetpur_Rajkot_District_Gujarat.html', 'Open 24 Hrs', 'Everyday Open', 'No', 'In Festivals Only', 'Multi-Cuisine Hotel Restaurant', 'Paneer Butter Masala', 'Dine-in,Hotel services', 'Dharmesh Prajapati|Street Snacks (Fafda, Jalebi)|4', 'Wi-Fi', 'Parking', 'Outdoor Seating', 'Live Music', 'Bar', 'Kids Friendly', 'Pet Friendly', 'Wheelchair Accessible', 'Cash, UPI', 'Popular hotel restaurant in Jetpur.', '68cda720e58bc_rj32.jpg,68cda720e5b3e_rj33.jpg', '2025-09-19 10:57:17', NULL),
(16, 'Eatery ', '68d3acea41b76_68cda4f814620_rj12.jpg', 'Amreli', 'Amreli', 'Restaurant', '+91 86515 05050', 'eatery12@gmail.com', 'N/A', '11:00 AM To 9:30 PM', 'Everyday Open', 'No', 'In Festivals Only', 'Fast Food', 'Pizza', 'Dine-in', 'N/A||', '', '', '', 'Live Music', '', 'Kids Friendly', 'Pet Friendly', '', 'Cash', 'Come and have some quality food with us.', '68d3acea42118_68cda223654f3_rb13.jpg,68d3acea425a3_68cda1030363f_ra33.jpg,68d3acea4b632_68cda4320446a_rb32.jpg', '2025-09-24 08:33:46', 'niragthakar@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(12) DEFAULT NULL,
  `grades` varchar(60) DEFAULT NULL,
  `departments` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `faculty` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `scholarships` text DEFAULT NULL,
  `hostel` varchar(10) DEFAULT NULL,
  `transport` varchar(10) DEFAULT NULL,
  `library` varchar(10) DEFAULT NULL,
  `sports` varchar(10) DEFAULT NULL,
  `cafeteria` varchar(10) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `img`, `address`, `city_name`, `cat_name`, `contact`, `email`, `website`, `established`, `grades`, `departments`, `facilities`, `faculty`, `admission_process`, `gallery`, `scholarships`, `hostel`, `transport`, `library`, `sports`, `cafeteria`, `disabled_friendly`, `created_at`, `user_email`) VALUES
(1, 'Podar International School, Junagadh', '68cdb072d77c7_sj11.jpg', 'Sukhpur (Opp Mahasagar Petrol Pump), Junagadh - 362310, Gujarat', 'Junagadh', 'School', '+91 63664 37861', 'admissions@podar.org', 'https://www.podareducation.org/school/junagadh', '2011', 'KG-12', 'Primary,Secondary,Senior Secondary', 'Labs,Library,Playgrounds,Smart Classrooms', 'Experienced faculty (subject-wise)||With 12+ Years of experience ', 'Online enquiry via website; standard Podar admission process', '68cdb072d79f6_sj12.jpg,68cdb072d7c84_sj13.jpg', 'Merit based', 'Hostel', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(2, 'Kendriya Vidyalaya Junagadh (KV Junagadh)', '68cdb13b80b94_sj21.jpg', 'Behind Adarsh Nivasi Shala, Bilkha Road, Junagadh - 362001', 'Junagadh', 'School', '0285-2960205', 'ppl.junagarh@kvs.gov.in', 'https://junagarh.kvs.ac.in/', '1986', '1-12 (CBSE)', 'Primary,Secondary,Higher Secondary', 'Library,Labs,Sports Ground,Computer Lab', 'KVS-certified teachers||With 15+ Years of experience ', 'KVS central admission rules; online portal', '68cdb13b80e96_sj22.jpg,68cdb13b8123f_sj23.jpg', 'Merit scholarships (contact school)', '', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(3, 'Alpha Vidhya Sankul (AVS), Junagadh', '68cdaf606db5e_sj31.jpg', 'Mendarda - Sasan Gir Rd / Vadal Road area, Junagadh', 'Junagadh', 'School', '+91 86515 05050', 'alphavidhyasankul02@gmail.com', 'https://www.alphavidhyasankul.org/', '2005', '1-12 (GSEB/Science Unit 11-12)', 'Science Unit (11-12)&#10;Primary&#10;Secondary', 'Hostel&#10;Coaching for competitive exams&#10;LabsLibrary', 'Qualified faculty including guest faculties||Wirh 10+ Years of experience ', 'Contact school for admission/enquiry', '68cdaf88eea5c_sj32.jpg,68cdaf88eed0b_sj33.jpg', 'Merit scholarships (contact school)', '', 'Transport', '', 'Sports', '', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(4, 'St. Xavier\'s School, Rajkot', '68cdb225ae19d_sr11.jpg', 'Kalawad Road, Near Wockhardt Hospital, Rajkot - 360005', 'Rajkot', 'School', '+91 87581 07225', 'stxaviersrajkot@gmail.com', 'https://stxaviersrajkot.org/', '1990', 'KG-12', 'Primary,Secondary,Senior Secondary', 'Auditorium,Labs,Library,Sports', 'Experienced teaching staff||With 8+ Years of experience ', 'School office / website enquiry', '68cdb225ae431_sr12.jpg,68cdb225ae683_sr13.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(5, 'S N Kansagra School, Rajkot', '68cdb2d137b42_sr21.jpg', 'University Road / Akashwani Quarters, Rajkot', 'Rajkot', 'School', '0281-2588391', 'kiran.bhalodia@tges.org', 'http://www.icbse.com/schools/s-n-kansagra-school/gu003', '1975', '1-12', 'Primary to Higher Secondary', 'Labs,Library,Sports', 'Qualified faculty||With 9+ Years of experience ', 'Contact school office', '68cdb2d137e06_sr22.jpg,68cdb2d137fd4_sr23.jpg', 'Merit based scholarships', 'Hostel', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(6, 'The Northstar School, Rajkot', '68cdb3a648db8_sr31.jpg', 'Kasturbadham / 2nd Ring Road / Bhavnagar Highway, Rajkot - 360020', 'Rajkot', 'School', '079 6920 8909', 'hello@northstar.edu', 'https://www.northstar.edu.in/', '2008', 'Pre-K to 12 (IGCSE/CBSE options)', 'Early Years,Primary,Secondary,International Curriculum', 'International curriculum campus,Labs,Sports,Arts', 'Internationally trained faculty||With 15+ Years of experience ', 'Online enquiry / admissions office', '68cdb3a64907d_sr32.jpg,68cdb3a649567_sr33.jpg', 'Need-based/merit (contact school)', 'Hostel', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(7, 'Shree Swaminarayan Gurukul Sec & H. Sec, Amreli', '68ce1e49133bf_sa11.jpg', 'Savarkundla / Amreli District (see schools.org.in listing)', 'Amreli', 'School', '07947426115', 'swaminarayangurukulamreli@gmail.com', 'N/A', 'N/A', '9-12', 'Secondary & Higher Secondary', 'Basic school facilities', 'Local faculty||With 7+ Years of experience ', 'Contact local school office', '68ce1e49135e2_s12.jpg,68ce1e4913728_sa13.jpg', 'Merit based scholarships ', 'Hostel', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(8, 'Abhinav Primary School, Amreli', '68ce1eda72a60_sa21.jpg', 'Shiv Krupa Complex, Nani Bazaar, Jafrabad, Amreli - 365540', 'Amreli', 'School', '02794-245631', 'abhinavprimarysch@gmaial.com', 'N/A', 'N/A', 'Primary', 'Primary', 'Basic facilities', 'Local teachers||Wirh 5+ Years of experience ', 'In-person at school', '68ce1eda72c66_sa22.jpg,68ce1eda7306d_sa23.jpg', 'Merit based scholarships ', '', '', 'Library', 'Sports', '', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(9, 'Sanskar Valley International School, Amreli', '68ce20220978b_sa31.jpg', 'Amreli (contact via AmreliOnline listing)', 'Amreli', 'School', '07942698135', 'sanskarvalleyamreli@gmail.com', 'https://www.amrelionline.in/schools/2941', 'N/A', 'KG-12', 'Primary to Higher Secondary', 'Playground,Labs,Library', 'Experienced faculty||Wirh 8+ Years of experience ', 'Contact school office', '68ce20220be2b_sa32.jpg,68ce2022153c2_sa33.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(10, 'Amarjyoti Saraswati International School (ASIS), Bhavnagar', '68ce21cd39247_sb11.jpg', 'Bhavnagar (main campus)', 'Bhavnagar', 'School', '+91 99138 69626', 'amarjyotibhavnagar@gmail.com', 'N/A', '1992', 'KG-12', 'Primary Secondary Senior Secondary', 'Labs Library Sports Auditorium', 'Qualified faculty||With 12+ Years of experience ', 'Contact school office', '68ce21cd39396_sb12.jpg,68ce21cd39499_sb13.jpg', 'Merit based scholarships', 'Hostel', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(11, 'Saint Mary\'s English School, Bhavnagar', '68ce225c4ae61_sb21.jpg', 'Shivaji Circle / Ghogha Road area, Bhavnagar - 364001', 'Bhavnagar', 'School', '+91 86515 05050', 'saintmary@gmail.com', 'N/A', 'N/A', 'KG-12', 'Primary to Higher Secondary', 'Library Labs Sports', 'Experienced faculty||Wirh 10+ Years of experience ', 'Contact school office', '68ce225c4b0fd_sb22.jpg,68ce225c4b442_sb23.jpg', 'Merit based scholarships ', 'Hostel', 'Transport', '', '', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(12, 'Shree Swaminarayan Naimisharanya School, Bhavnagar', '68ce231600bd2_sb31.jpg', 'Sidsar Road / Takhteswar area, Bhavnagar', 'Bhavnagar', 'School', '07947132910', 'swaminarayanschoolbhavnagar@gmail.com', 'N/A', 'N/A', 'KG-12', 'Primary to Higher Secondary', 'Library Sports Labs', 'Local faculty||With 8+ Years of experience ', 'Contact school', '68ce231600e4b_sb32.jpg,68ce23160110b_sb33.jpg', 'Merit based scholarships ', 'Hostel', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(13, 'St. Francis High School, Jetpur', '68ce23ac34e75_sj11.jpg', 'Navagadh Bypass Road / Near Bus Stand, Jetpur - 360370', 'Jetpur', 'School', '+91 0285-2654503', 'francishighschool@gmail.com', 'https://www.facebook.com/StFrancisHighSchoolJetpurGujIndia/', '1970s', '1-12', 'Primary Secondary', 'Library Playgrounds Labs', 'Experienced local faculty||', 'Contact school office', '68ce23ac351c8_sj12.jpg,68ce23ac354a2_sj13.jpg', 'Merit based scholarships', '', 'Transport', 'Library', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(14, 'Lions High School, Jetpur', '68ce2428e80ce_sj21.jpg', 'Jamadar Wadi / Jetpur, Rajkot District - 360370', 'Jetpur', 'School', '+91 74900 40595', 'lionsschoolJetpur@gmail.com', 'N/A', '1977', '1-10', 'Primary Secondary', 'Basic facilities Sports ground', 'Local faculty||', 'School office', '68ce2428e832e_sj22.jpg,68ce2428e852a_sj23.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', 'Cafeteria', 'Disabled Friendly', '2025-09-19 13:32:43', NULL),
(15, 'Sri Sri Academy CBSE School, Jetpur', '68ce250602a8f_sj31.jpg', 'Jetpur area (listed in Jetpur cluster)', 'Jetpur', 'School', '+91 86515 05050', 'sriacademyjetpur@gmail.com', 'N/A', 'N/A', 'KG-12 (CBSE)', 'Primary to Higher Secondary', 'Library Labs Playground', 'Qualified staff||With 12+ Years of experience ', 'Contact school office', '68ce250602bf4_sj32.jpg,68ce250602d03_sj33.jpg', 'Merit based scholarships ', '', 'Transport', '', 'Sports', '', 'Disabled Friendly', '2025-09-19 13:32:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_colleges`
--

CREATE TABLE `t_colleges` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(12) DEFAULT NULL,
  `courses` text DEFAULT NULL,
  `departments` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `faculty` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `scholarships` text DEFAULT NULL,
  `hostel` varchar(10) DEFAULT NULL,
  `transport` varchar(10) DEFAULT NULL,
  `library` varchar(10) DEFAULT NULL,
  `sports` varchar(10) DEFAULT NULL,
  `cafeteria` varchar(10) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_hospital`
--

CREATE TABLE `t_hospital` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `emergency` varchar(100) DEFAULT NULL,
  `visiting_hours` varchar(100) DEFAULT NULL,
  `departments` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `doctors` text DEFAULT NULL,
  `beds_general` int(11) DEFAULT NULL,
  `beds_icu` int(11) DEFAULT NULL,
  `beds_private` int(11) DEFAULT NULL,
  `laboratory` varchar(20) DEFAULT NULL,
  `diagnostics` varchar(20) DEFAULT NULL,
  `pharmacy` varchar(20) DEFAULT NULL,
  `ambulance` varchar(20) DEFAULT NULL,
  `wheelchair_accessible` varchar(30) DEFAULT NULL,
  `insurance` varchar(10) DEFAULT NULL,
  `history` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `emergency_procedures` text DEFAULT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `cat_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_h_p`
--

CREATE TABLE `t_h_p` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(50) DEFAULT NULL,
  `opening_hours` varchar(90) DEFAULT NULL,
  `ticket_info` varchar(200) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `guides` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `hostel` varchar(50) DEFAULT NULL,
  `transport` varchar(50) DEFAULT NULL,
  `library` varchar(50) DEFAULT NULL,
  `sports` varchar(50) DEFAULT NULL,
  `cafeteria` varchar(50) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_pgs`
--

CREATE TABLE `t_pgs` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(12) DEFAULT NULL,
  `rooms` varchar(60) DEFAULT NULL,
  `room_types` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `faculty` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `scholarships` text DEFAULT NULL,
  `hostel` varchar(10) DEFAULT NULL,
  `transport` varchar(10) DEFAULT NULL,
  `library` varchar(10) DEFAULT NULL,
  `sports` varchar(10) DEFAULT NULL,
  `cafeteria` varchar(10) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_restaurants`
--

CREATE TABLE `t_restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `closed_on` varchar(80) DEFAULT NULL,
  `reservation_required` varchar(20) DEFAULT NULL,
  `special_offers` varchar(255) DEFAULT NULL,
  `cuisines` text DEFAULT NULL,
  `menu` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `chefs` text DEFAULT NULL,
  `wifi` varchar(20) DEFAULT NULL,
  `parking` varchar(20) DEFAULT NULL,
  `outdoor_seating` varchar(20) DEFAULT NULL,
  `live_music` varchar(20) DEFAULT NULL,
  `bar` varchar(20) DEFAULT NULL,
  `kids_friendly` varchar(20) DEFAULT NULL,
  `pet_friendly` varchar(20) DEFAULT NULL,
  `wheelchair_accessible` varchar(50) DEFAULT NULL,
  `payment_options` varchar(120) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_schools`
--

CREATE TABLE `t_schools` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_name` varchar(90) DEFAULT NULL,
  `cat_name` varchar(90) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `established` varchar(12) DEFAULT NULL,
  `grades` varchar(60) DEFAULT NULL,
  `departments` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `faculty` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `scholarships` text DEFAULT NULL,
  `hostel` varchar(10) DEFAULT NULL,
  `transport` varchar(10) DEFAULT NULL,
  `library` varchar(10) DEFAULT NULL,
  `sports` varchar(10) DEFAULT NULL,
  `cafeteria` varchar(10) DEFAULT NULL,
  `disabled_friendly` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `created_at`) VALUES
(1, 'niragthakar@gmail.com', '$2y$10$wFA4U2CgSknBe7Zsht9t8OyUxdq6sPSC.JErPM5N4.FKjfFa8v4Xq', '2025-09-19 09:31:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'n', '$2y$10$N4qYxLxDBI/4h8SMCXUHlejudw6r0itGEjDPc9mSI3MBAAn40cFzS', 'admin@gmail.com', 'admin', '2025-07-17 17:59:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `unique_category_name` (`cat_name`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `unique_city_name` (`city_name`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `historical_place`
--
ALTER TABLE `historical_place`
  ADD PRIMARY KEY (`place_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `h_p`
--
ALTER TABLE `h_p`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pgs`
--
ALTER TABLE `pgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_colleges`
--
ALTER TABLE `t_colleges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_hospital`
--
ALTER TABLE `t_hospital`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_h_p`
--
ALTER TABLE `t_h_p`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pgs`
--
ALTER TABLE `t_pgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_restaurants`
--
ALTER TABLE `t_restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_schools`
--
ALTER TABLE `t_schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `historical_place`
--
ALTER TABLE `historical_place`
  MODIFY `place_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `h_p`
--
ALTER TABLE `h_p`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pgs`
--
ALTER TABLE `pgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `t_colleges`
--
ALTER TABLE `t_colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_hospital`
--
ALTER TABLE `t_hospital`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_h_p`
--
ALTER TABLE `t_h_p`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_pgs`
--
ALTER TABLE `t_pgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_restaurants`
--
ALTER TABLE `t_restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `t_schools`
--
ALTER TABLE `t_schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
