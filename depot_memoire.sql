-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 29 nov. 2021 à 18:38
-- Version du serveur :  10.4.14-MariaDB
-- Version de PHP : 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `depot_memoire`
--

-- --------------------------------------------------------

--
-- Structure de la table `classe`
--

CREATE TABLE `classe` (
  `id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `classe`
--

INSERT INTO `classe` (`id`, `libelle`) VALUES
(1, '--Choisir--'),
(2, 'Licence 3 '),
(3, 'Master 2'),
(4, 'Licence 2');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `libelle`) VALUES
(1, '--Choisir--'),
(2, 'Java'),
(3, 'php'),
(4, 'wordPress');

-- --------------------------------------------------------

--
-- Structure de la table `depot_memoire`
--

CREATE TABLE `depot_memoire` (
  `id` int(11) NOT NULL,
  `inscription` int(11) NOT NULL,
  `document` varchar(250) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `depot_memoire`
--

INSERT INTO `depot_memoire` (`id`, `inscription`, `document`, `date`) VALUES
(1, 1, 'uploadedFiles/Devoir-Examen-Dy-velopper-D-APPLICATIONS-PHP-JAVASCRIPT-MYSQL.pdf', '2021-11-29');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `id` int(11) NOT NULL,
  `inscription` int(11) NOT NULL,
  `classe` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`id`, `inscription`, `classe`, `date`) VALUES
(1, 1, 2, '2021-11-29');

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

--
-- Déchargement des données de la table `fields_info`
--

INSERT INTO `fields_info` (`id`, `name`, `label`, `htlm_type`, `select_first_option`, `dependance_field`, `default_where_order_by_limit`, `placeholder`, `col_number`, `read_label_col_number`, `read_value_col_number`, `new_line`, `description`, `title`, `fieldset`, `fieldset_legend`, `fieldset_other`, `html_id`, `html_class`, `other_html`, `ajax_param`, `icon`, `php_js_rule`, `ajax_validation`) VALUES
(1, 'id', 'Id', 'number', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(2, 'prenom', 'Prénom', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(3, 'nom', 'Nom', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(4, 'libelle', 'Niveau', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(5, 'etudiant', 'Etudiant', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(6, 'email', 'E-mail', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(7, 'login', 'Login', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(8, 'classe', 'Classe', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(9, 'matiere', 'Matiere', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(10, 'note', 'Note', 'number', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(11, 'document', 'Document', 'file', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(12, 'date', 'Date', 'datetime-local', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(13, 'prof', 'Prof', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(14, 'first_name', 'Prénom', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(15, 'last_name', 'Nom', 'text', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(16, 'password', 'Mot de passe', 'password', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(17, 'user_type', 'Type utilisateur', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(18, 'inscription', 'Etudiant', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur'),
(19, 'cours', 'Matière', 'select', NULL, '', '', NULL, '', '3', '4', '0', NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'blur');

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

CREATE TABLE `inscription` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `login` text NOT NULL,
  `password` varchar(100) NOT NULL,
  `classe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `inscription`
--

INSERT INTO `inscription` (`id`, `nom`, `prenom`, `email`, `login`, `password`, `classe`) VALUES
(1, 'Ndiaye', 'Diockel', 'ndiaye.diockel@estim.sn', 'ndiayediockel', '1e700fa8eea0321cee4ccbe680a56da18ebaae7def90f765c4bc480da7aefcf8', 2);

-- --------------------------------------------------------

--
-- Structure de la table `matiere`
--

CREATE TABLE `matiere` (
  `id` int(11) NOT NULL,
  `prof` int(11) NOT NULL,
  `classe` int(11) NOT NULL,
  `cours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `matiere`
--

INSERT INTO `matiere` (`id`, `prof`, `classe`, `cours`) VALUES
(3, 1, 2, 2);

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

--
-- Déchargement des données de la table `menu`
--

INSERT INTO `menu` (`id`, `libelle`, `parent`, `link`, `urlsup`, `user_type_id_list`) VALUES
(3, 'Ajouter Memoire', 0, 'depotMemoireCreate', '', NULL),
(4, 'Ajouter classe', 0, 'classeCreate', '', NULL),
(5, 'Ajouter Matiere', 0, 'matiereCreate', '', NULL),
(6, 'Ajouter note', 0, 'noteCreate', '', NULL),
(7, 'Ajouter prof', 0, 'profCreate', '', NULL),
(8, 'Inscription', 0, 'inscriptionCreate', '', NULL),
(9, 'etudiant', 0, 'etudiantCreate', '', NULL),
(10, 'Ajouter Cours_matiere', 0, 'coursCreate', '', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `note`
--

CREATE TABLE `note` (
  `id` int(11) NOT NULL,
  `inscription` int(11) NOT NULL,
  `cours` int(11) NOT NULL,
  `note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `prof`
--

CREATE TABLE `prof` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `prof`
--

INSERT INTO `prof` (`id`, `nom`, `prenom`) VALUES
(1, 'Mboup', 'Pape Adama'),
(2, 'Dia', 'Abdoulaye');

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
  `field_not_to_show_list_for_create` text DEFAULT '',
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
-- Déchargement des données de la table `tables_info`
--

INSERT INTO `tables_info` (`id`, `id_field_name`, `name`, `libelle`, `libelle_in_plural`, `libelle_abbr`, `ajax_validation`, `icon_name`, `icon_type`, `field_not_to_show_list_for_create`, `field_not_to_show_list_for_read`, `field_not_to_show_list_for_show`, `other_tab`, `line_number`, `search_zone`, `total_formul`, `range_field_list`, `table_class`, `number_of_lines_per_page`, `create_route_name`, `read_route_name`, `show_route_name`, `show_type`, `update_route_name`, `update_type`, `delete_route_name`, `download_route_name`, `print_route_name`, `print_type`, `stat_desc_col_list`, `update_back_to_tab_list`, `user_type_id_list_for_create`, `user_type_id_list_for_read`, `user_type_id_list_for_show`, `user_type_id_list_for_update`, `user_type_id_list_for_delete`, `user_type_id_list_for_download`, `user_type_id_list_for_print`, `typeahead_search_fields`, `typeahead_display`, `typeahead_temp_sugg`, `typeahead_temp_empty`, `add_create_infos`) VALUES
(1, 'id', 'depot_memoire', 'Depot Memoire', 'Depot Memoires', '', NULL, '', '', 'id', '', '', '{\"inscription->inscription\":\"nom,prenom,email\"}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'id', 'classe', 'Classe', 'Classes', '', NULL, '', '', 'id', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'id', 'matiere', 'Matière', 'Matières', '', NULL, '', '', 'id', '', '', '{\"prof->prof\":\"nom,prenom\",\"classe->classe\":\"libelle\",\"cours->cours\":\"libelle\"}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'id', 'note', 'Note', 'Notes', '', NULL, '', '', 'id', '', '', '{\"cours->cours\":\"libelle\",\"inscription->inscription\":\"nom,prenom,email\"}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'id', 'prof', 'Prof', 'Profs', '', NULL, '', '', 'id', '', '', NULL, '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'id', 'user', 'utilisateur', 'utilisateurs', '', NULL, '', '', 'id, tel_verified_at, email_verified_at, two_factor_secret, two_factor_recovery_code, remember_token, created_at, updated_at', '', '', '', '', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, '1', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(9, 'id', 'inscription', 'Inscription', 'Inscriptions', '', NULL, '', '', 'id', '', '', '{\"classe->classe\":\"libelle\"}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'id', 'etudiant', 'Etudiant', 'Etudiants', '', NULL, '', '', 'id', '', '', '{\"inscription->inscription\":\"nom,prenom,email\",\"classe->classe\":\"libelle\"}', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'id', 'cours', 'Matiere', 'Matieres', '', NULL, '', '', 'id', '', '', '', '#', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user_type`
--

CREATE TABLE `user_type` (
  `id` tinyint(2) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user_type`
--

INSERT INTO `user_type` (`id`, `libelle`, `description`) VALUES
(1, 'Aministrateur', NULL),
(2, 'Simple', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classe`
--
ALTER TABLE `classe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `depot_memoire`
--
ALTER TABLE `depot_memoire`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `fields_info`
--
ALTER TABLE `fields_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_name` (`name`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matiere`
--
ALTER TABLE `matiere`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `prof`
--
ALTER TABLE `prof`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tables_info`
--
ALTER TABLE `tables_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PRIMARY 2` (`name`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Index 2` (`login`);

--
-- Index pour la table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Index 2` (`libelle`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `classe`
--
ALTER TABLE `classe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `depot_memoire`
--
ALTER TABLE `depot_memoire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `etudiant`
--
ALTER TABLE `etudiant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `fields_info`
--
ALTER TABLE `fields_info`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `inscription`
--
ALTER TABLE `inscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `matiere`
--
ALTER TABLE `matiere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `note`
--
ALTER TABLE `note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `prof`
--
ALTER TABLE `prof`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `tables_info`
--
ALTER TABLE `tables_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
