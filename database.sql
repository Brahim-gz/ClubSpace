--
-- Database: clubspace
--

CREATE DATABASE IF NOT EXISTS clubspace;
USE clubspace;

--
-- Table structure for table `users`
-- Stores user credentials for authentication
--
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

--
-- Table structure for table `adherents`
-- Stores club members
--
CREATE TABLE IF NOT EXISTS adherents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    club VARCHAR(100) NOT NULL,
    date_inscription DATE DEFAULT CURRENT_DATE,
    statut ENUM('actif', 'inactif') DEFAULT 'actif'
);

--
-- Table structure for table `events`
-- Stores club events
--
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_event DATETIME NOT NULL,
    club_organisateur VARCHAR(100) NOT NULL,
    places_disponibles INT NOT NULL DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--
-- Table structure for table `reservations`
-- Stores event reservations by adherents
--
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    adherent_id INT NOT NULL,
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (adherent_id) REFERENCES adherents(id) ON DELETE CASCADE
);