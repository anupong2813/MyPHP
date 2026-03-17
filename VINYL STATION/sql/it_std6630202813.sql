-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 17, 2026 at 05:47 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it_std6630202813`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_info` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `order_details` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `main_img` varchar(500) DEFAULT NULL,
  `slip_img` varchar(255) NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `contact_info`, `shipping_address`, `order_details`, `total_price`, `main_img`, `slip_img`, `order_date`, `status`) VALUES
(1, 'Aemamorn Sudprasong', 'Aemamorn.su@ku.th', '', 'The 1975 - Being Funny In A Foreign Language (x1)', 1.00, 'storage/uploads/pre_1b6c0265c3b6d3749af4.jpg', 'storage/slips/slip_1772869622_60581be97f.jpg', '2026-03-07 14:47:02', 'Pending'),
(2, 'พันไมล์ พันธะโคตร', '0621652036', '', 'The 1975 - Being Funny In A Foreign Language (x1)', 1.00, 'storage/uploads/pre_1b6c0265c3b6d3749af4.jpg', 'storage/slips/slip_1773419249_5a13300cb8.jpg', '2026-03-13 23:27:29', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `productname` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `category` varchar(100) DEFAULT 'Other',
  `price` decimal(7,0) NOT NULL,
  `img` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `cpu` varchar(255) DEFAULT NULL,
  `ram` varchar(100) DEFAULT NULL,
  `storage` varchar(255) DEFAULT NULL,
  `screen` varchar(255) DEFAULT NULL,
  `stock_qty` int DEFAULT '0',
  `color` varchar(100) DEFAULT NULL,
  `format` varchar(100) DEFAULT NULL,
  `released_date` varchar(100) DEFAULT NULL,
  `spotify_url` varchar(500) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `productname`, `detail`, `category`, `price`, `img`, `brand`, `cpu`, `ram`, `storage`, `screen`, `stock_qty`, `color`, `format`, `released_date`, `spotify_url`, `stock`) VALUES
(1, 'CORTIS [COLOR OUTSIDE THE LINES] 1st EP Album', 'สัมผัสเสน่ห์ทางดนตรีที่ไร้กรอบกับ 1st EP \'COLOR OUTSIDE THE LINES\' อัลบั้มนี้โดดเด่นด้วยการเบลนด์แนวเพลง Pop และ Hip-Hop เข้ากับกลิ่นอาย R&B และ Electro-trap ถือเป็นไอเทมที่แฟนเพลง Pop-Rap ต้องมีติดชั้นวาง!', 'Pop / Hip-Hop / Pop-Rap / R&B / Electro-Trap', 1400, 'uploads/pre_f0f8b9ae3b07a689f8bc.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', 'Vinyl 1LP', 'September  8 2025', 'https://open.spotify.com/album/2yMfaynthtWVAkJ5A3Kwrf?si=5iyi2DraTGmckQ72QVPF4Q', 1),
(2, 'Dominic Fike - Don\'t Forget About Me, Demos', '\"สัมผัสจุดเริ่มต้นของ Dominic Fike กับ EP เดบิวต์ที่ทลายกำแพงแนวเพลงอย่างสิ้นเชิง ตัวแผ่นอัดแน่นไปด้วยส่วนผสมที่ลงตัวของ Alternative Rock, Indie Pop และ Hip-Hop มาพร้อมซิงเกิลฮิตถล่มทลายอย่าง \'3 Nights\' เป็นงานเพลงที่มีความดิบและจริงใจเหมือนฟังเดโมชั้นดีในรูปแบบอนาล็อก\"', 'Indie Rock / Alternative Pop / Hip-Hop / Bedroom Pop', 1300, 'uploads/pre_bed96adabc98e514b459.png', NULL, NULL, NULL, NULL, NULL, 0, 'Black', 'Vinyl 1LP', 'March 29 2019', 'https://open.spotify.com/album/05jbNkYoEQdjVDHEHtg1gY?si=cMNAwfT-TjauYYocAHPnqQ', 1),
(3, 'Post Malone - F-1 TRILLION', '\"การเดินทางครั้งใหม่ของ Post Malone กับอัลบั้มแนว Country เต็มตัวชุดแรกที่ทุบสถิติไปทั่วโลก อัดแน่นไปด้วยศิลปินรับเชิญระดับตำนานและแถวหน้าของวงการอย่าง Morgan Wallen, Blake Shelton, Luke Combs และ Dolly Parton มาพร้อมซิงเกิลฮิตอันดับ 1 อย่าง \'I Had Some Help\' ในรูปแบบแผ่นคู่ (Double Vinyl) ที่ให้พลังเสียงเต็มอิ่มและควรค่าแก่การสะสม\"', 'Country / Pop', 1400, 'uploads/pre_769b881d8c902aee9097.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2 x Vinyl, LP, Album', 'August 16 2024', 'https://open.spotify.com/album/4BbsHmXEghoPPevQjPnHXx?si=D1gmYPTJT9uQixFn0YsiNg', 1),
(4, 'The weeknd - Starboy (Limited)', '\"พบกับหนึ่งในอัลบั้มที่ไอคอนิกที่สุดแห่งยุค \'Starboy\' จาก The Weeknd ในรูปแบบแผ่นเสียงคู่สีแดงใส (Translucent Red) สุดลิมิเต็ด ตัวแผ่นมาพร้อมกับปกแบบ Gatefold สวยงามและเนื้อหาเพลงแบบ Explicit ครบถ้วน โดดเด่นด้วยซาวด์ดนตรีสไตล์ R&B ผสมผสานกับ New Wave และ Synth-pop ที่ได้ร่วมงานกับตำนานอย่าง Daft Punk เป็นแผ่นที่ต้องมีติดบ้านทั้งในแง่ของคุณภาพเสียงและการจัดโชว์\"', 'R&B /  Pop / Synth-pop', 1890, 'uploads/pre_8bafeaa56c04201276e8.png', NULL, NULL, NULL, NULL, NULL, 0, 'Translucent Red', '2 x Vinyl, LP, Album, Limited Edition', 'February 10 2017', 'https://open.spotify.com/album/2ODvWsOgouMbaA5xf0RkJe?si=ZJAVbmRyRpy_KZ24Ptb7Uw', 1),
(5, 'Summer Salt - My Favorite Holiday', '\"อัลบั้มรวมเพลงธีมวันหยุดสุดละมุนจากคู่หู Summer Salt โดดเด่นด้วยดนตรีสไตล์ Indie Pop ที่ผสมผสานความสดใสและเสียงร้องที่ปลอบประโลมใจ ตัวแผ่นสีพิเศษนี้ช่วยเพิ่มอรรถรสในการฟังและยังดูสวยงามเป็นเอกลักษณ์ เป็นแผ่นที่แฟนเพลงสายอินดี้ต้องมีติดบ้านไว้สร้างบรรยากาศดีๆ ในทุกช่วงเวลา\"', 'Indie Pop / Surf Pop / Dream Pop', 1300, 'uploads/pre_ca85dd7a45fc68cfc5ad.png', NULL, NULL, NULL, NULL, NULL, 0, 'Cyan / White Marbled', 'Vinyl 1LP', 'November 5 2021', 'https://open.spotify.com/album/5y5s6HzTgyf97FHAuJm8Ct?si=n-4eLI-5R7u5T7WUPHyRvQ', 0),
(9, 'Aespa - The 1st Album ARMAGEDDON (LP VER.)', '\"สัมผัสพลังทำลายล้างจากโลกคู่ขนานกับ \'ARMAGEDDON\' อัลบั้มเต็มชุดแรกของ aespa ที่มาเขย่าวงการ K-Pop อีกครั้ง ในรูปแบบแผ่นเสียงคุณภาพสูง อัดแน่นไปด้วยซาวด์ดนตรีสุดล้ำที่มีทั้ง Hip-hop, Dance และความเป็น Hyperpop ที่เป็นเอกลักษณ์ มาพร้อมเพลงฮิตอย่าง \'Supernova\' และ \'Armageddon\' ที่จะทำให้เครื่องเล่นแผ่นเสียงของคุณกลายเป็นประตูสู่มัลติเวิร์ส\"', 'K-Pop / Dance Pop / Hyperpop / Hip-Hop', 1500, 'uploads/pre_35aa294724ad2a5e164d.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', 'Vinyl 1LP', 'May 27 2024', 'https://open.spotify.com/album/3gHhPm8z8tid1kvpniUKuK?si=0FyV0oSMTLC0ymZDGn-6EQ', 1),
(10, 'Kendrick Lamar - DAMN. [2LP]', '\"การกลับมาอย่างยิ่งใหญ่ของราชันย์แห่ง West Coast กับอัลบั้ม DAMN. ที่เน้นซาวด์ Hip-Hop, Rap และ Trap ที่มีความดิบและทรงพลังกว่าเดิม โดดเด่นด้วยการร่วมงานกับโปรดิวเซอร์ระดับแถวหน้าอย่าง Mike WiLL Made-It และ James Blake พร้อมแขกรับเชิญสุดพิเศษทั้ง Rihanna และ U2 มอบประสบการณ์การฟังที่ยอดเยี่ยมผ่านแผ่นเสียงคู่คุณภาพสูง ให้รายละเอียดเสียงเบสและบีทที่หนักแน่นและนุ่มนวล\"', 'Hip-Hop / Rap / Conscious Rap', 1350, 'uploads/pre_0612b33ffbf7258cd00d.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2LP (Gatefold Sleeve)', 'July 14 2017', 'https://open.spotify.com/album/4eLPsYPBmXABThSJ821sqY?si=FPpJLd-ZS52zvTGHzy-3Bw', 1),
(11, 'Tyler, The Creator - Flower Boy [2LP]', '\"สัมผัสการเติบโตครั้งสำคัญของ Tyler, The Creator ในอัลบั้ม Flower Boy ที่เปลี่ยนผ่านจากความเกรี้ยวกราดสู่ความนุ่มนวลอย่างมีชั้นเชิง โดดเด่นด้วยงานโปรดักชันที่ผสมผสานดนตรี Jazz Rap, Neo Soul และ R&B เข้ากับบีทฮิปฮอปได้อย่างกลมกล่อม ตัวแผ่นมาในรูปแบบ 2LP พร้อมปก Gatefold ที่สวยงาม มอบมิติเสียงที่โปร่งสบายและอบอุ่น เหมาะสำหรับการฟังแบบดื่มด่ำกับซาวด์ดีไซน์ที่ละเอียดอ่อน\"', 'Hip-Hop / Jazz Rap / Neo Soul / Alternative Hip-Hop', 1800, 'uploads/pre_3c14ff939a6d769274c6.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2LP (Gatefold Sleeve)', 'December 1 2017', 'https://open.spotify.com/album/2nkto6YNI4rUYTLqEwWJ3o?si=eGH48xhWTBGOrvGwcFElXA', 0),
(12, 'Dept - Hey Mom, Did You See Me In The Newspaper', '\"การกลับมาของ Dept พร้อมชื่ออัลบั้มกวน ๆ ที่บ่งบอกถึงความสำเร็จ \'Hey Mom, Did You See Me In The Newspaper?\' รวมเพลงฮิตอย่าง \'ประกาศให้โลกรู้\', \'ฟ้ามืดทีไร\' และ \'ถ้ารู้ว่าจะหายไป\' ในรูปแบบแผ่นเสียง 1LP แผ่นใสสุดพรีเมียม เป็นอัลบั้มที่ผสมผสานความสดใสของยุค 2000 เข้ากับซาวด์ซินธ์พ็อปสมัยใหม่ได้อย่างลงตัว ไอเทมชิ้นสำคัญที่แฟนเพลงอินดี้ไทยต้องมีติดบ้าน\"', 'Indie Pop / R&B / Lo-fi', 2500, 'uploads/pre_8a4200efd700de752a7e.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Transparent', 'Vinyl 1LP (12\" 180 Gram / Gatefold Sleeve)', 'October 5 2023', 'https://open.spotify.com/album/2QGUzPveseqBGL7bptAnkx?si=4kjD_bDXTIq_cLLL1WrZ6g', 1),
(13, 'Oasis - What\'s The Story Morning Glory?', '\"สัมผัสอัลบั้มที่ขายดีที่สุดเป็นอันดับ 3 ในประวัติศาสตร์ชาร์ตเพลงอังกฤษ \'(What\'s The Story) Morning Glory?\' คือผลงานที่ส่งให้ Oasis กลายเป็นปรากฏการณ์ระดับโลก อัดแน่นไปด้วยเพลงที่เป็นเหมือนบทเพลงประจำชาติของแฟนเพลงยุค 90s อย่าง \'Wonderwall\', \'Don\'t Look Back in Anger\' และ \'Champagne Supernova\' ตัวแผ่นมาในรูปแบบ 2LP 180g บรรจุในปก Triple Gatefold สุดพรีเมียม ถือเป็นแผ่นที่ต้องมีติดบ้านสำหรับแฟนเพลงสาย Rock และ Britpop ทุกคน\"', 'Britpop / Alternative Rock / Rock', 2000, 'uploads/pre_8d823ed9561cd2837680.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2 x Vinyl LP (Double LP / Triple Gatefold Sleeve)', 'September 26 2014', 'https://open.spotify.com/album/1VW1MFNstaJuygaoTPkdCk?si=r4oc5uJgQRaYOMUvTAorTw', 1),
(14, 'Joji - Piss In The Wind (Limited Edition)', '\"การกลับมาอย่างยิ่งใหญ่ในปี 2026 ของ Joji กับอัลบั้มใหม่ล่าสุด \'Piss In The Wind\' ที่ยังคงเอกลักษณ์ความเหงาและเสียงร้องที่บาดลึกในสไตล์ Lo-Fi R&B และ Alternative พิเศษสุดกับเวอร์ชันแผ่นสี Ruby White Mist ที่มาพร้อมกับ Signed Artcard ลายเซ็นสดจาก Joji เอง ตัวแผ่นน้ำหนัก 140 กรัม ให้คุณภาพเสียงที่ใสและมีมิติ เหมาะสำหรับการเปิดฟังในคืนที่ฝนตกหรือช่วงเวลาที่ต้องการอยู่กับตัวเอง\"', 'Lo-fi / R&B / Alternative R&B', 1750, 'uploads/pre_a12620b2bda53d67ce28.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Ruby White Mist', 'Vinyl 1LP + Limited Signed Artcard', 'Feb 6 2026', 'https://open.spotify.com/album/7MLyEn1CPizpS8bjZ7zzrT?si=G18Qpvn1SSu48N0cDvjYRw', 1),
(15, 'Ed Sheeran - X (2LP)', '\"ผลงานชิ้นเอกที่ทำยอดขายถล่มทลายทั่วโลก \'X\' (Multiply) คือการผสมผสานความเรียบง่ายของเสียงกีตาร์โปร่งเข้ากับโปรดักชันที่ล้ำสมัยอย่างลงตัว อัดแน่นไปด้วยเพลงฮิตระดับพันล้านสตรีมอย่าง \'Thinking Out Loud\', \'Photograph\', \'Sing\' และ \'Don\'t\' ตัวแผ่นมาในรูปแบบ 2LP คุณภาพสูงที่เล่นด้วยความเร็ว 45 RPM มอบประสบการณ์การฟังที่ใสเคลียร์และทรงพลังในทุกตัวโน้ต\"', 'Pop, Folk Pop, Acoustic Pop, R&B', 1600, 'uploads/pre_33c5eb48ead7cdf6dd71.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2 x Vinyl LP (45 RPM / Gatefold Sleeve)', 'June 23 2014', 'https://open.spotify.com/album/1xn54DMo2qIqBuMqHtUsFd?si=cUCcRxa2QJWaAJrpuRvv_A', 1),
(16, 'Maroon 5 - Red Pill Blues', '\"อัลบั้มที่รวมศิลปินระดับแถวหน้าไว้มากที่สุดของ Maroon 5 พบกับการร่วมงานสุดพิเศษกับ SZA, Julia Michaels, A$AP Rocky และ Kendrick Lamar ในรูปแบบแผ่นเสียงคู่ (Double Vinyl) งานโปรดักชันมีความละเอียดและประณีต มอบประสบการณ์การฟังที่เพลิดเพลินตั้งแต่ต้นจนจบ เหมาะสำหรับสายสะสมที่ชื่นชอบดนตรี Pop-Rock ที่มีความเป็นสมัยใหม่\"', 'Pop / Pop Rock / R&B', 1450, 'uploads/pre_37ed46e3c1a37c7d47c0.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2LP', 'November 3 2017', 'https://open.spotify.com/album/1Li4rADxSxjT2g4xqUcMYh?si=SJGXeNGuT-SLzpwwPmKE6g', 1),
(17, 'ENHYPEN - ROMANCE : UNTOLD', '\"สัมผัสบทพิสูจน์ความรักที่ไม่อาจเปิดเผยไปกับ \'ROMANCE : UNTOLD\' อัลบั้มเต็มชุดที่ 2 จาก ENHYPEN ที่มาในรูปแบบแผ่นเสียงคุณภาพสูง งานเพลงชุดนี้เป็นการสำรวจความรู้สึกที่ซับซ้อนผ่านแนวเพลงที่หลากหลายทั้ง R&B, Pop และ Synth-pop อัดแน่นไปด้วยเพลงฮิตอย่าง \'XO (Only If You Say Yes)\' ในรูปแบบแผ่นใส (Clear Vinyl) ที่สวยงามล้ำสมัย มอบประสบการณ์การฟังที่นุ่มนวลและเป็นเอกลักษณ์สำหรับนักสะสมโดยเฉพาะ\"', 'K-Pop / Pop', 1800, 'uploads/pre_6729d729b1f92deb4cce.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Clear', 'Vinyl 1LP (12\" Album)', 'July 12 2024', 'https://open.spotify.com/album/05I8FltCMnGa3kE38mpOkL?si=8jbruh-RSyqZn137hXEhrg', 1),
(18, 'Justin Bieber - JUSTICE STANDARD 2LP', '\"สัมผัสการเดินทางครั้งสำคัญของ Justin Bieber ในอัลบั้ม \'Justice\' ที่นำเสนอความสุกงอมทางดนตรีผ่านการผสมผสานแนวดนตรี Pop, R&B, Synth-pop และกลิ่นอายของ Gospel เข้าด้วยกันอย่างลงตัว อัดแน่นไปด้วยบทเพลงที่สะท้อนถึงความรัก ความหวัง และการเติบโตส่วนตัว มาในรูปแบบแผ่นเสียงคู่ (2LP) ที่ช่วยดึงมิติเสียงและรายละเอียดของโปรดักชันออกมาได้อย่างเต็มอรรถรส\"', 'Pop / R&B', 2200, 'uploads/pre_c04bbea8bba3e0a1b490.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2LP', 'June 25 2021', 'https://open.spotify.com/album/5dGWwsZ9iB2Xc3UKR0gif2?si=lzRRYVXmQ3WegO5Zp3PkcQ', 1),
(19, 'Tyler, The Creator - CALL ME IF YOU GET LOST', '\"ร่วมออกเดินทางไปกับ Tyler Baudelaire ในอัลบั้ม CALL ME IF YOU GET LOST ผลงานระดับรางวัล Grammy ที่ได้รับแรงบันดาลใจจาก Gangsta Grillz mixtapes ของ DJ Drama งานชุดนี้เป็นการผสมผสานบีทฮิปฮอปที่หนักแน่นเข้ากับดนตรี Jazz-rap และ Neo-soul ได้อย่างมีชั้นเชิง ตัวแผ่นมาในรูปแบบ 2LP 180g คุณภาพสูง บรรจุในปก Gatefold ที่ดีไซน์มาอย่างประณีตเหมือนสมุดพาสปอร์ตสะสม มอบมิติเสียงที่กว้างและรายละเอียดดนตรีที่ครบถ้วน\"', 'Hip-Hop / Rap / Jazz Rap', 1600, 'uploads/pre_e671a46b3ffa8143b290.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', '2LP', 'April 15 2022', 'https://open.spotify.com/album/1GG6U2SSJPHO6XsFiBzxYv?si=vt5PVpweTzmIuJ4XK06Wrw', 1),
(20, 'Tyler, The Creator - Chromakopia', '\"ก้าวเข้าสู่โลกของ Chroma the Great กับอัลบั้มที่เปี่ยมไปด้วยการสำรวจตัวตนและวุฒิภาวะ \'Chromakopia\' คือการกลับมาที่ Tyler ลงมือโปรดิวซ์และแต่งเองทั้งหมด โดยมีเสียงบรรยายของแม่เขาเป็นเส้นเรื่องหลัก งานชุดนี้ผสมผสานดนตรี Hip-Hop, R&B และ Experimental ไว้ได้อย่างน่าทึ่ง ตัวแผ่นมาในรูปแบบ 2LP สีขาว (White Vinyl) สุดพรีเมียม บรรจุในปก Gatefold ที่มีงานอาร์ตเวิร์กอันเป็นเอกลักษณ์ มอบมิติเสียงที่หนักแน่นและชัดเจนตามสไตล์\"', 'Hip-Hop / Alternative Hip-Hop', 1950, 'uploads/pre_ac473763d8ad75c7d105.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'White', '2LP', 'October 28 2024', 'https://open.spotify.com/album/0U28P0QVB1QRxpqp5IHOlH?si=n1NAUcPXSHipkM3nWU6nng', 1),
(21, 'Troye Sivan - In A Dream (5th Anniversary Edition)', '\"เฉลิมฉลองครบรอบ 5 ปีของ EP ระดับมาสเตอร์พีซ \'In A Dream\' จาก Troye Sivan กับเวอร์ชันสุดพิเศษที่นักสะสมต้องมี ตัวแผ่นมาในสี Blue & Red Swirl ที่สวยงามและโดดเด่นสะดุดตา อัดแน่นไปด้วยเพลงแนว Synth-pop และ Dream Pop ที่ลุ่มลึกอย่าง \'Take Yourself Home\' และ \'Easy\' มอบประสบการณ์การฟังที่ยอดเยี่ยมผ่านแผ่นเสียง 180g คุณภาพพรีเมียม บรรจุในงานอาร์ตเวิร์กที่ได้รับการออกแบบใหม่เพื่อวาระพิเศษนี้โดยเฉพาะ\"', 'Pop / Synth-pop / Electropop', 1750, 'uploads/pre_cfcda00a19d0baa8691e.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Blue & Red Swirl', 'LP (12\" Vinyl)', 'December 5 2025', 'https://open.spotify.com/album/6DutwGzMeny33G6mIpujDj?si=ih_qkF9FQQeWvmLYbAbW5w', 1),
(22, 'Post Malone - HOLLYWOOD\'S BLEEDING ', '\"หนึ่งในอัลบั้มที่รวมศิลปินแถวหน้าไว้มากที่สุดแห่งทศวรรษ พบกับการร่วมงานที่น่าทึ่งระหว่าง Post Malone กับศิลปินระดับพระกาฬอย่าง Ozzy Osbourne, Travis Scott, SZA, Halsey และ Future อัลบั้ม \'Hollywood\'s Bleeding\' นำเสนอมู๊ดดนตรีที่หรูหราแต่แฝงไปด้วยความหม่นอันเป็นเอกลักษณ์ บรรจุในปก Gatefold ดีไซน์ประณีตพร้อมแผ่นเสียงคู่สีพิเศษ เป็นไอเทมที่ต้องมีติดบ้านสำหรับแฟนเพลง Pop-Rap ยุคใหม่\"', 'Hip-Hop / Pop Rap / Trap', 2150, 'uploads/pre_d3c5f964792aa029f5ad.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Opaque Pink', '2LP (Gatefold Sleeve)', 'June 26 2020', 'https://open.spotify.com/album/4g1ZRSobMefqF6nelkgibi?si=-a4Z1DMKSbuTfBu5ClHeXA', 1),
(23, 'Tyla - TYLA (Limited Edition)', '\"การเดินทางสู่โลกของ African Pop ไปกับ Tyla ในอัลบั้ม self-titled ชุดแรก งานโปรดักชันในแผ่นเสียงนี้โดดเด่นด้วยเสียงเบสแบบ Log-drum ที่เป็นเอกลักษณ์ของ Amapiano มอบประสบการณ์การฟังที่นุ่มนวลแต่หนักแน่น บรรจุในงานอาร์ตเวิร์กที่สวยงามพรีเมียม เหมาะสำหรับเปิดสร้างบรรยากาศในปาร์ตี้หรือฟังเพลินๆ ในวันที่ต้องการพลังงานบวก\"', 'Afropop / Amapiano / R&B / Pop', 1450, 'uploads/pre_ddce94a559a828fe8deb.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Orange', 'Vinyl 1LP', 'March 22 2024', 'https://open.spotify.com/album/6cWVXZCgpDJhvFOqB6o0DP?si=7NjHaWyRQDKmH3rZpj2Z7w', 1),
(24, 'Steve Lacy - Gemini Rights', '\"ดื่มด่ำไปกับซาวด์ดนตรีสุดล้ำที่ผสมผสานอาร์แอนด์บี, ฟังก์ และไซเคเดลิกพ็อปไว้อย่างมีเสน่ห์ใน \'Gemini Rights\' ผลงานเดี่ยวชุดที่สองของ Steve Lacy ที่แสดงถึงความสุกงอมทางดนตรีและการเล่าเรื่องที่บาดลึก อัดแน่นไปด้วยเพลงฮิตอันดับ 1 อย่าง \'Bad Habit\' และ \'Static\' ตัวแผ่น 1LP มอบมิติเสียงที่อบอุ่นและนุ่มนวลตามแบบฉบับอนาล็อก เหมาะสำหรับการเปิดฟังในวันที่ต้องการมู๊ดชิลล์ๆ แต่แฝงด้วยความเท่\"', 'Alternative R&B / Funk / Indie Pop', 1400, 'uploads/pre_417d5a0a5b926b8e16e6.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Black', 'Vinyl 1LP', 'November 4 2022', 'https://open.spotify.com/album/3Ks0eeH0GWpY4AU20D5HPD?si=HO3C3d4_QTa5z8OKml78gg', 2),
(25, 'Jihyo – ZONE (The 1st Mini Album)', '\"ก้าวเข้าสู่โซนแห่งความร้อนแรงไปกับ Jihyo ลีดเดอร์ผู้ทรงพลังจาก TWICE ในอัลบั้มโซโล่ชุดแรก \'ZONE\' ที่ปลดปล่อยเสน่ห์ความเป็นศิลปินเดี่ยวออกมาได้อย่างเต็มพิกัด อัดแน่นไปด้วยเพลงคุณภาพที่โชว์พลังเสียงอันเป็นเอกลักษณ์อย่าง \'Killin\' Me Good\' ตัวแผ่นมาในสีส้ม Limited Edition ที่สวยงามและพรีเมียม มอบประสบการณ์การฟังที่เต็มอิ่มและคมชัดในสไตล์ Pop และ R&B สมฐานะ \'God Jihyo\' ที่แฟนๆ หลงรัก\"', 'K-Pop / Pop / R&B', 1300, 'uploads/pre_a746615ec19c3ea692c2.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Orange', 'Vinyl 1LP', 'August 18 2023', 'https://open.spotify.com/album/1GIkapvyAw5lwdNw66hI44?si=vBho0D1_R72eBOEEM00KsA', 3),
(26, 'Daniel Caesar - NEVER ENOUGH', '\"ดื่มด่ำไปกับความนุ่มนวลของดนตรี R&B และ Soul ระดับมาสเตอร์พีซในอัลบั้ม \'NEVER ENOUGH\' ของ Daniel Caesar งานชุดนี้เป็นการเดินทางผ่านห้วงอารมณ์ที่ซับซ้อน ความรัก และการเติบโต ตัวแผ่นมาในรูปแบบ 2LP สีน้ำตาล (Brown Vinyl) ที่ดูสุขุมและพรีเมียม มอบมิติเสียงที่อบอุ่นและนุ่มนวล เหมาะสำหรับเปิดคลอในค่ำคืนที่ต้องการความสงบและดื่มด่ำกับเสียงร้องที่เปี่ยมไปด้วยจิตวิญญาณ\"', 'R&B / Soul / Neo-Soul', 1800, 'uploads/pre_915883257fc497434243.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Translucent Brown', '2LP (Gatefold Sleeve)', 'April 7 2023', 'https://open.spotify.com/album/2z9lM6LDS58F70IGyQ1XMK?si=DAmmluohRuer2TI-Lexf8w', 4),
(29, 'The 1975 - Being Funny In A Foreign Language', '\"สัมผัสความสุกงอมทางดนตรีของ The 1975 ในอัลบั้มที่ได้รับการยกย่องว่ากลมกล่อมที่สุดชุดหนึ่ง งานชุดนี้ Matty Healy ร่วมกับโปรดิวเซอร์มือทองอย่าง Jack Antonoff รังสรรค์ซาวด์ดนตรีที่เน้นเครื่องดนตรีจริงและลดการใช้คอมพิวเตอร์ให้น้อยที่สุด อัดแน่นไปด้วยเพลงฮิตอย่าง \'Part of the Band\' และ \'About You\' ตัวแผ่นมอบคุณภาพเสียงที่ใสเคลียร์และอบอุ่น เหมาะสำหรับเปิดฟังเพื่อดื่มด่ำกับบทเพลงแห่งความรักในมุมมองที่ตรงไปตรงมา\"', 'Indie Pop / Soft Rock / Alternative Pop', 1, 'storage/uploads/pre_1b6c0265c3b6d3749af4.jpg', NULL, NULL, NULL, NULL, NULL, 0, 'Clear', 'Vinyl 1LP', 'October 14 2022', 'https://open.spotify.com/album/6dVCpQ7oGJD1oYs2fv1t5M?si=JhjQfHyZQz2YN3KnEfsNYw', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$1N0iXMifuAYi.AuVyuvHZOQ4vyITHqpRk48rqSfCHEVaMc/rOjBrW', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
