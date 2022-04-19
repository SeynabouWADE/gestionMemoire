-- --------------------------------------------------------
-- Hôte :                        localhost
-- Version du serveur:           5.7.24 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Listage de la structure de la table hpd_database_labo. fields_info
CREATE TABLE IF NOT EXISTS `fields_info` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'on peut mettre le nom de la table suivi de celui du champs',
  `label` text,
  `htlm_type` enum('button','checkbox','color','date','datetime-local','editorBasic','editorFull','editorFullAll','editorStandard','editorStandardAll','email','file','hidden','image','month','number','password','radio','range','reset','search','select','submit','tel','text','textarea','time','typeahead','url','week') NOT NULL DEFAULT 'text',
  `select_first_option` varchar(100) DEFAULT NULL COMMENT '{"option_value", "option_text"}. Ex. {"0","-- Choisir"}',
  `dependance_field` varchar(100) DEFAULT '',
  `default_where_order_by_limit` varchar(500) DEFAULT '',
  `placeholder` varchar(255) DEFAULT NULL,
  `col_number` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '',
  `read_label_col_number` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '3',
  `read_value_col_number` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '4',
  `new_line` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0 : pas de nouvelle ligne, laisser juste l''organisation générale; 1: se mettre sur une novelle ligne; 2',
  `description` varchar(255) DEFAULT NULL,
  `title` text COMMENT 'an eventuel tible of the field',
  `fieldset` enum('','start','end','start end') DEFAULT '',
  `fieldset_legend` text,
  `fieldset_other` varchar(500) NOT NULL,
  `html_id` varchar(50) DEFAULT NULL COMMENT 'id of the input tag',
  `html_class` varchar(100) DEFAULT NULL COMMENT 'class of the input tag. Can take several classes',
  `other_html` varchar(255) DEFAULT NULL,
  `ajax_param` varchar(500) DEFAULT NULL COMMENT 'Ex : "ajax.php", "id="+ $(this).val(), "funtionName". Ce qui donnera : ajax("ajax.php", "id="+ $(this).val(), "funtionName"). Car la signature de la \\r\\nfunction est : ajax(urlOrRoute, data, successFunction, type="post") ',
  `icon` varchar(255) DEFAULT NULL,
  `php_js_rule` varchar(255) DEFAULT NULL,
  `ajax_validation` enum('','none','blur','change','keyup','keydown') DEFAULT 'blur',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=latin1 COMMENT='Json is used to take into account the internalization';

-- Listage des données de la table hpd_database_labo.fields_info : ~157 rows (environ)
/*!40000 ALTER TABLE `fields_info` DISABLE KEYS */;
INSERT INTO `fields_info` (`id`, `name`, `label`, `htlm_type`, `select_first_option`, `dependance_field`, `default_where_order_by_limit`, `placeholder`, `col_number`, `read_label_col_number`, `read_value_col_number`, `new_line`, `description`, `title`, `fieldset`, `fieldset_legend`, `fieldset_other`, `html_id`, `html_class`, `other_html`, `ajax_param`, `icon`, `php_js_rule`, `ajax_validation`) VALUES
	(1, 'prenom', 'PrénomHHGG', 'text', NULL, '', '', ' ', '3', '3', '3', '0', '', '', '', '{"fr": "Présentation"}', '', 'prenom', NULL, NULL, NULL, NULL, 'required|min:1', 'blur'),
	(2, 'nom', '{"fr": "Nom"}', 'text', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required|min:2', 'blur'),
	(3, 'tel', '{"fr": "Téléphone"}', 'tel', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required|regex:#^(((\\+\\d{1,3}(-|.| )?\\(?\\d\\)?(-|.| )?\\d{1,5})|(\\(\\d{2,6}\\)))(-|.| )?)?(\\d{2,4}(-|.| )?){2,5}(( x| ext)\\d{1,5}){0,1}$#', 'blur'),
	(4, 'nivlecture', '{"fr": "Niveau de lecture"}', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', '{"fr": "Test"}', '', NULL, NULL, NULL, NULL, NULL, 'required', 'blur'),
	(5, 'avis', '{"fr": "Avis"}', 'text', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(6, 'aviscommentaire', '{"fr": "Commentaire"}', 'textarea', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, 'pam pam1', 'cols="5" rows="5"', NULL, NULL, 'required', 'blur'),
	(7, 'tranche_age', '{"fr": "Tranche d\'age"}', 'radio', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', '{"fr": "REZ"}', '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(8, 'document', '{"fr": "Document"}', 'file', NULL, '', '', NULL, '3', '3', '3', '0', NULL, '{"fr": "Si vous avez mis des suggestions sur le document qui circule alors vous pouvez le charger ici (peu importe le format du fichier) :"}', '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required', 'blur'),
	(9, 'edit_cont', 'Texte from Editeur', 'editorFull', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required|min:3', 'blur'),
	(10, 'id', 'Id', 'text', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(11, 'first_name', 'Prénom', 'text', NULL, '', '', NULL, '3', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required|min:2|max:100', 'blur'),
	(12, 'last_name', 'Nom', 'text', NULL, '', '', NULL, '3', '3', '3', '2', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required|min:2|max:50', 'blur'),
	(13, 'login', 'E-mail / Téléphone', 'text', NULL, '', '', NULL, '3', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required|unique:user', 'blur'),
	(14, 'password', 'Mot de passe tets ', 'password', NULL, '', '', NULL, '3', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'required', 'blur'),
	(15, 'password_confirmation', 'Confirmation du mot de passe', 'password', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, 'confirmed:password', 'blur'),
	(16, 'user_type', 'Type d\'utilisation', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(17, 'naissance', '{\r\n"en":"Date of birth","fr":"Date de naissance"\r\n}', 'date', NULL, '', '', ' ', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(18, 'departement', 'Département', 'select', '["0","-- Choisir"]', '', '{"parent":0}', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, '', NULL, NULL, NULL, 'blur'),
	(19, 'service', 'Sérvice', 'select', '["0","-- Choisir"]', 'departement:parent,onchange="<?= ajax({\'id\': $(this).val()}, \'ajaxServiceFromDepart\')?>"', '{"parent":"-1"}, libelle,30', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, 'service', NULL, NULL, NULL, NULL, 'blur'),
	(20, 'prelevement', 'Nature du prélevement', 'select', '["0","-- Choisir"]', 'prelevements:parent,onchange="<?= ajax({\'id\': $(this).val()}, \'ajaxServiceFromPrelevement\')?>"', '{"parent":"-1"}, libelle,50', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, 'prelevement', NULL, NULL, NULL, NULL, 'blur'),
	(21, 'nature_prelevement', 'Nature du prélévement', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(22, 'signe_clinique', '{"fr":"Signes clinique","en":"clinical signs"}', 'text', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(23, 'date_hospitalisation', 'Date d\'hospitalisation', 'date', NULL, '', '', ' ', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(24, 'etat_septique', 'Etat séptique', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(25, 'hyperthermie', 'Hyperthérmie', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(26, 'choc', 'Choc', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(27, 'date_prelevement', 'Date du prelevement', 'date', NULL, '', '', ' ', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(28, 'methode_identification', 'Méthode d\'identification', 'select', NULL, '', '', NULL, '3', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(29, 'signes_infection', 'Signes biologique de l\'inféction', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(30, 'aspect_prelevement', 'Aspect macroscopique du prelevement', 'select', NULL, '', '', NULL, '3', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, 'onchange="pam(id,fieldset_start_to_tauxleucocyte,[\'hemorragique\',\'limpide\',\'opalescent\',\'purulent\',\'trouble\'])"', NULL, NULL, NULL, 'blur'),
	(31, 'taux_leucocytes', 'Taux de leucocyte significatif', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, 'start', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(32, 'sida_avere', 'Sida avéré', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(33, 'diabete', 'Diabéte', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(34, 'cancer', 'Cancer', 'select', NULL, '', '', '', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(35, 'malnutrition', 'Mal nutrition', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(36, 'motif_hospitalisation', 'Motif d\'hospitalisation', 'text', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(37, 'trait_immunosupresseur', 'Traitement immunosupresseur', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(38, 'score_mac_cabe', 'Score cab cabe', 'number', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, 'min:0|max:2', NULL, NULL, NULL, 'blur'),
	(39, 'intervention_chirurgical', 'Intervention chirurgicale', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, 'onchange="pam(id,fieldset_Start_to_date_intervention)"', NULL, NULL, NULL, 'blur'),
	(40, 'date_arrive_service', 'Date d\'arrivée du patient dans le service', 'date', NULL, '', '', ' ', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(41, 'date_intervention', 'Date intervention\r\n', 'date', NULL, '', '', ' ', '4', '3', '3', '0', NULL, NULL, 'start', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(42, 'chirurgie_endo', 'Chirurgie endo', 'select', NULL, '', '', NULL, '4', '3', '3', '1', NULL, NULL, 'end', NULL, '', NULL, NULL, 'hidden', NULL, NULL, NULL, 'blur'),
	(43, 'classe_contamination', 'Classe de contamination', 'select', NULL, '', '', NULL, '4', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(44, 'crp', 'Crp(mg/l)', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(45, 'fibrenemie', 'Fibrénémie(g/l)', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(46, 'vs', 'Vs(mm)', 'select', NULL, '', '', NULL, '3', '3', '3', '2', NULL, NULL, 'end', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(47, 'nip', 'Nip du patient', 'text', NULL, '', '', 'numero d\'identification du patient', '3', '3', '3', '0', NULL, NULL, '', 'Donnees personnelles', '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(48, 'sexe', 'Sexe', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(49, 'germe', 'Nom du germe', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(50, 'prise_antibiotique', 'Antibiotique prescrit', 'typeahead', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(51, 'interpretation', NULL, 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(52, 'profil_resistance', 'profil de resistance', 'select', NULL, '', '', NULL, '4', '3', '3', '2', NULL, NULL, 'start', 'Les profils de resistances\r\n', '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(55, 'catpiv1', 'Cathéter GGGFFpériph I.V1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', ' ', '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(56, 'catpia1', 'Cathéter périph I.A1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(57, 'catpsc1', 'Cathéter périph S.C1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(58, 'catciv1', 'Cathéter centr I.V1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(59, 'catcia1', 'Cathéter centr I.A1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(60, 'incubation_tracheo1', 'Incubation trachéotomie1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(61, 'sonde_urinaire1', 'Sonde urinaire1', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(62, 'catpiv7', 'Cathéter périph I.V7', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', ' ', '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(63, 'catpia7', 'Cathéter périph I.A', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(64, 'catpsc7', 'Cathéter périph S.C', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(65, 'catciv7', 'Cathéter centr I.V7', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(66, 'catcia7', 'Cathéter centr I.A7', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(67, 'incubation_tracheo7', 'Incubation trachéotomie7', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(68, 'sonde_urinaire7', 'Sonde urinaire', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(69, 'hospit_recente', 'Hospitalisation récente', 'select', NULL, '', '', NULL, '3', '3', '3', '0', 'est-ce que ce patient a été hospitalisé dans les 3 mois précédents', NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(70, 'prise_anti_infect', 'Prise d\'anti-infectieux', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, 'onchange="pam(id,block0nbr_anti_infect)"', NULL, NULL, NULL, 'blur'),
	(71, 'nbr_anti_infect', 'Nombre d\'anti-infectieux', 'select', '["0","-- Choisir"]', '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, 'onchange="ajax({\'value\' : $(this).val()}, \'pamroute\', \'pamJsSuccFct\', \'post\', \'<?= csrfToken()?>\')"', NULL, NULL, NULL, 'blur'),
	(72, 'noms_anti_infect', 'Nom d\'antibiotique', 'text', NULL, '', '', 'ampcilline,penicilline,amoxicilline', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, '', NULL, NULL, NULL, 'blur'),
	(73, 'motif_prescription', 'Motif de la préscription de l\'antibiotique', 'select', NULL, '', '', '', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(74, 'infect_nosoc', 'Infection nosocomiale', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(75, 'site_anat', 'Site anatomique', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(76, 'lieu_acquis', 'Lieu d\'acquisition', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(77, 'date_acquis', 'Date d\'acquisition', 'date', NULL, '', '', ' ', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(78, 'donnee_clinique', 'Nip du patient', 'typeahead', NULL, '', '', 'Ex:1093YZN', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, 'blur'),
	(79, 'inlog', 'Inlogue ATB', 'text', NULL, '', '', 'numéro de l\'antibiogramme', '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(81, 'patient', 'Patient', 'typeahead', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(82, 'deces', 'Déces', 'select', NULL, '', '', NULL, '3', '3', '3', '2', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(84, 'typegerme', 'Type Germe', 'select', '["0","-- Choisir"]', '', '', NULL, '6', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(85, 'nomgerme', 'Nom du germe', 'select', '["0","-- Choisir"]', 'nomgerme:parent,onchange="<?= ajax({\'id\': $(this).val()}, ajaxNomgermeFromTypegerme)?>"', '', NULL, '6', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(86, 'codeATB', 'In log patient', 'text', '', '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(87, 'ampicilline', 'Ampicilline', 'select', '', '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(88, 'amoxicilline_acide_clav', 'Amoxicilline + Acide clavu', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(89, 'ticarcilline', 'Ticarcilline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(90, 'ticarcilline_acide_clav', 'Ticarcilline Acide clavulanique', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(91, 'temocilline', 'Temocilline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(92, 'piperacilline', 'Piperacilline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(93, 'piperacilline_tazobactam', 'Piperacilline Tazobactame', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(94, 'cefalotine', 'Cefalotine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(95, 'cefadroxile', 'Cefadroxil', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(96, 'ceftazidime', 'Ceftazidime', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(97, 'cefotaxime', 'Cefotaxime', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(98, 'cefixime', 'Cefixime', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(99, 'imipenem', 'Imipenem', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(100, 'cefoxitine', 'Cefoxitine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(101, 'ceftriaxone', 'Ceftriaxone', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(102, 'ertapeneme', 'Ertapeneme', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(103, 'aztreoname', 'Aztreoname', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(104, 'cefepime', 'Cefepime', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(105, 'gentamycine', 'Gentamycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(106, 'tobramycine', 'Tobramycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(107, 'amikacine', 'Amikacine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(108, 'netilmicine', 'Netilmicine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(109, 'levofloxacine', 'Levofloxacine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(110, 'ciprofloxacine', 'Ciprofloxine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(111, 'pefloxacine', 'Pefloxacine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(112, 'acide_nalidixique', 'Acide Nalidixique', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(113, 'trimetho_sulfa', 'Trimethoprime Sulfamide', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(114, 'nitrofurantoine', 'Nitrofurantoine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(115, 'cefuroxime', 'Cefuroxime', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(116, 'ceftazidime_avibactam', 'Ceftazidime Avibactam', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(117, 'ceftolozane_tazobactam', 'Ceftolozane Tazobactam', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(118, 'ofloxacine', 'Ofloxacine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(119, 'norfloxacine', 'Norfloxacine', 'select', NULL, '', '', NULL, '3', '3', '3', '1', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(120, 'meropeneme', 'Meropeneme', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(121, 'meropenem_vabobactam', 'Meropenem Vabobactam', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(122, 'rifampicine', 'Rifampicine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(123, 'colistine', 'Colistine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(124, 'benzylpenicilline', 'Benzylpenicilline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(125, 'oxacilline', 'Oxacilline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(126, 'ceftaroline', 'Ceftaroline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(127, 'kanamycine', 'Kanamycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(128, 'moxifloxacine', 'Moxifloxacine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(129, 'erythromycine', 'Erythromycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(130, 'telithromycine', 'Telithromycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(131, 'clindamycine', 'Clindamycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(132, 'lincomycine', 'Lincomycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(133, 'pristinimycine', 'Pristinimycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(134, 'quinupristine_dalfopristine', 'Quinupristine + Dalfopristine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(135, 'linezolide', 'Linezolide', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(136, 'daptomycine', 'Daptomycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(137, 'teicoplanine', 'Teicoplanine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(138, 'vancomycine', 'Vancomycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(140, 'minocycline', 'Minocycline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(141, 'tetracycline', 'Tetracycline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(142, 'tigecycline', 'Tigecycline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(144, 'trimethoprime', 'Trimethoprime', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(145, 'penicilline', 'Penicilline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(146, 'streptomycine', 'Streptomycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(147, 'eravacycline', 'Eravacycline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(148, 'fluoroquinolone', 'Fluoroquinolone', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(149, 'cotrimoxazole', 'Cotrimoxazole', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(150, 'fosfomycine', 'Fosfomycine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(151, 'chloramphenicol', 'Cholramphenicole', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(152, 'cefalexine', 'Cefalaxine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(154, 'aminoside', 'Aminosides', 'select', NULL, '', '', NULL, '4', '3', '3', '2', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(155, 'quinolone', 'Quinilones', 'select', NULL, '', '', NULL, '4', '3', '3', '0', NULL, NULL, 'end', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(156, 'nitroxoline', 'Nitroxoline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(157, 'acide_fusidique', 'Acide Fusidique', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(158, 'mupirocine', 'Mupirocine', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(159, 'tedizolide', 'Tedizolide', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(160, 'doxycycline', 'Doxycycline', 'select', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(163, 'prelevements', 'Type du prelevement', 'select', '["0","-- Choisir"]', '', '{"parent":0}', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(165, 'antibiotique', 'Antibiotique', 'typeahead', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(166, 'libelle', 'Libelle', 'text', NULL, '', '', NULL, '3', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
	(167, 'type_germe_list', 'Type de germe', 'select', NULL, '', '', NULL, '', '3', '3', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur');
/*!40000 ALTER TABLE `fields_info` ENABLE KEYS */;

-- Listage de la structure de la table hpd_database_labo. menu
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `parent` int(11) NOT NULL,
  `link` varchar(100) NOT NULL,
  `user_type_id_list` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Listage des données de la table hpd_database_labo.menu : ~10 rows (environ)
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` (`id`, `libelle`, `parent`, `link`, `user_type_id_list`) VALUES
	(1, 'Tableau de bord', 0, '', NULL),
	(2, 'Patient', 0, 'patientCreate', ''),
	(3, 'Données cliniques', 0, 'donneeCliniqueCreate', NULL),
	(4, 'Antibiogrammes', 0, '', NULL),
	(5, 'Gestion des utilisateurs', 0, 'userCreate', '4'),
	(6, 'Antibiogramme enterobacterie', 4, 'Ajouter-antibiogramme-enterobacterie', NULL),
	(7, 'Antibiogramme non enterobac', 4, 'Ajouter-antibiogramme-non-eterobacterie', NULL),
	(8, 'Antibiogramme Staphilocoque', 4, 'Ajouter-antibiogramme-staphilocoque', NULL),
	(9, 'Antibiogramme Streptocoque', 4, 'Ajouter-antibiogramme-streptocoque', NULL);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

-- Listage de la structure de la table hpd_database_labo. tables_info
CREATE TABLE IF NOT EXISTS `tables_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_field_name` varchar(50) DEFAULT 'id',
  `name` varchar(50) NOT NULL DEFAULT '',
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `libelle_in_plural` varchar(255) NOT NULL DEFAULT '',
  `libelle_abbr` varchar(50) DEFAULT '',
  `ajax_validation` varchar(50) DEFAULT NULL,
  `icon_name` varchar(50) DEFAULT '',
  `icon_type` enum('','material-icons','fa-icons') DEFAULT '',
  `field_not_to_show_list_for_create` text NOT NULL COMMENT 'the fields that are not displayed on the creation page (create of crud)',
  `field_not_to_show_list_for_read` text NOT NULL COMMENT 'the fields that are not displayed on the page containing the list of records of a table (read of crud)',
  `field_not_to_show_list_for_show` text NOT NULL,
  `other_tab` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'fields in other tables (to make joins)',
  `line_number` enum('','#','N°') DEFAULT '#' COMMENT '0 = no (false), 1 = yes (true)',
  `search_zone` enum('0','1') DEFAULT '1' COMMENT '0 = no (false), 1 = yes (true)',
  `total_formul` varchar(255) DEFAULT NULL,
  `range_field_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'for research (date, time, int, real... type of fields)',
  `table_class` varchar(255) DEFAULT NULL,
  `number_of_lines_per_page` smallint(6) DEFAULT '20',
  `create_route_name` varchar(50) DEFAULT NULL,
  `read_route_name` varchar(50) DEFAULT NULL,
  `show_route_name` varchar(50) DEFAULT '',
  `show_type` enum('','blank','modal') DEFAULT '',
  `update_route_name` varchar(50) DEFAULT '',
  `update_type` enum('','blank','modal') DEFAULT '',
  `delete_route_name` varchar(50) DEFAULT '',
  `download_route_name` varchar(50) DEFAULT '',
  `print_route_name` varchar(50) DEFAULT '',
  `print_type` enum('','blank','modal') DEFAULT '',
  `stat_desc_col_list` varchar(255) DEFAULT NULL COMMENT 'ex : table_name.id, prix ...',
  `update_back_to_tab_list` enum('0','1') DEFAULT NULL COMMENT '0 = no (false), 1 = yes (true)',
  `user_type_id_list_for_create` varchar(30) DEFAULT NULL,
  `user_type_id_list_for_read` varchar(30) DEFAULT NULL,
  `user_type_id_list_for_show` varchar(30) DEFAULT NULL,
  `user_type_id_list_for_update` varchar(30) DEFAULT NULL,
  `user_type_id_list_for_delete` varchar(30) DEFAULT NULL,
  `user_type_id_list_for_download` varchar(30) DEFAULT NULL,
  `user_type_id_list_for_print` varchar(30) DEFAULT NULL,
  `typeahead_search_fields` varchar(1000) DEFAULT NULL,
  `typeahead_display` varchar(250) DEFAULT NULL,
  `typeahead_temp_sugg` varchar(500) DEFAULT NULL,
  `typeahead_temp_empty` varchar(500) DEFAULT NULL,
  `add_create_infos` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `PRIMARY 2` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- Listage des données de la table hpd_database_labo.tables_info : ~15 rows (environ)
/*!40000 ALTER TABLE `tables_info` DISABLE KEYS */;
INSERT INTO `tables_info` (`id`, `id_field_name`, `name`, `libelle`, `libelle_in_plural`, `libelle_abbr`, `ajax_validation`, `icon_name`, `icon_type`, `field_not_to_show_list_for_create`, `field_not_to_show_list_for_read`, `field_not_to_show_list_for_show`, `other_tab`, `line_number`, `search_zone`, `total_formul`, `range_field_list`, `table_class`, `number_of_lines_per_page`, `create_route_name`, `read_route_name`, `show_route_name`, `show_type`, `update_route_name`, `update_type`, `delete_route_name`, `download_route_name`, `print_route_name`, `print_type`, `stat_desc_col_list`, `update_back_to_tab_list`, `user_type_id_list_for_create`, `user_type_id_list_for_read`, `user_type_id_list_for_show`, `user_type_id_list_for_update`, `user_type_id_list_for_delete`, `user_type_id_list_for_download`, `user_type_id_list_for_print`, `typeahead_search_fields`, `typeahead_display`, `typeahead_temp_sugg`, `typeahead_temp_empty`, `add_create_infos`) VALUES
	(1, 'id', 'table', 'étudiant', 'étudiants', '', NULL, '', '', 'id, nomsite, enume, tel, date', '', '', '{"tranche_age": "libelle, prix, heure"}', '#', '1', NULL, '{"avis": "double", "date": "double, date", "tranche_age.prix": "double", "tranche_age.heure": "double, time"}', NULL, 20, NULL, NULL, 'tableShow', '', 'tableUpdate', '', 'tableDelete', '', '', '', 'table.id, prix', NULL, NULL, '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(2, 'id', 'etudiant', 'étudiant', 'étudiants', '', NULL, '', '', 'id, nomsite, enume, tel', '', '', '{"tranche_age": "libelle, prix, heure"}', '#', '1', NULL, '{"avis": "double", "date": "double, date", "tranche_age.prix": "double", "tranche_age.heure": "double, time"}', NULL, 20, NULL, NULL, 'tableshow', '', 'tableupdate', '', 'tabledelete', '', '', '', 'table.id, prix', NULL, NULL, '', '1', '1', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(4, 'id', 'user', 'utilisateur', 'utilisateurs', '', NULL, '', '', 'id, tel_verified_at, email_verified_at, two_factor_secret, two_factor_recovery_code, remember_token, created_at, updated_at', '', '', '{"user_type->user_type": "libelle"}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, '1', '3', '3', '3', '3', '3', '3', '3', NULL, NULL, NULL, NULL, NULL),
	(5, 'id', 'donnee_clinique', 'Donnée clinique', 'Données cliniques', '', NULL, '', '', 'id', '', '', '\r\n{\r\n"patient->patient":"nip,nom,prenom",\r\n"etat_septique->n_o_n":"libelle",\r\n"hyperthermie->n_o_n":"libelle",\r\n"choc->n_o_n":"libelle",\r\n"cancer->n_o_n":"libelle",\r\n"nbr_anti_infect->nombre_anti_infectieux":"nombre",\r\n"diabete->n_o_n":"libelle",\r\n"hospit_recente->n_o_n":"libelle",\r\n"prise_anti_infect->n_o_n":"libelle",\r\n"sida_avere->n_o_n":"libelle",\r\n"trait_immunosupresseur->n_o_n":"libelle",\r\n"chirurgie_endo->n_o_n":"libelle",\r\n"malnutrition->n_o_n":"libelle",\r\n"intervention_chirurgical->n_o_n":"libelle",\r\n"infect_nosoc->n_o_n":"libelle",\r\n"deces->n_o_n":"libelle",\r\n"site_anat->sites_anatomique":"libelle",\r\n"service->departement":"libelle",\r\n"prelevement->prelevements":"libelle",\r\n"catpiv1->n_o_n":"libelle",\r\n"catpiv7->n_o_n":"libelle",\r\n"catpia1->n_o_n":"libelle",\r\n"catpia7->n_o_n":"libelle",\r\n"catpsc1->n_o_n":"libelle",\r\n"catpsc7->n_o_n":"libelle",\r\n"catciv1->n_o_n":"libelle",\r\n"catciv7->n_o_n":"libelle",\r\n"catcia1->n_o_n":"libelle",\r\n"catcia7->n_o_n":"libelle",\r\n"incubation_tracheo1->n_o_n":"libelle",\r\n"incubation_tracheo7->n_o_n":"libelle",\r\n"sonde_urinaire1->n_o_n":"libelle",\r\n"sonde_urinaire7->n_o_n":"libelle",\r\n"crp->e_n_s":"libelle",\r\n"fibrenemie->e_n_s":"libelle",\r\n"vs->e_n_s":"libelle"\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', 'patient.id, patient, date_prelevement, nip, nom, prenom', 'nip (prenom nom date_prelevement)', '\'<strong>\' + data.nip + \'</strong>\' + \'  (\' + data.prenom + \'  \' + data.nom + \'  \'  + data.date_prelevement + \')\'', '\'aucun patient associé a ce nip\'', NULL),
	(7, 'id', 'dispositif_envasif ', 'Dispositif invasif', 'Dispositifs invasifs', '', NULL, '', '', 'id', '', '', '{\r\n"patient":"prenom",\r\n"etat_septique->n_o_n":"libelle",\r\n"hyperthermie->n_o_n":"libelle",\r\n"choc->n_o_n":"libelle",\r\n"cancer->n_o_n":"libelle",\r\n\r\n"diabete->n_o_n":"libelle",\r\n"hospit_recente->n_o_n":"libelle",\r\n"prise_anti_infect->n_o_n":"libelle",\r\n"sida_avere->n_o_n":"libelle",\r\n"trait_immunosupresseur->n_o_n":"libelle",\r\n\r\n"malnutrition->n_o_n":"libelle",\r\n"intervention_chirurgical->n_o_n":"libelle",\r\n"infect_nosoc->n_o_n":"libelle",\r\n"deces->n_o_n":"libelle",\r\n"site_anat->sites_anatomique":"libelle",\r\n"service->departemen":"libelle",\r\n"prelevement->prelevements":"libelle",\r\n\r\n"catpiv1->n_o_n":"libelle",\r\n"catpiv7->n_o_n":"libelle",\r\n"catpia1->n_o_n":"libelle",\r\n"catpia7->n_o_n":"libelle",\r\n"catpsc1->n_o_n":"libelle",\r\n"catpsc7->n_o_n":"libelle",\r\n"catciv1->n_o_n":"libelle",\r\n"catciv7->n_o_n":"libelle",\r\n"catcia1->n_o_n":"libelle",\r\n"catcia7->n_o_n":"libelle",\r\n"incubation_tracheo1->n_o_n":"libelle",\r\n"incubation_tracheo7->n_o_n":"libelle",\r\n"sonde_urinaire1->n_o_n":"libelle",\r\n"sonde_urinaire7->n_o_n":"libelle",\r\n\r\n"taux_leucocytes->e_n_s":"libelle",\r\n"crp->e_n_s":"libelle",\r\n"fibrenemie->e_n_s":"libelle",\r\n"vs->e_n_s":"libelle"\r\n\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(8, 'id', 'antibiogramme', 'Antibiogramme', 'Antibiogrammes', 'ATB', NULL, '', '', 'id', '', '', '{\r\n\r\n"aminoside":"libelle",\r\n"germe":"libelle",\r\n"interpretation":"libelle",\r\n"profil_resistance":"libelle",\r\n"quinolone":"libelle"\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(11, 'id', 'patient', 'patient', 'Patients', '', NULL, '', '', 'id', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '-id', 'nip (prenom nom)', '\'<strong>\' + data.nip + \'-\' + data.prenom + \'-\' + data.nom + \'</strong>\'', '\'aucun résultat\'', 'nip, nom, prenom, naissance, sexe'),
	(12, 'id', 'departemement', 'Département', 'Départements', 'dép', NULL, '', '', 'id', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(13, 'id', 'atb_enterobacterie', 'Antibiogramme Enterobacterie', 'Antibiogramme Enterobacterie', 'atb_entero', NULL, '', '', 'id', '', '', '{\r\n"pefloxacine->interpretation":"libelle",\r\n"acide_nalidixique->interpretation":"libelle",\r\n"norfloxacine->interpretation":"libelle",\r\n"trimetho_sulfa->interpretation":"libelle",\r\n"nitrofurantoine->interpretation":"libelle",\r\n"cefuroxime->interpretation":"libelle",\r\n\r\n"ceftazidime_avibactam->interpretation":"libelle",\r\n"ceftolozane_tazobactam->interpretation":"libelle",\r\n"ofloxacine->interpretation":"libelle",\r\n"norfloxacine->interpretation":"libelle",\r\n\r\n"amoxicilline_acide_clav->interpretation":"libelle",\r\n"ticarcilline->interpretation":"libelle",\r\n"ticarcilline_acide_clav->interpretation":"libelle",\r\n"temocilline->interpretation":"libelle",\r\n"piperacilline->interpretation":"libelle",\r\n\r\n"piperacilline_tazobactam->interpretation":"libelle",\r\n"cefalotine->interpretation":"libelle",\r\n"cefadroxile->interpretation":"libelle",\r\n"cefalexine->interpretation":"libelle",\r\n\r\n"cefoxitine->interpretation":"libelle",\r\n"ceftazidime->interpretation":"libelle",\r\n"cefotaxime->interpretation":"libelle",\r\n"cefixime->interpretation":"libelle",\r\n"imipenem->interpretation":"libelle",\r\n\r\n"ertapeneme->interpretation":"libelle",\r\n"aztreoname->interpretation":"libelle",\r\n"cefepime->interpretation":"libelle",\r\n"gentamycine->interpretation":"libelle",\r\n"tigecycline->interpretation":"libelle",    \r\n\r\n"nitroxoline->interpretation":"libelle",  \r\n"ceftriaxone->interpretation":"libelle",  \r\n"tobramycine->interpretation":"libelle",\r\n"amikacine->interpretation":"libelle",\r\n"netilmicine->interpretation":"libelle",\r\n"ciprofloxacine->interpretation":"libelle",\r\n"levofloxacine->interpretation":"libelle",  \r\n\r\n"ampicilline->interpretation":"libelle",\r\n"chloramphenicol->interpretation":"libelle",\r\n"eravacycline->interpretation":"libelle",\r\n"meropenem_vabobactam->interpretation":"libelle",\r\n\r\n"aminoside":"libelle",\r\n"germe->germe_entero":"libelle",\r\n"profil_resistance":"libelle",\r\n"quinolone":"libelle"\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', NULL, NULL, NULL, NULL, NULL),
	(15, 'id', 'atb_non_enterobacterie', 'Antibiogramme non eterobacterie', 'Antibiogrammes non eterobacterie', 'atb_non_entero', NULL, '', '', 'id', '', '', '{\r\n\r\n"ticarcilline->interpretation":"libelle",\r\n"piperacilline->interpretation":"libelle",\r\n"piperacilline_tazobactam->interpretation":"libelle",\r\n"ceftazidime->interpretation":"libelle",\r\n\r\n"imipenem->interpretation":"libelle",\r\n"aztreoname->interpretation":"libelle",\r\n"cefepime->interpretation":"libelle",\r\n"gentamycine->interpretation":"libelle",\r\n"tobramycine->interpretation":"libelle",\r\n\r\n"amikacine->interpretation":"libelle",\r\n"netilmicine->interpretation":"libelle",\r\n"ciprofloxacine->interpretation":"libelle",\r\n"trimetho_sulfa->interpretation":"libelle",\r\n"levofloxacine->interpretation":"libelle",\r\n\r\n"ceftazidime_avibactam->interpretation":"libelle",\r\n"ceftolozane_tazobactam->interpretation":"libelle",\r\n"meropeneme->interpretation":"libelle",\r\n"rifampicine->interpretation":"libelle",\r\n"colistine->interpretation":"libelle",\r\n\r\n"fosfomycine->interpretation":"libelle",\r\n"ticarcilline_acide_clav->interpretation":"libelle",\r\n\r\n"aminoside":"libelle",\r\n"germe->germe_nonentero":"libelle",\r\n"profil_resistance":"libelle",\r\n"quinolone":"libelle"\r\n\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', NULL, NULL, NULL, NULL, NULL),
	(16, 'id', 'atb_staphilocoque', 'Antibiogramme Staphilocoque', 'Antibiogrammes Staphilocoques', 'atb_staph', NULL, '', '', 'id', '', '', '{"cefoxitine->interpretation":"libelle",\r\n"imipenem->interpretation":"libelle","norfloxacine->interpretation":"libelle",\r\n"cefotaxime->interpretation":"libelle",\r\n\r\n"tobramycine->interpretation":"libelle",\r\n\r\n"netilmicine->interpretation":"libelle",\r\n"ciprofloxacine->interpretation":"libelle",\r\n"trimetho_sulfa->interpretation":"libelle",\r\n"levofloxacine->interpretation":"libelle",\r\n"rifampicine->interpretation":"libelle",\r\n"benzylpenicilline->interpretation":"libelle",\r\n"penicilline->interpretation":"libelle",\r\n\r\n"ampicilline->interpretation":"libelle",\r\n"oxacilline->interpretation":"libelle",\r\n"ceftaroline->interpretation":"libelle",\r\n"kanamycine->interpretation":"libelle",\r\n"moxifloxacine->interpretation":"libelle",\r\n"erythromycine->interpretation":"libelle",\r\n\r\n"telithromycine->interpretation":"libelle",\r\n"clindamycine->interpretation":"libelle",\r\n"lincomycine->interpretation":"libelle",\r\n"pristinimycine->interpretation":"libelle",\r\n"quinupristine_dalfopristine->interpretation":"libelle",\r\n"linezolide->interpretation":"libelle",\r\n\r\n"daptomycine->interpretation":"libelle",\r\n"chloramphenicol->interpretation":"libelle",\r\n"teicoplanine->interpretation":"libelle",\r\n"vancomycine->interpretation":"libelle",\r\n"doxycycline->interpretation":"libelle",\r\n"minocycline->interpretation":"libelle",\r\n"tetracycline->interpretation":"libelle",\r\n\r\n"tigecycline->interpretation":"libelle",\r\n"nitrofurantoine->interpretation":"libelle",\r\n"trimethoprime->interpretation":"libelle",\r\n"kanamycine->interpretation":"libelle",\r\n"moxifloxacine->interpretation":"libelle",\r\n"erythromycine->interpretation":"libelle",\r\n\r\n"tedizolide->interpretation":"libelle",\r\n"acide_fusidique->interpretation":"libelle",\r\n"cotrimoxazole->interpretation":"libelle",\r\n"gentamycine->interpretation":"libelle",\r\n"eravacycline->interpretation":"libelle",\r\n"fosfomycine->interpretation":"libelle",\r\n"mupirocine->interpretation":"libelle",\r\n\r\n\r\n"aminoside":"libelle",\r\n"germe->germe_staph":"libelle",\r\n"profil_resistance":"libelle",\r\n"quinolone":"libelle"\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', NULL, NULL, NULL, NULL, NULL),
	(17, 'id', 'atb_streptocoque', 'Antibiogramme Streptocoque', 'Antibiogrammes Streptocoques', 'atb_strepto', NULL, '', '', 'id', '', '', '\r\n{\r\n"imipenem->interpretation":"libelle",\r\n"cefotaxime->interpretation":"libelle",\r\n"ampicilline->interpretation":"libelle",\r\n"norfloxacine->interpretation":"libelle",\r\n"rifampicine->interpretation":"libelle",\r\n\r\n"oxacilline->interpretation":"libelle",\r\n"erythromycine->interpretation":"libelle",\r\n"pristinimycine->interpretation":"libelle",\r\n"quinupristine_dalfopristine->interpretation":"libelle",\r\n"linezolide->interpretation":"libelle",\r\n"daptomycine->interpretation":"libelle",\r\n\r\n"teicoplanine->interpretation":"libelle",\r\n"vancomycine->interpretation":"libelle",\r\n"doxycycline->interpretation":"libelle",\r\n"minocycline->interpretation":"libelle",\r\n"tetracycline->interpretation":"libelle",\r\n"tigecycline->interpretation":"libelle",\r\n\r\n"nitrofurantoine->interpretation":"libelle",\r\n"trimethoprime->interpretation":"libelle",\r\n"telithromycine->interpretation":"libelle",\r\n"clindamycine->interpretation":"libelle",\r\n"kanamycine->interpretation":"libelle",\r\n"gentamycine->interpretation":"libelle",\r\n\r\n\r\n"penicilline->interpretation":"libelle",\r\n"streptomycine->interpretation":"libelle",\r\n"eravacycline->interpretation":"libelle",\r\n"fluoroquinolone->interpretation":"libelle",\r\n"cotrimoxazole->interpretation":"libelle",\r\n"fosfomycine->interpretation":"libelle",\r\n"chloramphenicol->interpretation":"libelle",\r\n\r\n"aminoside":"libelle",\r\n"germe->germe_strepto":"libelle",\r\n"profil_resistance":"libelle",\r\n"quinolone":"libelle"\r\n\r\n}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', '3,4', NULL, NULL, NULL, NULL, NULL),
	(18, 'id', 'prelevements', 'Prelevement', 'Prelevements', '', NULL, '', '', 'id', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
	(19, 'id', 'antibiotique', 'Prise d\'un antibiotique', '', 'prise atb', NULL, '', '', 'id', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'libelle', 'libelle', '\'<strong>\' + data.libelle+ \'</strong>\'', '\'aucun antibiotique ayant ce nom\'', 'libelle'),
	(21, 'id', 'dashboard', 'Tableau de bord\r\n', '', '', NULL, '', '', '', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `tables_info` ENABLE KEYS */;

-- Listage de la structure de la table hpd_database_labo. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `login` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `user_type` tinyint(2) DEFAULT NULL,
  `tel_verified_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_recovery_code` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- Listage des données de la table hpd_database_labo.user : ~8 rows (environ)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `first_name`, `last_name`, `login`, `password`, `user_type`, `tel_verified_at`, `email_verified_at`, `two_factor_secret`, `two_factor_recovery_code`, `remember_token`, `created_at`, `updated_at`) VALUES
	(14, 'Pape Adama', 'MBOUP', 'pamboup', 'aed18d52e4611e6f7ee1fe4b22ec5334abdeb7a1588d1289a62577bd1fef5e66', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(15, '', 'Dia', 'dia', '31e396e3eaf1f2d542ec95a78741f9a88298eb732fdba4415e62e836d2459118', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(16, '', 'Dia', 'dia2', '31e396e3eaf1f2d542ec95a78741f9a88298eb732fdba4415e62e836d2459118', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(17, 'Becaye', 'FALL', 'becayefall@gmail.com', '8bf4890ebc3c4d74905aa104cb31d57b90a915faeefbbbb0bee9f1c0d9b00b7d', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(18, 'Ndeye Fatou', 'MBODJ', 'Fatou', '542ef11a575d3b924e3d0f28d938e0f668081d6faab1187efb604c371b76e135', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(19, 'Safietou', 'DIALLO', 'Safietou', '446d5a79758bfbdfa99abccc39976c1f868e871fcf93a60030f95e652bbd07f0', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(20, 'Maimouna', 'Sylla', 'Maimouna', '62eeaba03a73566174807c9c96f317ba1cbfff40e9810bdeb66d22a4a5bf6154', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(22, 'Pape Adama', 'MBOUP', 'pamboup2', 'aed18d52e4611e6f7ee1fe4b22ec5334abdeb7a1588d1289a62577bd1fef5e66', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Listage de la structure de la table hpd_database_labo. user_type
CREATE TABLE IF NOT EXISTS `user_type` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Listage des données de la table hpd_database_labo.user_type : ~2 rows (environ)
/*!40000 ALTER TABLE `user_type` DISABLE KEYS */;
INSERT INTO `user_type` (`id`, `libelle`, `description`) VALUES
	(3, 'Aministrateur', NULL),
	(4, 'Simple', NULL);
/*!40000 ALTER TABLE `user_type` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
