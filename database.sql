--
-- Structure de la base de données pour 'clubspace'
--

CREATE DATABASE IF NOT EXISTS clubspace;
USE clubspace;

--
-- Table structure for table `adherents`
--

CREATE TABLE IF NOT EXISTS adherents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  club VARCHAR(100) NOT NULL,
  date_adhesion DATE NOT NULL
);

--
-- Table structure for table `comite`
--

CREATE TABLE IF NOT EXISTS comite (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  poste VARCHAR(100) NOT NULL,
  club VARCHAR(100) NOT NULL
);

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  date_event DATE NOT NULL,
  club VARCHAR(100) NOT NULL
); 