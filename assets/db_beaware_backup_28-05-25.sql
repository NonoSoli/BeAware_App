-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 28 mai 2025 à 08:15
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_beaware`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateDomain` (IN `p_title` VARCHAR(255), IN `p_icon_path` VARCHAR(255), IN `p_color` VARCHAR(7), IN `p_icon_filename` VARCHAR(255))   BEGIN
    INSERT INTO domains (title, icon_path, color, icon_filename, is_active, created_at) 
    VALUES (p_title, p_icon_path, p_color, p_icon_filename, 1, NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateExercice` (IN `p_title` VARCHAR(255), IN `p_situation` TEXT, IN `p_fk_level_id` INT)   BEGIN
    INSERT INTO exercices (title, situation, fk_level_id)
    VALUES (p_title, p_situation, p_fk_level_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateLevel` (IN `p_title` VARCHAR(255), IN `p_fk_domain_id` INT, IN `p_time` INT, IN `p_difficulty` INT)   BEGIN
  INSERT INTO levels (title, fk_domain_id, time, difficulty) VALUES (p_title, p_fk_domain_id, p_time, p_difficulty);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateOption` (IN `p_title` VARCHAR(255), IN `p_fk_exercice_id` INT, IN `p_feedback` VARCHAR(255), IN `p_correct` BOOL)   BEGIN
  INSERT INTO options (title, fk_exercice_id, feedback, correct) VALUES (p_title, p_fk_exercice_id, p_feedback, p_correct);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteDomain` (IN `p_id` INT)   BEGIN
  DELETE FROM domains WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteExercice` (IN `p_id` INT)   BEGIN
  DELETE FROM exercices WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteLevel` (IN `p_id` INT)   BEGIN
  DELETE FROM levels WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteOption` (IN `p_id` INT)   BEGIN
  DELETE FROM options WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDomains` ()   BEGIN
  SELECT * FROM domains;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetExercices` ()   BEGIN
  SELECT * FROM exercices;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLevels` ()   BEGIN
  SELECT * FROM levels;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOptions` ()   BEGIN
  SELECT * FROM options;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateDomain` (IN `p_id` INT, IN `p_title` VARCHAR(255), IN `p_icon_path` VARCHAR(255), IN `p_color` VARCHAR(7), IN `p_icon_filename` VARCHAR(255))   BEGIN
    UPDATE domains 
    SET 
        title = p_title,
        icon_path = p_icon_path,
        color = p_color,
        icon_filename = p_icon_filename,
        updated_at = NOW()  -- Optionnel si vous avez la colonne updated_at
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateExercice` (IN `p_id` INT, IN `p_title` VARCHAR(255), IN `p_fk_level_id` INT)   BEGIN
  UPDATE exercices SET title = p_title, fk_level_id = p_fk_level_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateLevel` (IN `p_id` INT, IN `p_title` VARCHAR(255), IN `p_fk_domain_id` INT, IN `p_time` INT, IN `p_difficulty` INT)   BEGIN
    UPDATE levels
    SET title = p_title,
        fk_domain_id = p_fk_domain_id,
        time = p_time,
        difficulty = p_difficulty
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateOption` (IN `p_id` INT, IN `p_title` VARCHAR(255), IN `p_fk_exercice_id` INT, IN `p_feedback` VARCHAR(255), IN `p_correct` BOOL)   BEGIN
  UPDATE options SET title = p_title, fk_exercice_id = p_fk_exercice_id, feedback = p_feedback, correct = p_correct WHERE id = p_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `domains`
--

CREATE TABLE `domains` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `icon_path` varchar(255) NOT NULL,
  `icon_filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `domains`
--

INSERT INTO `domains` (`id`, `title`, `color`, `is_active`, `icon_path`, `icon_filename`, `created_at`, `updated_at`) VALUES
(1, 'Aide Médicale', '#0082ff', 1, 'assets/img/domains/domain_6835af77c3404.png', 'caducee-medic.png', '2025-05-27 07:46:10', '2025-05-27 14:38:41'),
(2, 'Pompier', '#ff0f00', 0, '', NULL, '2025-05-27 07:46:10', '2025-05-27 12:26:16'),
(3, '1722', '#404040', 1, 'assets/img/domains/domain_6835c44f0fd27.png', '1722.png', '2025-05-27 07:46:10', '2025-05-27 13:55:27'),
(4, 'Prévention', '#ffc800', 1, 'assets/img/domains/domain_6835d4831578f.png', 'extincteur.png', '2025-05-27 07:46:10', '2025-05-27 15:04:35'),
(5, 'TEST 2', '#44ff00', 0, 'assets/img/domains/domain_68356fa2b0382.png', 'close-x.png', '2025-05-27 07:46:58', '2025-05-27 12:20:42'),
(6, 'Pompier', '#ff0f00', 0, 'assets/img/domains/domain_6835aea279366.png', 'caducee-pompier.png', '2025-05-27 12:22:58', '2025-05-27 12:26:06'),
(7, 'Pompier', '#ff0f00', 1, 'assets/img/domains/domain_6835af2c4b878.png', 'caducee-pompier.png', '2025-05-27 12:25:16', '2025-05-27 12:25:16');

-- --------------------------------------------------------

--
-- Structure de la table `exercices`
--

CREATE TABLE `exercices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `situation` text DEFAULT NULL,
  `fk_level_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exercices`
--

INSERT INTO `exercices` (`id`, `title`, `situation`, `fk_level_id`, `is_active`) VALUES
(1, 'Premiers réflexes', 'Mise en situation\r\n', 1, 0),
(2, 'TEST EXERCICE', NULL, 1, 0),
(3, 'Vous êtes témoin d\'un accident : une voiture a percuté un arbre sur une route départementale peu fréquentée. Que faites-vous en priorité?', 'Vous êtes témoin d\'un accident : une voiture a percuté un arbre sur une route départementale peu fréquentée. Que faites-vous en priorité?', 1, 1),
(4, 'Premiers réflexes', 'Vous êtes témoin d\'un accident : une voiture a percuté un arbre sur une route départementale peu fréquentée. Que faites-vous en priorité?', 1, 1),
(5, 'titre aide medic', 'Vous arrivez devant', 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `fk_domain_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `levels`
--

INSERT INTO `levels` (`id`, `title`, `fk_domain_id`, `time`, `difficulty`, `is_active`) VALUES
(1, 'Accident de voiture', 1, 5, 1, 1),
(2, 'Connaitre le 1722', 3, 5, 1, 1),
(3, 'niv aide medicale', 1, 3, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `fk_exercice_id` int(11) NOT NULL,
  `feedback` varchar(255) DEFAULT NULL,
  `correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `title`, `fk_exercice_id`, `feedback`, `correct`) VALUES
(6, 'Je vérifie l\'état des victimes et les extrait si je vois des signes de fumée ou de feu.', 3, 'Sécurisez d\'abord la zone. Seul un danger imminent justifie d\'extraire une victime sans formation adéquate.', 0),
(7, 'Je mets mon gilet, place le triangle et observe l\'état des victimes sans les déplacer.', 3, 'Correct! Sécurisez d\'abord les lieux et évaluez la situation sans vous mettre en danger.', 1),
(8, 'J\'appelle immédiatement le 112 pour gagner du temps et avoir des instructions.', 3, 'Sécurisez d\'abord la zone pour éviter un suraccident et évaluez la situation avant d\'appeler les secours.', 0),
(15, 'Option 1', 4, 'Option 1', 0),
(16, 'Option 1', 4, 'Option 1', 1),
(17, 'Option 1', 4, 'Option 1', 0),
(18, 'je m\'arrête (correct', 5, 'c\'est juste', 1),
(19, 'je continue', 5, 'c\'est faux', 0),
(20, '', 5, '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'Nolan', '$2y$10$DizXthKqqDtiWhGWKds0aOtqiYjs5jdXs.f6f0paMZNJQ0bOLOMKm');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `exercices`
--
ALTER TABLE `exercices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_level_id` (`fk_level_id`);

--
-- Index pour la table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_domain_id` (`fk_domain_id`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exercice_id` (`fk_exercice_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `exercices`
--
ALTER TABLE `exercices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `exercices`
--
ALTER TABLE `exercices`
  ADD CONSTRAINT `exercices_ibfk_1` FOREIGN KEY (`fk_level_id`) REFERENCES `levels` (`id`);

--
-- Contraintes pour la table `levels`
--
ALTER TABLE `levels`
  ADD CONSTRAINT `levels_ibfk_1` FOREIGN KEY (`fk_domain_id`) REFERENCES `domains` (`id`);

--
-- Contraintes pour la table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`fk_exercice_id`) REFERENCES `exercices` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
