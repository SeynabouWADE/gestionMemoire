
-- Listage de la structure de la table ki330884_lpd0. article
CREATE TABLE IF NOT EXISTS `article` (
  `idarticle` int(5) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `reference` varchar(25) NOT NULL,
  `referenceinternationale` varchar(25) NOT NULL,
  `idrubrique` int(3) NOT NULL,
  `idmarque` int(3) NOT NULL,
  `seuil` int(5) DEFAULT NULL,
  `seuilperemption` float DEFAULT NULL,
  `endroit` varchar(50) DEFAULT NULL,
  `idfournisseur` int(3) DEFAULT NULL,
  `photo` varchar(50) DEFAULT NULL,
  `enpromo` int(4) DEFAULT '0',
  `idsuperarticle` int(5) DEFAULT NULL,
  `actif` enum('0','1') DEFAULT '1',
  PRIMARY KEY (`idarticle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table ki330884_lpd0. articlesdefacture
CREATE TABLE IF NOT EXISTS `articlesdefacture` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `idsuccursale` int(3) NOT NULL,
  `numerofacture` int(10) NOT NULL,
  `idarticle` int(3) NOT NULL,
  `quantite` double NOT NULL,
  `prixunitaire` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23517 DEFAULT CHARSET=latin1;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table ki330884_lpd0. facture
CREATE TABLE IF NOT EXISTS `facture` (
  `idsuccursale` int(3) NOT NULL,
  `numerofacture` int(10) NOT NULL,
  `tva` tinyint(4) NOT NULL DEFAULT '0',
  `typefacture` varchar(55) NOT NULL,
  `voie` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 : facture faite par la boutique, 2 : faite en ligne par le client',
  `alivreadom` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : non, 1 : oui',
  `facturepayee` varchar(3) NOT NULL,
  `autoriselivree` varchar(3) NOT NULL,
  `facturelivree` varchar(3) NOT NULL,
  `iduserfacture` int(4) NOT NULL,
  `iduserpayee` int(4) DEFAULT NULL,
  `iduserautoriselivree` int(4) DEFAULT NULL,
  `iduserlivree` int(4) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `date2` date NOT NULL,
  `time2` time NOT NULL,
  `idclient` int(10) NOT NULL,
  `tempstest` double DEFAULT NULL,
  `idmodificateur` varchar(255) DEFAULT NULL,
  `datemodification` date DEFAULT NULL,
  `heuremodification` time DEFAULT NULL,
  `prototype` varchar(50) DEFAULT NULL,
  `remise` decimal(4,2) DEFAULT '0.00' COMMENT 'pour 20% on met juste 20',
  KEY `idsuccursale` (`idsuccursale`),
  KEY `numerofacture` (`numerofacture`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table ki330884_lpd0. facutre_annulee
CREATE TABLE IF NOT EXISTS `facutre_annulee` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `idsuccursale` int(3) NOT NULL DEFAULT '0',
  `idutilisateur` int(4) NOT NULL DEFAULT '0',
  `numerofacture` int(10) NOT NULL DEFAULT '0',
  `montant` double NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `heure` time DEFAULT NULL,
  `confirmation` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COMMENT='Fature complètement ou partielle payée et ensuite annulée pour modification ou autre';

-- Les données exportées n'étaient pas sélectionnées.
