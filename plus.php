<?php
session_start();
if (!isset($_SESSION['user']) || strpos($_SESSION['user']['email'], '@adherent.com') === false) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

// Récupérer l'ID de l'événement depuis l'URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupérer les détails de l'événement
try {
    $stmt = $pdo->prepare("SELECT e.*, 
        (SELECT COUNT(*) FROM reservations r WHERE r.event_id = e.id) as reservations_count 
        FROM events e 
        WHERE e.id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        header('Location: adherent.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: adherent.php');
    exit();
}

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserver'])) {
    $user_id = $_SESSION['user']['id'];
    
    try {
        // Vérifier si l'utilisateur a déjà réservé cet événement
        $check = $pdo->prepare("SELECT * FROM reservations WHERE event_id = ? AND user_id = ?");
        $check->execute([$event_id, $user_id]);
        
        if ($check->rowCount() === 0) {
            // Vérifier s'il reste des places
            if ($event['places_disponibles'] > 0) {
                // Créer la réservation
                $reservation = $pdo->prepare("INSERT INTO reservations (event_id, user_id) VALUES (?, ?)");
                $reservation->execute([$event_id, $user_id]);
                
                // Mettre à jour le nombre de places disponibles
                $update_places = $pdo->prepare("UPDATE events SET places_disponibles = places_disponibles - 1 WHERE id = ?");
                $update_places->execute([$event_id]);
                
                $success_message = "Réservation effectuée avec succès !";
                // Rafraîchir les données de l'événement
                $stmt->execute([$event_id]);
                $event = $stmt->fetch();
            } else {
                $error_message = "Désolé, il n'y a plus de places disponibles pour cet événement.";
            }
        } else {
            $error_message = "Vous avez déjà réservé cet événement.";
        }
    } catch (PDOException $e) {
        $error_message = "Une erreur est survenue lors de la réservation.";
    }
}

// Déterminer l'image du club
$club_image = '';
switch(strtolower($event['club_organisateur'])) {
    case 'a&m mechatronics':
        $club_image = './photos/mechatronics.png';
        break;
    case 'criam':
        $club_image = './photos/robotique.png';
        break;
    case 'aeronautics & aerospace':
        $club_image = './photos/Aeronautics.png';
        break;
    case 'enactus':
        $club_image = './photos/enactus.png';
        break;
    case 'rotaract':
        $club_image = './photos/rotaract.png';
        break;
    case 'club sawaid al amal':
        $club_image = './photos/sawaid.png';
        break;
    default:
        $club_image = './photos/default-club.png';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSpace - Plus</title>
    <link rel="icon" type="image/png" href="photos/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .gradient-text {
            background: linear-gradient(45deg, #3B82F6, #1D4ED8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-blur {
            backdrop-filter: blur(10px);
            background: rgba(59, 130, 246, 0.9);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-available {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-limited {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-full {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="nav-blur text-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="adherent.php" class="text-2xl font-bold flex items-center gap-2">
                        <i class="fas fa-users-cog"></i>
                        <span>ClubSpace</span>
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-circle text-xl"></i>
                        <span class="text-sm"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></span>
                    </div>
                    <a href="logout.php" class="glass-effect px-4 py-2 rounded-full hover:bg-white hover:text-blue-600 transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <?php if (isset($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert" data-aos="fade-right">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?php echo $success_message; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert" data-aos="fade-right">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?php echo $error_message; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden" data-aos="fade-up">
                <!-- En-tête de l'événement -->
                <div class="relative h-64 bg-gradient-to-r from-blue-500 to-blue-700">
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center text-white">
                            <h1 class="text-4xl font-bold mb-2"><?php echo htmlspecialchars($event['titre']); ?></h1>
                            <div class="flex items-center justify-center gap-4">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <?php echo date('d/m/Y', strtotime($event['date_event'])); ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    <?php echo date('H:i', strtotime($event['date_event'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations détaillées -->
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <img src="<?php echo $club_image; ?>" alt="<?php echo htmlspecialchars($event['club_organisateur']); ?>" class="w-16 h-16 rounded-full object-cover">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($event['club_organisateur']); ?></h2>
                                <p class="text-gray-600">Organisateur</p>
                            </div>
                        </div>
                        <?php
                        $status_class = 'status-available';
                        $status_text = 'Places disponibles';
                        if ($event['places_disponibles'] <= 0) {
                            $status_class = 'status-full';
                            $status_text = 'Complet';
                        } elseif ($event['places_disponibles'] <= 5) {
                            $status_class = 'status-limited';
                            $status_text = 'Plus que ' . $event['places_disponibles'] . ' places';
                        }
                        ?>
                        <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Date et Heure</h3>
                            <p class="text-gray-600">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                <?php echo date('d/m/Y H:i', strtotime($event['date_event'])); ?>
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Places</h3>
                            <p class="text-gray-600">
                                <i class="fas fa-users mr-2 text-blue-500"></i>
                                <?php echo $event['places_disponibles']; ?> places disponibles
                            </p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </p>
                    </div>


                </div>
            </div>

            <!-- Bouton retour -->
            <div class="text-center mt-8">
                <a href="adherent.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition duration-300">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste des événements
                </a>
            </div>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html> 