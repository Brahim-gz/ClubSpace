<?php
session_start();
if (!isset($_SESSION['user']) || strpos($_SESSION['user']['email'], '@adherent.com') === false) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserver'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user']['id'];
    
    try {
        // Vérifier si l'utilisateur a déjà réservé cet événement
        $check = $pdo->prepare("SELECT * FROM reservations WHERE event_id = ? AND user_id = ?");
        $check->execute([$event_id, $user_id]);
        
        if ($check->rowCount() === 0) {
            // Vérifier s'il reste des places
            $check_places = $pdo->prepare("SELECT places_disponibles FROM events WHERE id = ?");
            $check_places->execute([$event_id]);
            $event = $check_places->fetch();
            
            if ($event['places_disponibles'] > 0) {
                // Créer la réservation
                $reservation = $pdo->prepare("INSERT INTO reservations (event_id, user_id) VALUES (?, ?)");
                $reservation->execute([$event_id, $user_id]);
                
                // Mettre à jour le nombre de places disponibles
                $update_places = $pdo->prepare("UPDATE events SET places_disponibles = places_disponibles - 1 WHERE id = ?");
                $update_places->execute([$event_id]);
                
                $success_message = "Réservation effectuée avec succès !";
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

// Récupérer la liste des événements
try {
    $stmt = $pdo->query("SELECT e.*, 
        (SELECT COUNT(*) FROM reservations r WHERE r.event_id = e.id) as reservations_count 
        FROM events e 
        ORDER BY e.date_event ASC");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des événements.";
    $events = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSpace - Adhérent</title>
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
        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        .event-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .club-badge {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
                    <a href="#" class="text-2xl font-bold flex items-center gap-2">
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
        <div class="max-w-7xl mx-auto px-4">
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

            <div class="text-center mb-12" data-aos="fade-up">
                <h1 class="text-4xl font-bold gradient-text mb-4">Événements à venir</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Découvrez et réservez votre place pour les prochains événements organisés par nos clubs.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($events as $event): ?>
                    <div class="event-card rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up">
                        <?php
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
                                $club_image = './photos/default.png';
                        }
                        ?>
                        <div class="relative h-48">
                            <img src="<?php echo $club_image; ?>" alt="<?php echo htmlspecialchars($event['club_organisateur']); ?>" 
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h2 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($event['titre']); ?></h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="club-badge mb-4">
                                <i class="fas fa-users"></i>
                                <?php echo htmlspecialchars($event['club_organisateur']); ?>
                            </div>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($event['description']); ?></p>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-gray-600">
                                    <i class="far fa-calendar-alt w-6"></i>
                                    <span class="ml-2"><?php echo date('d/m/Y H:i', strtotime($event['date_event'])); ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-users w-6"></i>
                                        <span class="ml-2"><?php echo $event['places_disponibles']; ?> places</span>
                                    </div>
                                    <?php
                                    $status_class = 'status-available';
                                    $status_text = 'Disponible';
                                    if ($event['places_disponibles'] <= 5 && $event['places_disponibles'] > 0) {
                                        $status_class = 'status-limited';
                                        $status_text = 'Plus que ' . $event['places_disponibles'] . ' places';
                                    } elseif ($event['places_disponibles'] === 0) {
                                        $status_class = 'status-full';
                                        $status_text = 'Complet';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                            </div>

                            <a href="plus.php?id=<?php echo $event['id']; ?>" 
                                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center justify-center space-x-2">
                                <i class="fas fa-info-circle"></i>
                                <span>En savoir plus</span>
                            </a>

                        </div>
                    </div>
                <?php endforeach; ?>
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