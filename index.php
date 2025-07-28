<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSpace - ENSAM Casablanca</title>
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
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-blur {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="nav-blur shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold gradient-text">ClubSpace</a>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="#clubs" class="text-gray-600 hover:text-blue-600 transition duration-300 font-medium">Clubs</a>
                    <a href="#about" class="text-gray-600 hover:text-blue-600 transition duration-300 font-medium">À propos</a>
                    <a href="login.php" class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition duration-300 font-medium shadow-lg hover:shadow-xl">Connexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-20">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-800 opacity-95 hero-pattern"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-32">
            <div class="text-center" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="text-6xl md:text-7xl font-bold text-white mb-8 leading-tight">ENSAM Casablanca</h1>
                <p class="text-xl md:text-2xl text-gray-100 mb-12 max-w-3xl mx-auto leading-relaxed">
                    Découvrez l'excellence à travers nos clubs et associations étudiantes, où l'innovation rencontre l'expertise
                </p>
                <div class="flex justify-center space-x-6">
                    <a href="#clubs" class="bg-white text-blue-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Explorer les clubs
                    </a>
                    <a href="#about" class="glass-effect text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-blue-600 transition duration-300 transform hover:-translate-y-1">
                        En savoir plus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl font-bold mb-6 gradient-text">Notre Mission</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Favoriser l'épanouissement des étudiants à travers des activités extra-académiques enrichissantes et innovantes
                </p>
            </div>
        </div>
    </div>

    <!-- Clubs Section -->
    <div id="clubs" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl font-bold mb-6 gradient-text">Nos Clubs</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Découvrez nos clubs dynamiques et leurs activités passionnantes
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- A&M Mechatronics -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="h-48 relative overflow-hidden">
                        <img src="photos/mechatronics.png" alt="A&M Mechatronics" class="w-full h-full object-cover transform hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 gradient-text">A&M Mechatronics</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Spécialisé en mécatronique, ce club anime des workshops (télébot, robot suiveur de ligne) et participe à des compétitions robotisées pour mettre en pratique l'électronique, la mécanique et l'informatique embarquée.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fab fa-facebook mr-2"></i> Suivez-nous
                        </a>
                    </div>
                </div>

                <!-- CRIAM -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="h-48 relative overflow-hidden">
                        <img src="photos/robotique.png" alt="Club Robotique & Innovation A&M" class="w-full h-full object-cover transform hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 gradient-text">Club Robotique & Innovation A&M</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">CRIAM organise des confrontations robotiques et des conférences thématiques, offrant une immersion dans les technologies robotiques et l'intelligence artificielle.</p>
                        <a href="#" class="inline-flex items-center text-pink-600 hover:text-pink-800 font-medium">
                            <i class="fab fa-instagram mr-2"></i> Suivez-nous
                        </a>
                    </div>
                </div>

                <!-- Aeronautics & Aerospace -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="h-48 relative overflow-hidden">
                        <img src="photos/Aeronautics.png" alt="Aeronautics & Aerospace Club" class="w-full h-full object-cover transform hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 gradient-text">Aeronautics & Aerospace Club</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Ce club, centré sur l'aéronautique et l'espace, propose des séminaires techniques et des projets (modélisation, étude de systèmes embarqués) pour les passionnés d'ingénierie aéronautique.</p>
                    </div>
                </div>

                <!-- Enactus -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="400">
                    <div class="h-48 relative overflow-hidden">
                        <img src="photos/enactus.png" alt="Enactus ENSAM Casablanca" class="w-full h-full object-cover transform hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 gradient-text">Enactus ENSAM Casablanca</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Membre du réseau Enactus, l'équipe développe des projets d'entrepreneuriat social pour répondre à des défis environnementaux et sociétaux, formant les étudiants à l'innovation à impact.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fab fa-facebook mr-2"></i> Suivez-nous
                        </a>
                    </div>
                </div>

                <!-- Rotaract -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="500">
                    <div class="h-48 relative overflow-hidden">
                        <img src="photos/rotaract.png" alt="Rotaract ENSAM Casablanca" class="w-full h-full object-cover transform hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 gradient-text">Rotaract ENSAM Casablanca</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Club-service du Rotary, Rotaract ENSAM organise des actions de solidarité (collecte de sang, ateliers caritatifs) et développe le leadership et l'engagement citoyen de ses membres.</p>
                        <a href="#" class="inline-flex items-center text-pink-600 hover:text-pink-800 font-medium">
                            <i class="fab fa-instagram mr-2"></i> Suivez-nous
                        </a>
                    </div>
                </div>

                <!-- Sawaid Al Amal -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="600">
                    <div class="h-48 relative overflow-hidden">
                        <img src="photos/sawaid.png" alt="Club Sawaid Al Amal" class="w-full h-full object-cover transform hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-4 gradient-text">Club Sawaid Al Amal</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Engagé dans la santé et le secourisme, ce club propose des formations de premiers secours aux étudiants, notamment lors de la Semaine de la Santé Universitaire.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div>
                    <h3 class="text-2xl font-bold gradient-text mb-6">ClubSpace</h3>
                    <p class="text-gray-400 leading-relaxed">La plateforme de gestion des clubs de l'ENSAM Casablanca</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Liens rapides</h4>
                    <ul class="space-y-4">
                        <li><a href="#clubs" class="text-gray-400 hover:text-white transition duration-300">Clubs</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition duration-300">À propos</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition duration-300">Connexion</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Suivez-nous</h4>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-facebook text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-linkedin text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>© 2024 ClubSpace - ENSAM Casablanca. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        AOS.init();
    </script>
</body>
</html> 