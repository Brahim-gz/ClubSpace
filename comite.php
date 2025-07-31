<?php
session_start();
require_once 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user']) || strpos($_SESSION['user']['email'], '@comite.com') === false) {
    header('Location: login.php');
    exit();
}

// Traitement des actions CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO adherents (nom, prenom, email, telephone, club) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['club']]);
                break;
            
            case 'edit':
                $stmt = $pdo->prepare("UPDATE adherents SET nom = ?, prenom = ?, email = ?, telephone = ?, club = ?, statut = ? WHERE id = ?");
                $stmt->execute([$_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['club'], $_POST['statut'], $_POST['id']]);
                break;
            
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM adherents WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
        header('Location: comite.php');
        exit();
    }
}

// Récupération des paramètres de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$club = isset($_GET['club']) ? $_GET['club'] : '';
$statut = isset($_GET['statut']) ? $_GET['statut'] : '';

// Construction de la requête
$query = "SELECT * FROM adherents WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
}

if (!empty($club)) {
    $query .= " AND club = ?";
    $params[] = $club;
}

if (!empty($statut)) {
    $query .= " AND statut = ?";
    $params[] = $statut;
}

$query .= " ORDER BY date_inscription DESC";

// Exécution de la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$adherents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSpace - Comité</title>
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
                    <a href="events.php" class="text-gray-600 hover:text-blue-600 flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Événements
                    </a>
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
                <h1 class="text-3xl font-bold text-gray-800">Gestion des Adhérents</h1>
                <button onclick="openModal('add')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Nouvel Adhérent
                </button>
            </div>

            <!-- Filtres de recherche -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1">
                        <input type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Rechercher par nom, prénom ou email..."
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <select name="club" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="">Tous les clubs</option>
                            <option value="A&M Mechatronics" <?php echo $club === 'A&M Mechatronics' ? 'selected' : ''; ?>>A&M Mechatronics</option>
                            <option value="CRIAM" <?php echo $club === 'CRIAM' ? 'selected' : ''; ?>>CRIAM</option>
                            <option value="Aeronautics & Aerospace" <?php echo $club === 'Aeronautics & Aerospace' ? 'selected' : ''; ?>>Aeronautics & Aerospace</option>
                            <option value="Enactus" <?php echo $club === 'Enactus' ? 'selected' : ''; ?>>Enactus</option>
                            <option value="Rotaract" <?php echo $club === 'Rotaract' ? 'selected' : ''; ?>>Rotaract</option>
                            <option value="Sawaid Al Amal" <?php echo $club === 'Sawaid Al Amal' ? 'selected' : ''; ?>>Sawaid Al Amal</option>
                        </select>
                    </div>
                    <div>
                        <select name="statut" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="actif" <?php echo $statut === 'actif' ? 'selected' : ''; ?>>Actif</option>
                            <option value="inactif" <?php echo $statut === 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>Rechercher
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($adherents as $adherent): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($adherent['prenom'] . ' ' . $adherent['nom']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($adherent['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($adherent['club']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $adherent['statut'] == 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo ucfirst($adherent['statut']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openModal('edit', <?php echo htmlspecialchars(json_encode($adherent)); ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDelete(<?php echo $adherent['id']; ?>)" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modal-title">Nouvel Adhérent</h3>
                <form id="adherent-form" method="POST">
                    <input type="hidden" name="action" id="form-action" value="add">
                    <input type="hidden" name="id" id="form-id">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                        <input type="email" name="email" id="email" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="telephone">Téléphone</label>
                        <input type="tel" name="telephone" id="telephone"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="club">Club</label>
                        <select name="club" id="club" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="A&M Mechatronics">A&M Mechatronics</option>
                            <option value="CRIAM">Club Robotique & Innovation A&M</option>
                            <option value="Aeronautics & Aerospace">Aeronautics & Aerospace Club</option>
                            <option value="Enactus">Enactus ENSAM Casablanca</option>
                            <option value="Rotaract">Rotaract ENSAM Casablanca</option>
                            <option value="Sawaid Al Amal">Club Sawaid Al Amal</option>
                        </select>
                    </div>
                    
                    <div class="mb-4" id="statut-container" style="display: none;">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="statut">Statut</label>
                        <select name="statut" id="statut"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(action, data = null) {
            const modal = document.getElementById('modal');
            const form = document.getElementById('adherent-form');
            const title = document.getElementById('modal-title');
            const statutContainer = document.getElementById('statut-container');
            
            document.getElementById('form-action').value = action;
            
            if (action === 'add') {
                title.textContent = 'Nouvel Adhérent';
                form.reset();
                statutContainer.style.display = 'none';
            } else if (action === 'edit' && data) {
                title.textContent = 'Modifier Adhérent';
                document.getElementById('form-id').value = data.id;
                document.getElementById('nom').value = data.nom;
                document.getElementById('prenom').value = data.prenom;
                document.getElementById('email').value = data.email;
                document.getElementById('telephone').value = data.telephone;
                document.getElementById('club').value = data.club;
                document.getElementById('statut').value = data.statut;
                statutContainer.style.display = 'block';
            }
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet adhérent ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 