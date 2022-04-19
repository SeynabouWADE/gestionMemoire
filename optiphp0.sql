-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 14 oct. 2021 à 20:27
-- Version du serveur :  10.4.14-MariaDB
-- Version de PHP : 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `optiphp0`
--

-- --------------------------------------------------------

--
-- Structure de la table `fields_info`
--

CREATE TABLE `fields_info` (
  `id` int(4) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'on peut mettre le nom de la table suivi de celui du champs',
  `label` text DEFAULT NULL,
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
  `title` text DEFAULT NULL COMMENT 'an eventuel tible of the field',
  `fieldset` enum('','start','end','start end') DEFAULT '',
  `fieldset_legend` text DEFAULT NULL,
  `fieldset_other` varchar(500) NOT NULL,
  `html_id` varchar(50) DEFAULT NULL COMMENT 'id of the input tag',
  `html_class` varchar(100) DEFAULT NULL COMMENT 'class of the input tag. Can take several classes',
  `other_html` varchar(255) DEFAULT NULL,
  `ajax_param` varchar(500) DEFAULT NULL COMMENT 'Ex : "ajax.php", "id="+ $(this).val(), "funtionName". Ce qui donnera : ajax("ajax.php", "id="+ $(this).val(), "funtionName"). Car la signature de la \\r\\nfunction est : ajax(urlOrRoute, data, successFunction, type="post") ',
  `icon` varchar(255) DEFAULT NULL,
  `php_js_rule` varchar(255) DEFAULT NULL,
  `ajax_validation` enum('','none','blur','change','keyup','keydown') DEFAULT 'blur'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Json is used to take into account the internalization';

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `parent` int(11) NOT NULL,
  `link` varchar(100) NOT NULL,
  `urlsup` varchar(100) NOT NULL,
  `user_type_id_list` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tables_info`
--

CREATE TABLE `tables_info` (
  `id` int(11) NOT NULL,
  `id_field_name` varchar(50) DEFAULT 'id',
  `name` varchar(50) NOT NULL DEFAULT '',
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `libelle_in_plural` varchar(255) NOT NULL DEFAULT '',
  `libelle_abbr` varchar(50) DEFAULT '',
  `ajax_validation` varchar(50) DEFAULT NULL,
  `icon_name` varchar(50) DEFAULT '',
  `icon_type` enum('','material-icons','fa-icons') DEFAULT '',
  `field_not_to_show_list_for_create` text DEFAULT '\'\'' COMMENT 'the fields that are not displayed on the page containing the list of records of a table (read of crud)',
  `field_not_to_show_list_for_read` text NOT NULL,
  `field_not_to_show_list_for_show` text NOT NULL,
  `other_tab` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'fields in other tables (to make joins)',
  `line_number` enum('','#','N°') DEFAULT '#' COMMENT '0 = no (false), 1 = yes (true)',
  `search_zone` enum('0','1') DEFAULT '1' COMMENT '0 = no (false), 1 = yes (true)',
  `total_formul` varchar(255) DEFAULT NULL,
  `range_field_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'for research (date, time, int, real... type of fields)',
  `table_class` varchar(255) DEFAULT NULL,
  `number_of_lines_per_page` smallint(6) DEFAULT 20,
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
  `add_create_infos` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `fields_info`
--
ALTER TABLE `fields_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_name` (`name`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tables_info`
--
ALTER TABLE `tables_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PRIMARY 2` (`name`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `fields_info`
--
ALTER TABLE `fields_info`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tables_info`
--
ALTER TABLE `tables_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
