<?php
session_start();
require_once 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$isComite = strpos($_SESSION['user']['email'], '@comite.com') !== false;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_event':
                if ($isComite) {
                    $stmt = $pdo->prepare("INSERT INTO events (titre, date_event, description, club_organisateur, places_disponibles) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['titre'],
                        $_POST['date_event'],
                        $_POST['description'],
                        $_POST['club_organisateur'],
                        $_POST['places_disponibles'] ?? 50
                    ]);
                }
                break;
            
            case 'delete_event':
                if ($isComite) {
                    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->execute([$_POST['event_id']]);
                }
                break;
            
            case 'reserve':
                if (!$isComite) {
                    // Vérifier si l'adhérent n'a pas déjà réservé
                    $stmt = $pdo->prepare("SELECT id FROM reservations WHERE event_id = ? AND adherent_id = ?");
                    $stmt->execute([$_POST['event_id'], $_SESSION['user']['id']]);
                    if (!$stmt->fetch()) {
                        // Vérifier s'il reste des places
                        $stmt = $pdo->prepare("SELECT places_disponibles FROM events WHERE id = ?");
                        $stmt->execute([$_POST['event_id']]);
                        $event = $stmt->fetch();
                        
                        if ($event && $event['places_disponibles'] > 0) {
                            // Créer la réservation
                            $stmt = $pdo->prepare("INSERT INTO reservations (event_id, adherent_id) VALUES (?, ?)");
                            $stmt->execute([$_POST['event_id'], $_SESSION['user']['id']]);
                            
                            // Mettre à jour le nombre de places disponibles
                            $stmt = $pdo->prepare("UPDATE events SET places_disponibles = places_disponibles - 1 WHERE id = ?");
                            $stmt->execute([$_POST['event_id']]);
                        }
                    }
                }
                break;
        }
        header('Location: events.php');
        exit();
    }
}

// Récupération des événements
$stmt = $pdo->query("SELECT e.*, 
    (SELECT COUNT(*) FROM reservations WHERE event_id = e.id) as reservations_count 
    FROM events e 
    ORDER BY e.date_event ASC");
$events = $stmt->fetchAll();

// Récupération des réservations de l'adhérent
$reservations = [];
if (!$isComite) {
    $stmt = $pdo->prepare("SELECT event_id FROM reservations WHERE adherent_id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $reservations = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSpace - Événements</title>
    <link rel="icon" type="image/png" href="assets/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold text-blue-600">ClubSpace</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($isComite): ?>
                    <a href="comite.php" class="text-gray-600 hover:text-blue-600">Gestion des Adhérents</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-gray-600 hover:text-blue-600">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-20">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Événements à venir</h1>
                <?php if ($isComite): ?>
                <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Nouvel Événement
                </button>
                <?php endif; ?>
            </div>

            <!-- Events Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($events as $event): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($event['titre']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($event['description']); ?></p>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-calendar mr-2"></i>
                                <?php echo date('d/m/Y H:i', strtotime($event['date_event'])); ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-users mr-2"></i>
                                Organisé par <?php echo htmlspecialchars($event['club_organisateur']); ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-ticket-alt mr-2"></i>
                                Places disponibles : <?php echo $event['places_disponibles']; ?>
                            </p>
                        </div>
                        <?php if (!$isComite): ?>
                            <?php if (in_array($event['id'], $reservations)): ?>
                                <button disabled class="mt-4 w-full bg-gray-300 text-gray-600 px-4 py-2 rounded-lg">
                                    Déjà réservé
                                </button>
                            <?php else: ?>
                                <form method="POST" class="mt-4">
                                    <input type="hidden" name="action" value="reserve">
                                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                    <button type="submit" <?php echo $event['places_disponibles'] <= 0 ? 'disabled' : ''; ?>
                                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 <?php echo $event['places_disponibles'] <= 0 ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                        <?php echo $event['places_disponibles'] <= 0 ? 'Complet' : 'Réserver une place'; ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="action" value="delete_event">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')"
                                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                    Supprimer l'événement
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un événement -->
    <?php if ($isComite): ?>
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Nouvel Événement</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_event">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="titre">Titre</label>
                        <input type="text" name="titre" id="titre" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="date_event">Date et heure</label>
                        <input type="datetime-local" name="date_event" id="date_event" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                        <textarea name="description" id="description" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="club_organisateur">Club organisateur</label>
                        <select name="club_organisateur" id="club_organisateur" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="A&M Mechatronics">A&M Mechatronics</option>
                            <option value="CRIAM">Club Robotique & Innovation A&M</option>
                            <option value="Aeronautics & Aerospace">Aeronautics & Aerospace Club</option>
                            <option value="Enactus">Enactus ENSAM Casablanca</option>
                            <option value="Rotaract">Rotaract ENSAM Casablanca</option>
                            <option value="Sawaid Al Amal">Club Sawaid Al Amal</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="places_disponibles">Nombre de places</label>
                        <input type="number" name="places_disponibles" id="places_disponibles" min="1" value="50" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Créer l'événement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>
</body>
</html> 