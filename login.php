<?php
session_start();
require_once 'config.php';

// Si l'utilisateur est déjà connecté, rediriger vers la page appropriée
if (isset($_SESSION['user'])) {
    if (strpos($_SESSION['user']['email'], '@comite.com') !== false) {
        header('Location: events.php');
    } else {
        header('Location: adherent.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                $_SESSION['user'] = $user;
                
                // Redirection selon le type d'utilisateur
                if (strpos($email, '@comite.com') !== false) {
                    header('Location: comite.php');
                } else {
                    header('Location: adherent.php');
                }
                exit();
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        } catch (PDOException $e) {
            $error = 'Une erreur est survenue lors de la connexion';
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSpace - Connexion</title>
    <link rel="icon" type="image/png" href="photos/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-300 to-blue-500 flex items-center justify-center">
    <div class="flex flex-col md:flex-row items-center justify-center w-full max-w-4xl mx-auto shadow-2xl rounded-2xl overflow-hidden bg-white">
        <!-- Illustration (desktop) -->
        <div class="hidden md:flex flex-col items-center justify-center bg-gradient-to-br from-blue-600 to-blue-400 p-10 w-1/2 h-full">
            <img src='https://img.icons8.com/ios-filled/100/ffffff/conference-call.png' alt="ClubSpace Logo" class="mb-6 w-24 h-24">
            <h2 class="text-3xl font-bold text-white mb-2">Bienvenue sur ClubSpace</h2>
            <p class="text-blue-100 text-lg text-center">Gérez vos clubs et événements à l'ENSAM Casablanca</p>
        </div>
        <!-- Formulaire -->
        <div class="w-full md:w-1/2 p-8 md:p-12 bg-white">
            <div class="flex flex-col items-center mb-6">
                <img src='https://img.icons8.com/ios-filled/50/4f46e5/conference-call.png' alt="Logo" class="w-16 h-16 mb-2">
                <h2 class="text-2xl font-extrabold text-blue-700 mb-1">Connexion à ClubSpace</h2>
                <p class="text-gray-500 text-sm">Accédez à votre espace membre ou comité</p>
            </div>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <form class="space-y-6" method="POST" autocomplete="on">
                <div class="space-y-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-500">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email" required autofocus
                               class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 placeholder-gray-400 text-gray-900 transition"
                               placeholder="Adresse email">
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required
                               class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 placeholder-gray-400 text-gray-900 transition"
                               placeholder="Mot de passe">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="w-full py-2 px-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                        <i class="fas fa-sign-in-alt mr-2"></i> Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 