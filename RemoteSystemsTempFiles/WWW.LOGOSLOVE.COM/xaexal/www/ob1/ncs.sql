-- MySQL dump 10.15  Distrib 10.0.21-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: xaexal
-- ------------------------------------------------------
-- Server version	10.1.13-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ncs_student`
--

DROP TABLE IF EXISTS `ncs_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ncs_student` (
  `name` varchar(32) NOT NULL,
  `birth` char(8) DEFAULT NULL,
  `seq` int(10) unsigned DEFAULT NULL,
  `active` char(10) DEFAULT NULL,
  `presented` char(8) DEFAULT NULL,
  `tvid` varchar(12) DEFAULT NULL,
  `tvkey` varchar(12) DEFAULT NULL,
  `absence` char(1) DEFAULT NULL,
  `ipaddr` varchar(16) DEFAULT NULL,
  `msg` text,
  `msgtime` char(14) DEFAULT '00000000000000',
  `msg2student` text,
  `alive` char(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ncs_student`
--

LOCK TABLES `ncs_student` WRITE;
/*!40000 ALTER TABLE `ncs_student` DISABLE KEYS */;
INSERT INTO `ncs_student` VALUES ('고이준','860925',16,'2009021741','20200527','',NULL,NULL,NULL,NULL,'20200807101949',NULL,'0'),('구자민','950706',13,'2007141738','20200527',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0'),('김영철','931102',1,'2008071016','20200527','','',NULL,NULL,NULL,'20200807101635',NULL,'0'),('김진수','980425',4,'2008281737','20200527','','',NULL,NULL,NULL,'20200807102609',NULL,'0'),('박상훈','930828',20,'2007141210','20200527','',NULL,NULL,NULL,NULL,'20200625110313',NULL,'0'),('신보람','850405',11,'2008071738','20200527',NULL,NULL,NULL,NULL,NULL,'20200807101459',NULL,'0'),('오종민','920206',18,'2008071402','20200527','','',NULL,NULL,NULL,'20200807102136',NULL,'0'),('이승미','951202',9,'2008071045','20200527','','',NULL,NULL,NULL,'20200807100856',NULL,'0'),('이연수','920130',3,'2008071737','20200527','','',NULL,NULL,NULL,'20200807101917',NULL,'0'),('이은수','980421',12,'2008071738','20200911','','',NULL,NULL,NULL,'20200807100354',NULL,'0'),('이지선','990127',2,'2008281523','20200527',NULL,NULL,NULL,NULL,NULL,'20200807095824',NULL,'0'),('장재우','991215',10,'2008071042','20200527',NULL,NULL,NULL,NULL,NULL,'20200807101513',NULL,'0'),('최정민','920619',24,'2005181540','20200527',NULL,NULL,NULL,NULL,NULL,NULL,'sadfasdfasdf','0'),('최태란','991126',17,'2008121011','20200527',NULL,NULL,NULL,NULL,NULL,'20200807101931',NULL,'0'),('안하정','930418',15,'2009020910','20200527','','',NULL,NULL,NULL,'20200807100354',NULL,'0'),('김정은','901021',8,'2008071400','20200527','','',NULL,NULL,NULL,'20200807101630',NULL,'0'),('인주영','980306',7,'2008101707','20200527',NULL,NULL,NULL,NULL,NULL,'20200807102226',NULL,'0'),('곽선영','980529',5,'2008071741','20200527',NULL,NULL,NULL,NULL,NULL,'20200807101930',NULL,'0'),('박보근','950529',6,'2008071221','20200527',NULL,NULL,NULL,NULL,NULL,'20200807100505',NULL,'0'),('채성주','970117',10,'2010261740',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1'),('정해인','950107',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'0'),('이하린','980907',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'0'),('이주환','000529',13,'2010262052',NULL,'','',NULL,NULL,'','20201008153037',NULL,'1'),('이성규','950215',14,'2010261716',NULL,'1643732509','',NULL,NULL,'강사님 이메일 <xaexal@gmail.com> 맞나여?','20201013153744',NULL,'1'),('이경희','921213',3,'2010261722',NULL,NULL,NULL,NULL,NULL,'강사님.. 저는 지금 인덱스로 구한게 아니라 그냥 피보나치수열 구한거죠 ?','20201012153413',NULL,'1'),('원서영','960415',6,'2010261721',NULL,'','',NULL,NULL,'선생님 피보나치 수열 했봤는데 \n-90618175\n-889489150\n같은 숫자가 나오는건 왜인가요jQuery34107978356797154817_1602134419253','20201008143625',NULL,'1'),('우정국','980715',11,'2010261433',NULL,'',NULL,NULL,NULL,'select cust_year_of_birth,cust_gender, count(*) from customers group by cust_year_of_birth,cust_gender order by cust_year_of_birth,cust_gender ;','20201026143305',NULL,'1'),('안진환','880504',20,'2010261559',NULL,NULL,NULL,NULL,NULL,'이렇게하면 코딩 이라고 생각할수없나요?','20201012112910',NULL,'1'),('신은상','881021',9,'2010261738',NULL,NULL,NULL,NULL,NULL,'강사님 Menu탭 아래 부분으로 좀 내려주시면 감사드리겠습니다.','20201020101620',NULL,'1'),('백송이','980821',22,'2010261534',NULL,NULL,NULL,NULL,NULL,'질문있어요~','20201014160238',NULL,'1'),('방진희','930906',8,'2010261736',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1'),('박진성','921004',5,'2010261211',NULL,'1268450776','js22315564',NULL,NULL,NULL,'00000000000000',NULL,'1'),('민주홍','900521',21,'2010261739',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1'),('김효빈','980419',15,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'0'),('김혜지','970101',4,'2010261738',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1'),('김혜원','980709',7,'2010261735',NULL,NULL,NULL,NULL,NULL,'자꾸 에러가 뜨는데 어떤게 문제인지 모르겠습니다','20201012172826',NULL,'1'),('김진섭','941217',18,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'0'),('김에스더','960803',16,'2010261718',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1'),('김세웅','890107',19,'2010261738',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1'),('김병호','980107',15,'2010261738',NULL,'','',NULL,NULL,NULL,'00000000000000',NULL,'1'),('김규백','950216',22,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'0'),('강혁','830416',12,'2010261529',NULL,NULL,NULL,NULL,NULL,NULL,'00000000000000',NULL,'1');
/*!40000 ALTER TABLE `ncs_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ncs_config`
--

DROP TABLE IF EXISTS `ncs_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ncs_config` (
  `title` varchar(64) DEFAULT NULL,
  `orgname` varchar(64) DEFAULT NULL,
  `period1` char(8) DEFAULT NULL,
  `days` decimal(3,0) DEFAULT NULL,
  `period2` char(8) DEFAULT NULL,
  `endtime` varchar(256) DEFAULT NULL,
  `seat_cnt` int(11) DEFAULT '0',
  `active` char(1) DEFAULT NULL,
  `col_cnt` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ncs_config`
--

LOCK TABLES `ncs_config` WRITE;
/*!40000 ALTER TABLE `ncs_config` DISABLE KEYS */;
INSERT INTO `ncs_config` VALUES ('빅데이터 기반의 자바,파이썬UI전문가 양성과정','휴먼교육센터','20200825',104,'20210127','0950,1050,1150,1250,1440,1540,1640,1740',24,'1',8);
/*!40000 ALTER TABLE `ncs_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ncs_drill`
--

DROP TABLE IF EXISTS `ncs_drill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ncs_drill` (
  `name` varchar(32) NOT NULL,
  `done` text,
  `submit` text,
  `created` char(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ncs_drill`
--

LOCK TABLES `ncs_drill` WRITE;
/*!40000 ALTER TABLE `ncs_drill` DISABLE KEYS */;
INSERT INTO `ncs_drill` VALUES ('최대공약수,최소공배수','김에스더,박진성,김혜지,강혁,이성규,김세웅,방진희,원서영,민주홍,이경희','김병호','20201016164643'),('매니저별 부하직원 숫자','이성규,김혜원,김세웅,이경희,강혁,방진희,원서영,김혜지,채성주,민주홍,백송이,안진환,우정국,김병호,김에스더,신은상','이주환','20201026140726'),('출생년도별 회원 숫자 조회','김혜원,방진희,김에스더,이성규,강혁,채성주,안진환,백송이,민주홍,김혜지,김세웅,신은상,김병호,이경희,원서영','이주환,우정국','20201026141912'),('출생년도별  성별 회원숫자 조회','김에스더,방진희,김혜원,김병호,백송이,원서영,안진환,강혁,김혜지,이경희,김세웅,민주홍,우정국,이주환,이성규,채성주,신은상','','20201026142402'),('매니저별 부하직원 연봉합계','김에스더,방진희,강혁,이성규,안진환,채성주,김혜지,김병호,백송이,민주홍,김세웅,이주환,이경희,원서영,신은상,김혜원','','20201026150415');
/*!40000 ALTER TABLE `ncs_drill` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-27  1:17:51
