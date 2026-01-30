<?php
/**
 * =====================================================
 * SYSTÈME DE GESTION CRUD
 * Professeurs, Emplois du temps, Élèves, Voyages scolaires
 * =====================================================
 * Fichier unique : gestion.php
 * =====================================================
 */

// =====================================================
// CONFIGURATION DE LA BASE DE DONNÉES
// =====================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_repetition');
define('DB_USER', 'root');
define('DB_PASS', '');

// =====================================================
// CLASSE DE CONNEXION À LA BASE DE DONNÉES
// =====================================================
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

// =====================================================
// CLASSE CRUD GÉNÉRIQUE
// =====================================================
class CrudManager {
    protected $pdo;
    protected $table;

    public function __construct($table) {
        $this->pdo = Database::getInstance()->getConnection();
        $this->table = $table;
    }

    public function create($data) {
        // Supprimer le champ 'id' s'il est présent avec une valeur vide
        if (isset($data['id']) && ($data['id'] === '' || $data['id'] === null)) {
            unset($data['id']);
        }
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function read($id = null) {
        if ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        }
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $data['id'] = $id;
        $sql = "UPDATE {$this->table} SET $setClause WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function search($column, $value) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE $column LIKE :value");
        $stmt->execute(['value' => "%$value%"]);
        return $stmt->fetchAll();
    }

    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $stmt->fetch()['total'];
    }
}

// =====================================================
// CLASSES SPÉCIFIQUES
// =====================================================
class ProfesseurManager extends CrudManager {
    public function __construct() {
        parent::__construct('professeurs');
    }

    public function getEmploisDuTemps($professeur_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM emplois_du_temps WHERE professeur_id = :id ORDER BY FIELD(jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut");
        $stmt->execute(['id' => $professeur_id]);
        return $stmt->fetchAll();
    }

    public function getEleves($professeur_id) {
        $stmt = $this->pdo->prepare("
            SELECT e.*, pe.matiere, pe.date_debut, pe.statut as statut_liaison
            FROM eleves e
            JOIN professeur_eleve pe ON e.id = pe.eleve_id
            WHERE pe.professeur_id = :id
        ");
        $stmt->execute(['id' => $professeur_id]);
        return $stmt->fetchAll();
    }
}

class EmploiDuTempsManager extends CrudManager {
    public function __construct() {
        parent::__construct('emplois_du_temps');
    }

    public function getByProfesseur($professeur_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM emplois_du_temps WHERE professeur_id = :id ORDER BY FIELD(jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut");
        $stmt->execute(['id' => $professeur_id]);
        return $stmt->fetchAll();
    }

    public function getAllWithProfesseur() {
        $stmt = $this->pdo->query("
            SELECT e.*, p.nom as prof_nom, p.prenom as prof_prenom 
            FROM emplois_du_temps e 
            JOIN professeurs p ON e.professeur_id = p.id 
            ORDER BY p.nom, FIELD(e.jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), e.heure_debut
        ");
        return $stmt->fetchAll();
    }
}

class EleveManager extends CrudManager {
    public function __construct() {
        parent::__construct('eleves');
    }

    public function getProfesseurs($eleve_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, pe.matiere, pe.date_debut, pe.statut as statut_liaison
            FROM professeurs p
            JOIN professeur_eleve pe ON p.id = pe.professeur_id
            WHERE pe.eleve_id = :id
        ");
        $stmt->execute(['id' => $eleve_id]);
        return $stmt->fetchAll();
    }

    public function getVoyages($eleve_id) {
        $stmt = $this->pdo->prepare("
            SELECT v.*, dv.statut_dossier, dv.paiement_effectue, dv.autorisation_parentale
            FROM voyages_scolaires v
            JOIN dossiers_voyage dv ON v.id = dv.voyage_id
            WHERE dv.eleve_id = :id
        ");
        $stmt->execute(['id' => $eleve_id]);
        return $stmt->fetchAll();
    }
}

class VoyageManager extends CrudManager {
    public function __construct() {
        parent::__construct('voyages_scolaires');
    }

    public function getParticipants($voyage_id) {
        $stmt = $this->pdo->prepare("
            SELECT e.*, dv.*
            FROM eleves e
            JOIN dossiers_voyage dv ON e.id = dv.eleve_id
            WHERE dv.voyage_id = :id
        ");
        $stmt->execute(['id' => $voyage_id]);
        return $stmt->fetchAll();
    }

    public function countParticipants($voyage_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM dossiers_voyage WHERE voyage_id = :id");
        $stmt->execute(['id' => $voyage_id]);
        return $stmt->fetch()['total'];
    }
}

class DossierVoyageManager extends CrudManager {
    public function __construct() {
        parent::__construct('dossiers_voyage');
    }

    public function getAllWithDetails() {
        $stmt = $this->pdo->query("
            SELECT dv.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.classe,
                   v.intitule as voyage_intitule, v.destination, v.date_depart
            FROM dossiers_voyage dv
            JOIN eleves e ON dv.eleve_id = e.id
            JOIN voyages_scolaires v ON dv.voyage_id = v.id
            ORDER BY v.date_depart, e.nom
        ");
        return $stmt->fetchAll();
    }

    public function getByVoyage($voyage_id) {
        $stmt = $this->pdo->prepare("
            SELECT dv.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.classe
            FROM dossiers_voyage dv
            JOIN eleves e ON dv.eleve_id = e.id
            WHERE dv.voyage_id = :id
        ");
        $stmt->execute(['id' => $voyage_id]);
        return $stmt->fetchAll();
    }
}

class ProfesseurEleveManager extends CrudManager {
    public function __construct() {
        parent::__construct('professeur_eleve');
    }

    public function getAllWithDetails() {
        $stmt = $this->pdo->query("
            SELECT pe.*, p.nom as prof_nom, p.prenom as prof_prenom, 
                   e.nom as eleve_nom, e.prenom as eleve_prenom, e.classe
            FROM professeur_eleve pe
            JOIN professeurs p ON pe.professeur_id = p.id
            JOIN eleves e ON pe.eleve_id = e.id
            ORDER BY p.nom, e.nom
        ");
        return $stmt->fetchAll();
    }
}

// =====================================================
// TRAITEMENT DES REQUÊTES AJAX
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    try {
        $action = $_POST['action'] ?? '';
        $module = $_POST['module'] ?? '';

        // Sélection du manager approprié
        switch ($module) {
            case 'professeurs':
                $manager = new ProfesseurManager();
                break;
            case 'emplois':
                $manager = new EmploiDuTempsManager();
                break;
            case 'eleves':
                $manager = new EleveManager();
                break;
            case 'voyages':
                $manager = new VoyageManager();
                break;
            case 'dossiers':
                $manager = new DossierVoyageManager();
                break;
            case 'affectations':
                $manager = new ProfesseurEleveManager();
                break;
            default:
                throw new Exception("Module inconnu");
        }

        switch ($action) {
            case 'create':
                unset($_POST['ajax'], $_POST['action'], $_POST['module']);
                // Supprimer le champ 'id' s'il est vide ou présent dans les données POST
                if (isset($_POST['id']) && ($_POST['id'] === '' || $_POST['id'] === null)) {
                    unset($_POST['id']);
                }
                $id = $manager->create($_POST);
                $response = ['success' => true, 'message' => 'Enregistrement créé avec succès', 'id' => $id];
                break;

            case 'read':
                $id = $_POST['id'] ?? null;
                $data = $manager->read($id);
                $response = ['success' => true, 'data' => $data];
                break;

            case 'update':
                $id = $_POST['id'];
                unset($_POST['ajax'], $_POST['action'], $_POST['module'], $_POST['id']);
                $manager->update($id, $_POST);
                $response = ['success' => true, 'message' => 'Enregistrement mis à jour avec succès'];
                break;

            case 'delete':
                $manager->delete($_POST['id']);
                $response = ['success' => true, 'message' => 'Enregistrement supprimé avec succès'];
                break;

            case 'search':
                $data = $manager->search($_POST['column'], $_POST['value']);
                $response = ['success' => true, 'data' => $data];
                break;

            case 'getEmplois':
                $profManager = new ProfesseurManager();
                $data = $profManager->getEmploisDuTemps($_POST['professeur_id']);
                $response = ['success' => true, 'data' => $data];
                break;

            case 'getParticipants':
                $voyageManager = new VoyageManager();
                $data = $voyageManager->getParticipants($_POST['voyage_id']);
                $response = ['success' => true, 'data' => $data];
                break;

            case 'getAllWithDetails':
                if ($module === 'dossiers') {
                    $data = $manager->getAllWithDetails();
                } elseif ($module === 'emplois') {
                    $data = $manager->getAllWithProfesseur();
                } elseif ($module === 'affectations') {
                    $data = $manager->getAllWithDetails();
                }
                $response = ['success' => true, 'data' => $data];
                break;

            case 'stats':
                $pdo = Database::getInstance()->getConnection();
                $stats = [
                    'professeurs' => $pdo->query("SELECT COUNT(*) FROM professeurs WHERE statut='actif'")->fetchColumn(),
                    'eleves' => $pdo->query("SELECT COUNT(*) FROM eleves WHERE statut='actif'")->fetchColumn(),
                    'voyages' => $pdo->query("SELECT COUNT(*) FROM voyages_scolaires WHERE statut IN ('planifie','ouvert')")->fetchColumn(),
                    'dossiers' => $pdo->query("SELECT COUNT(*) FROM dossiers_voyage")->fetchColumn()
                ];
                $response = ['success' => true, 'data' => $stats];
                break;

            default:
                throw new Exception("Action inconnue");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}

// =====================================================
// RÉCUPÉRATION DES DONNÉES POUR L'AFFICHAGE INITIAL
// =====================================================
$professeurManager = new ProfesseurManager();
$eleveManager = new EleveManager();
$voyageManager = new VoyageManager();
$emploiManager = new EmploiDuTempsManager();
$dossierManager = new DossierVoyageManager();
$affectationManager = new ProfesseurEleveManager();

$professeurs = $professeurManager->read();
$eleves = $eleveManager->read();
$voyages = $voyageManager->read();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion - Répétition Scolaire</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #0ea5e9;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: white;
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header h1 {
            color: var(--dark);
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header h1 i {
            color: var(--primary);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.prof { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .stat-icon.eleve { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }
        .stat-icon.voyage { background: linear-gradient(135deg, #22c55e, #10b981); }
        .stat-icon.dossier { background: linear-gradient(135deg, #f59e0b, #f97316); }

        .stat-info h3 {
            font-size: 1.75rem;
            color: var(--dark);
        }

        .stat-info p {
            color: var(--gray);
            font-size: 0.875rem;
        }

        /* Navigation Tabs */
        .nav-tabs {
            background: white;
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 24px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .nav-tab {
            padding: 12px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--gray);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-tab:hover {
            background: var(--light);
            color: var(--primary);
        }

        .nav-tab.active {
            background: var(--primary);
            color: white;
        }

        /* Main Content */
        .main-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .content-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .content-header h2 {
            color: var(--dark);
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-body {
            padding: 24px;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #16a34a;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: var(--gray);
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--light);
            font-weight: 600;
            color: var(--dark);
            white-space: nowrap;
        }

        tr:hover {
            background: #f1f5f9;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        /* Status Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-secondary { background: #e2e8f0; color: #475569; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlide 0.3s ease;
        }

        @keyframes modalSlide {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            color: var(--dark);
            font-size: 1.25rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--danger);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Search & Filters */
        .toolbar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        /* Tab Panels */
        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: var(--dark);
            margin-bottom: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 16px;
            }

            .header h1 {
                font-size: 1.25rem;
            }

            .nav-tabs {
                padding: 6px;
            }

            .nav-tab {
                padding: 10px 14px;
                font-size: 0.8rem;
            }

            .nav-tab span {
                display: none;
            }

            .content-header {
                flex-direction: column;
                align-items: stretch;
            }

            .modal {
                margin: 10px;
                max-height: 95vh;
            }

            th, td {
                padding: 10px 12px;
                font-size: 0.85rem;
            }

            .actions {
                flex-direction: column;
            }
        }

        /* Loading Spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Detail View */
        .detail-card {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            width: 180px;
            color: var(--gray);
        }

        .detail-value {
            flex: 1;
            color: var(--dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1><i class="fas fa-graduation-cap"></i> Gestion Scolaire</h1>
            <div style="color: var(--gray);">
                <i class="fas fa-calendar"></i> <?= date('d/m/y') ?>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon prof"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-info">
                    <h3 id="stat-professeurs"><?= count($professeurs) ?></h3>
                    <p>Professeurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon eleve"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-info">
                    <h3 id="stat-eleves"><?= count($eleves) ?></h3>
                    <p>Élèves</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon voyage"><i class="fas fa-plane"></i></div>
                <div class="stat-info">
                    <h3 id="stat-voyages"><?= count($voyages) ?></h3>
                    <p>Voyages</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon dossier"><i class="fas fa-folder-open"></i></div>
                <div class="stat-info">
                    <h3 id="stat-dossiers">0</h3>
                    <p>Dossiers voyage</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="nav-tabs">
            <button class="nav-tab active" data-tab="professeurs">
                <i class="fas fa-chalkboard-teacher"></i> <span>Professeurs</span>
            </button>
            <button class="nav-tab" data-tab="emplois">
                <i class="fas fa-calendar-alt"></i> <span>Emplois du temps</span>
            </button>
            <button class="nav-tab" data-tab="eleves">
                <i class="fas fa-user-graduate"></i> <span>Élèves</span>
            </button>
            <button class="nav-tab" data-tab="affectations">
                <i class="fas fa-link"></i> <span>Affectations</span>
            </button>
            <button class="nav-tab" data-tab="voyages">
                <i class="fas fa-plane"></i> <span>Voyages</span>
            </button>
            <button class="nav-tab" data-tab="dossiers">
                <i class="fas fa-folder-open"></i> <span>Dossiers voyage</span>
            </button>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Alert Container -->
            <div id="alert-container"></div>

            <!-- ==================== PROFESSEURS ==================== -->
            <div class="tab-panel active" id="panel-professeurs">
                <div class="content-header">
                    <h2><i class="fas fa-chalkboard-teacher"></i> Gestion des Professeurs</h2>
                    <button class="btn btn-primary" onclick="openModal('professeur')">
                        <i class="fas fa-plus"></i> Nouveau Professeur
                    </button>
                </div>
                <div class="content-body">
                    <div class="toolbar">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Rechercher un professeur..." id="search-professeurs">
                        </div>
                    </div>
                    <div class="table-container">
                        <table id="table-professeurs">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Spécialité</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($professeurs as $prof): ?>
                                <tr data-id="<?= $prof['id'] ?>">
                                    <td><?= htmlspecialchars($prof['matricule']) ?></td>
                                    <td><?= htmlspecialchars($prof['nom']) ?></td>
                                    <td><?= htmlspecialchars($prof['prenom']) ?></td>
                                    <td><?= htmlspecialchars($prof['specialite'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($prof['telephone'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $prof['statut'] === 'actif' ? 'success' : ($prof['statut'] === 'inactif' ? 'secondary' : 'warning') ?>">
                                            <?= ucfirst($prof['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-primary" onclick="viewProfesseur(<?= $prof['id'] ?>)" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editProfesseur(<?= $prof['id'] ?>)" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteProfesseur(<?= $prof['id'] ?>)" title="Supprimer">
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

            <!-- ==================== EMPLOIS DU TEMPS ==================== -->
            <div class="tab-panel" id="panel-emplois">
                <div class="content-header">
                    <h2><i class="fas fa-calendar-alt"></i> Emplois du Temps</h2>
                    <button class="btn btn-primary" onclick="openModal('emploi')">
                        <i class="fas fa-plus"></i> Nouvel Horaire
                    </button>
                </div>
                <div class="content-body">
                    <div class="toolbar">
                        <select class="form-control" id="filter-prof-emploi" style="width: auto; min-width: 200px;">
                            <option value="">Tous les professeurs</option>
                            <?php foreach ($professeurs as $prof): ?>
                            <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="table-container">
                        <table id="table-emplois">
                            <thead>
                                <tr>
                                    <th>Professeur</th>
                                    <th>Jour</th>
                                    <th>Horaire</th>
                                    <th>Matière</th>
                                    <th>Classe</th>
                                    <th>Salle</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-emplois">
                                <!-- Chargé dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== ÉLÈVES ==================== -->
            <div class="tab-panel" id="panel-eleves">
                <div class="content-header">
                    <h2><i class="fas fa-user-graduate"></i> Gestion des Élèves</h2>
                    <button class="btn btn-primary" onclick="openModal('eleve')">
                        <i class="fas fa-plus"></i> Nouvel Élève
                    </button>
                </div>
                <div class="content-body">
                    <div class="toolbar">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Rechercher un élève..." id="search-eleves">
                        </div>
                    </div>
                    <div class="table-container">
                        <table id="table-eleves">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Classe</th>
                                    <th>Parent</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eleves as $eleve): ?>
                                <tr data-id="<?= $eleve['id'] ?>">
                                    <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                                    <td><?= htmlspecialchars($eleve['nom']) ?></td>
                                    <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                                    <td><?= htmlspecialchars($eleve['classe']) ?></td>
                                    <td><?= htmlspecialchars($eleve['nom_parent'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($eleve['telephone_parent'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $eleve['statut'] === 'actif' ? 'success' : ($eleve['statut'] === 'termine' ? 'info' : 'secondary') ?>">
                                            <?= ucfirst($eleve['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-primary" onclick="viewEleve(<?= $eleve['id'] ?>)" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editEleve(<?= $eleve['id'] ?>)" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteEleve(<?= $eleve['id'] ?>)" title="Supprimer">
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

            <!-- ==================== AFFECTATIONS ==================== -->
            <div class="tab-panel" id="panel-affectations">
                <div class="content-header">
                    <h2><i class="fas fa-link"></i> Affectations Professeurs-Élèves</h2>
                    <button class="btn btn-primary" onclick="openModal('affectation')">
                        <i class="fas fa-plus"></i> Nouvelle Affectation
                    </button>
                </div>
                <div class="content-body">
                    <div class="table-container">
                        <table id="table-affectations">
                            <thead>
                                <tr>
                                    <th>Professeur</th>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Matière</th>
                                    <th>Date début</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-affectations">
                                <!-- Chargé dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== VOYAGES ==================== -->
            <div class="tab-panel" id="panel-voyages">
                <div class="content-header">
                    <h2><i class="fas fa-plane"></i> Voyages Scolaires</h2>
                    <button class="btn btn-primary" onclick="openModal('voyage')">
                        <i class="fas fa-plus"></i> Nouveau Voyage
                    </button>
                </div>
                <div class="content-body">
                    <div class="table-container">
                        <table id="table-voyages">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Intitulé</th>
                                    <th>Destination</th>
                                    <th>Dates</th>
                                    <th>Coût/Élève</th>
                                    <th>Places</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($voyages as $voyage): ?>
                                <tr data-id="<?= $voyage['id'] ?>">
                                    <td><?= htmlspecialchars($voyage['code_voyage']) ?></td>
                                    <td><?= htmlspecialchars($voyage['intitule']) ?></td>
                                    <td><?= htmlspecialchars($voyage['destination']) ?> <?= $voyage['pays'] ? '(' . htmlspecialchars($voyage['pays']) . ')' : '' ?></td>
                                    <td><?= !empty($voyage['date_depart']) && $voyage['date_depart'] != '0000-00-00' ? date('d/m/Y', strtotime($voyage['date_depart'])) : '-' ?> - <?= !empty($voyage['date_retour']) && $voyage['date_retour'] != '0000-00-00' ? date('d/m/Y', strtotime($voyage['date_retour'])) : '-' ?></td>
                                    <td><?= number_format($voyage['cout_par_eleve'] ?? 0, 2, ',', ' ') ?> €</td>
                                    <td><?= $voyage['nombre_places'] ?? '-' ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = match($voyage['statut']) {
                                            'planifie' => 'secondary',
                                            'ouvert' => 'success',
                                            'complet' => 'warning',
                                            'en_cours' => 'info',
                                            'termine' => 'info',
                                            'annule' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge badge-<?= $badgeClass ?>"><?= ucfirst($voyage['statut']) ?></span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-primary" onclick="viewVoyage(<?= $voyage['id'] ?>)" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editVoyage(<?= $voyage['id'] ?>)" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteVoyage(<?= $voyage['id'] ?>)" title="Supprimer">
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

            <!-- ==================== DOSSIERS VOYAGE ==================== -->
            <div class="tab-panel" id="panel-dossiers">
                <div class="content-header">
                    <h2><i class="fas fa-folder-open"></i> Dossiers de Voyage</h2>
                    <button class="btn btn-primary" onclick="openModal('dossier')">
                        <i class="fas fa-plus"></i> Nouveau Dossier
                    </button>
                </div>
                <div class="content-body">
                    <div class="toolbar">
                        <select class="form-control" id="filter-voyage-dossier" style="width: auto; min-width: 200px;">
                            <option value="">Tous les voyages</option>
                            <?php foreach ($voyages as $voyage): ?>
                            <option value="<?= $voyage['id'] ?>"><?= htmlspecialchars($voyage['intitule']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="table-container">
                        <table id="table-dossiers">
                            <thead>
                                <tr>
                                    <th>Voyage</th>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Autorisation</th>
                                    <th>Paiement</th>
                                    <th>Statut dossier</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-dossiers">
                                <!-- Chargé dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- ==================== MODALS ==================== -->
    
    <!-- Modal Professeur -->
    <div class="modal-overlay" id="modal-professeur">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-professeur-title">Nouveau Professeur</h3>
                <button class="modal-close" onclick="closeModal('professeur')">&times;</button>
            </div>
            <form id="form-professeur" onsubmit="saveProfesseur(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="prof-id">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Matricule <span class="required">*</span></label>
                            <input type="text" class="form-control" name="matricule" id="prof-matricule" required>
                        </div>
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" name="statut" id="prof-statut">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="suspendu">Suspendu</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom <span class="required">*</span></label>
                            <input type="text" class="form-control" name="nom" id="prof-nom" required>
                        </div>
                        <div class="form-group">
                            <label>Prénom <span class="required">*</span></label>
                            <input type="text" class="form-control" name="prenom" id="prof-prenom" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="prof-email">
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" class="form-control" name="telephone" id="prof-telephone">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Spécialité</label>
                            <input type="text" class="form-control" name="specialite" id="prof-specialite">
                        </div>
                        <div class="form-group">
                            <label>Niveau d'enseignement</label>
                            <select class="form-control" name="niveau_enseignement" id="prof-niveau">
                                <option value="">Sélectionner</option>
                                <option value="Primaire">Primaire</option>
                                <option value="Collège">Collège</option>
                                <option value="Lycée">Lycée</option>
                                <option value="Primaire/Collège">Primaire/Collège</option>
                                <option value="Collège/Lycée">Collège/Lycée</option>
                                <option value="Tous niveaux">Tous niveaux</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" class="form-control" name="date_naissance" id="prof-date-naissance">
                        </div>
                        <div class="form-group">
                            <label>Date d'embauche</label>
                            <input type="date" class="form-control" name="date_embauche" id="prof-date-embauche">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <textarea class="form-control" name="adresse" id="prof-adresse" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('professeur')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Emploi du temps -->
    <div class="modal-overlay" id="modal-emploi">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-emploi-title">Nouvel Horaire</h3>
                <button class="modal-close" onclick="closeModal('emploi')">&times;</button>
            </div>
            <form id="form-emploi" onsubmit="saveEmploi(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="emploi-id">
                    <div class="form-group">
                        <label>Professeur <span class="required">*</span></label>
                        <select class="form-control" name="professeur_id" id="emploi-professeur" required>
                            <option value="">Sélectionner un professeur</option>
                            <?php foreach ($professeurs as $prof): ?>
                            <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Jour <span class="required">*</span></label>
                            <select class="form-control" name="jour_semaine" id="emploi-jour" required>
                                <option value="">Sélectionner</option>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                                <option value="Samedi">Samedi</option>
                                <option value="Dimanche">Dimanche</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type de cours</label>
                            <select class="form-control" name="type_cours" id="emploi-type">
                                <option value="groupe">Groupe</option>
                                <option value="individuel">Individuel</option>
                                <option value="classe">Classe</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Heure début <span class="required">*</span></label>
                            <input type="time" class="form-control" name="heure_debut" id="emploi-debut" required>
                        </div>
                        <div class="form-group">
                            <label>Heure fin <span class="required">*</span></label>
                            <input type="time" class="form-control" name="heure_fin" id="emploi-fin" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Matière <span class="required">*</span></label>
                            <input type="text" class="form-control" name="matiere" id="emploi-matiere" required>
                        </div>
                        <div class="form-group">
                            <label>Classe</label>
                            <input type="text" class="form-control" name="classe" id="emploi-classe">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Salle</label>
                        <input type="text" class="form-control" name="salle" id="emploi-salle">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" id="emploi-notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('emploi')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Élève -->
    <div class="modal-overlay" id="modal-eleve">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-eleve-title">Nouvel Élève</h3>
                <button class="modal-close" onclick="closeModal('eleve')">&times;</button>
            </div>
            <form id="form-eleve" onsubmit="saveEleve(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="eleve-id">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Matricule <span class="required">*</span></label>
                            <input type="text" class="form-control" name="matricule" id="eleve-matricule" required>
                        </div>
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" name="statut" id="eleve-statut">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom <span class="required">*</span></label>
                            <input type="text" class="form-control" name="nom" id="eleve-nom" required>
                        </div>
                        <div class="form-group">
                            <label>Prénom <span class="required">*</span></label>
                            <input type="text" class="form-control" name="prenom" id="eleve-prenom" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" class="form-control" name="date_naissance" id="eleve-date-naissance">
                        </div>
                        <div class="form-group">
                            <label>Sexe <span class="required">*</span></label>
                            <select class="form-control" name="sexe" id="eleve-sexe" required>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Classe <span class="required">*</span></label>
                            <input type="text" class="form-control" name="classe" id="eleve-classe" required>
                        </div>
                        <div class="form-group">
                            <label>Niveau</label>
                            <select class="form-control" name="niveau" id="eleve-niveau">
                                <option value="">Sélectionner</option>
                                <option value="Primaire">Primaire</option>
                                <option value="Collège">Collège</option>
                                <option value="Lycée">Lycée</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>École d'origine</label>
                        <input type="text" class="form-control" name="ecole_origine" id="eleve-ecole">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom du parent</label>
                            <input type="text" class="form-control" name="nom_parent" id="eleve-parent">
                        </div>
                        <div class="form-group">
                            <label>Téléphone parent</label>
                            <input type="tel" class="form-control" name="telephone_parent" id="eleve-tel-parent">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email parent</label>
                        <input type="email" class="form-control" name="email_parent" id="eleve-email-parent">
                    </div>
                    <div class="form-group">
                        <label>Matières de répétition</label>
                        <textarea class="form-control" name="matieres_repetition" id="eleve-matieres" rows="2" placeholder="Ex: Mathématiques, Français, Anglais"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Date d'inscription</label>
                        <input type="date" class="form-control" name="date_inscription" id="eleve-date-inscription">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('eleve')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Affectation -->
    <div class="modal-overlay" id="modal-affectation">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-affectation-title">Nouvelle Affectation</h3>
                <button class="modal-close" onclick="closeModal('affectation')">&times;</button>
            </div>
            <form id="form-affectation" onsubmit="saveAffectation(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="affectation-id">
                    <div class="form-group">
                        <label>Professeur <span class="required">*</span></label>
                        <select class="form-control" name="professeur_id" id="affectation-professeur" required>
                            <option value="">Sélectionner un professeur</option>
                            <?php foreach ($professeurs as $prof): ?>
                            <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nom'] . ' ' . $prof['prenom'] . ' - ' . ($prof['specialite'] ?? 'N/A')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Élève <span class="required">*</span></label>
                        <select class="form-control" name="eleve_id" id="affectation-eleve" required>
                            <option value="">Sélectionner un élève</option>
                            <?php foreach ($eleves as $eleve): ?>
                            <option value="<?= $eleve['id'] ?>"><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom'] . ' - ' . $eleve['classe']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Matière</label>
                            <input type="text" class="form-control" name="matiere" id="affectation-matiere">
                        </div>
                        <div class="form-group">
                            <label>Date de début</label>
                            <input type="date" class="form-control" name="date_debut" id="affectation-debut">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select class="form-control" name="statut" id="affectation-statut">
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="suspendu">Suspendu</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('affectation')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Voyage -->
    <div class="modal-overlay" id="modal-voyage">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-voyage-title">Nouveau Voyage</h3>
                <button class="modal-close" onclick="closeModal('voyage')">&times;</button>
            </div>
            <form id="form-voyage" onsubmit="saveVoyage(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="voyage-id">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Code voyage <span class="required">*</span></label>
                            <input type="text" class="form-control" name="code_voyage" id="voyage-code" required>
                        </div>
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" name="statut" id="voyage-statut">
                                <option value="planifie">Planifié</option>
                                <option value="ouvert">Ouvert</option>
                                <option value="complet">Complet</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Intitulé <span class="required">*</span></label>
                        <input type="text" class="form-control" name="intitule" id="voyage-intitule" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Destination <span class="required">*</span></label>
                            <input type="text" class="form-control" name="destination" id="voyage-destination" required>
                        </div>
                        <div class="form-group">
                            <label>Pays</label>
                            <input type="text" class="form-control" name="pays" id="voyage-pays">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de départ <span class="required">*</span></label>
                            <input type="date" class="form-control" name="date_depart" id="voyage-depart" required>
                        </div>
                        <div class="form-group">
                            <label>Date de retour <span class="required">*</span></label>
                            <input type="date" class="form-control" name="date_retour" id="voyage-retour" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Coût par élève (€)</label>
                            <input type="number" step="0.01" class="form-control" name="cout_par_eleve" id="voyage-cout">
                        </div>
                        <div class="form-group">
                            <label>Nombre de places</label>
                            <input type="number" class="form-control" name="nombre_places" id="voyage-places">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Responsable du voyage</label>
                        <input type="text" class="form-control" name="responsable_voyage" id="voyage-responsable">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="voyage-description" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Objectif pédagogique</label>
                        <textarea class="form-control" name="objectif_pedagogique" id="voyage-objectif" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Documents requis</label>
                        <textarea class="form-control" name="documents_requis" id="voyage-documents" rows="2" placeholder="Ex: Passeport, Autorisation parentale, Certificat médical"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('voyage')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Dossier Voyage -->
    <div class="modal-overlay" id="modal-dossier">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-dossier-title">Nouveau Dossier de Voyage</h3>
                <button class="modal-close" onclick="closeModal('dossier')">&times;</button>
            </div>
            <form id="form-dossier" onsubmit="saveDossier(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="dossier-id">
                    <div class="form-group">
                        <label>Voyage <span class="required">*</span></label>
                        <select class="form-control" name="voyage_id" id="dossier-voyage" required>
                            <option value="">Sélectionner un voyage</option>
                            <?php foreach ($voyages as $voyage): ?>
                            <option value="<?= $voyage['id'] ?>"><?= htmlspecialchars($voyage['intitule'] . ' - ' . $voyage['destination']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Élève <span class="required">*</span></label>
                        <select class="form-control" name="eleve_id" id="dossier-eleve" required>
                            <option value="">Sélectionner un élève</option>
                            <?php foreach ($eleves as $eleve): ?>
                            <option value="<?= $eleve['id'] ?>"><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom'] . ' - ' . $eleve['classe']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date d'inscription <span class="required">*</span></label>
                        <input type="date" class="form-control" name="date_inscription" id="dossier-date" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Autorisation parentale</label>
                            <select class="form-control" name="autorisation_parentale" id="dossier-autorisation">
                                <option value="en_attente">En attente</option>
                                <option value="oui">Oui</option>
                                <option value="non">Non</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Paiement effectué</label>
                            <select class="form-control" name="paiement_effectue" id="dossier-paiement">
                                <option value="non">Non</option>
                                <option value="partiel">Partiel</option>
                                <option value="oui">Oui</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Montant payé (€)</label>
                            <input type="number" step="0.01" class="form-control" name="montant_paye" id="dossier-montant" value="0">
                        </div>
                        <div class="form-group">
                            <label>Assurance voyage</label>
                            <select class="form-control" name="assurance_voyage" id="dossier-assurance">
                                <option value="non">Non</option>
                                <option value="oui">Oui</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Numéro de passeport</label>
                            <input type="text" class="form-control" name="numero_passeport" id="dossier-passeport">
                        </div>
                        <div class="form-group">
                            <label>Date expiration passeport</label>
                            <input type="date" class="form-control" name="date_expiration_passeport" id="dossier-exp-passeport">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Documents fournis</label>
                        <textarea class="form-control" name="documents_fournis" id="dossier-documents" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Allergies / Informations médicales</label>
                        <textarea class="form-control" name="allergies_medicales" id="dossier-allergies" rows="2"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact d'urgence</label>
                            <input type="text" class="form-control" name="contact_urgence" id="dossier-contact">
                        </div>
                        <div class="form-group">
                            <label>Téléphone urgence</label>
                            <input type="tel" class="form-control" name="telephone_urgence" id="dossier-tel-urgence">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Statut du dossier</label>
                        <select class="form-control" name="statut_dossier" id="dossier-statut">
                            <option value="incomplet">Incomplet</option>
                            <option value="complet">Complet</option>
                            <option value="valide">Validé</option>
                            <option value="refuse">Refusé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Observations</label>
                        <textarea class="form-control" name="observations" id="dossier-observations" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('dossier')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Détail (générique) -->
    <div class="modal-overlay" id="modal-detail">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-detail-title">Détails</h3>
                <button class="modal-close" onclick="closeModal('detail')">&times;</button>
            </div>
            <div class="modal-body" id="modal-detail-content">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('detail')">Fermer</button>
            </div>
        </div>
    </div>

    <script>



// Ajouter cette fonction en haut du script JavaScript (après les utilitaires)
function formatDateFR(dateString) {
    if (!dateString || dateString === '0000-00-00') return '-';
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function formatDateForInput(dateString) {
    if (!dateString || dateString === '0000-00-00') return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}
    // =====================================================
    // GESTION DES ONGLETS
    // =====================================================
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Désactiver tous les onglets
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            
            // Activer l'onglet cliqué
            this.classList.add('active');
            document.getElementById('panel-' + this.dataset.tab).classList.add('active');
            
            // Charger les données si nécessaire
            if (this.dataset.tab === 'emplois') loadEmplois();
            if (this.dataset.tab === 'affectations') loadAffectations();
            if (this.dataset.tab === 'dossiers') loadDossiers();
        });
    });

    // =====================================================
    // FONCTIONS UTILITAIRES
    // =====================================================
    function showAlert(message, type = 'success') {
        const container = document.getElementById('alert-container');
        container.innerHTML = `
            <div class="alert alert-${type}">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
            </div>
        `;
        setTimeout(() => container.innerHTML = '', 5000);
    }

    function openModal(type) {
        document.getElementById('modal-' + type).classList.add('active');
        document.getElementById('form-' + type)?.reset();
        document.getElementById(type + '-id')?.setAttribute('value', '');
        
        const title = document.getElementById('modal-' + type + '-title');
        if (title) {
            const titles = {
                'professeur': 'Nouveau Professeur',
                'emploi': 'Nouvel Horaire',
                'eleve': 'Nouvel Élève',
                'affectation': 'Nouvelle Affectation',
                'voyage': 'Nouveau Voyage',
                'dossier': 'Nouveau Dossier de Voyage'
            };
            title.textContent = titles[type] || 'Nouveau';
        }
    }

    function closeModal(type) {
        document.getElementById('modal-' + type).classList.remove('active');
    }

    async function ajaxRequest(data) {
        const formData = new FormData();
        formData.append('ajax', '1');
        for (let key in data) {
            formData.append(key, data[key]);
        }
        
        const response = await fetch('gestion.php', {
            method: 'POST',
            body: formData
        });
        return response.json();
    }

    function updateStats() {
        ajaxRequest({ action: 'stats', module: 'professeurs' }).then(result => {
            if (result.success) {
                document.getElementById('stat-professeurs').textContent = result.data.professeurs;
                document.getElementById('stat-eleves').textContent = result.data.eleves;
                document.getElementById('stat-voyages').textContent = result.data.voyages;
                document.getElementById('stat-dossiers').textContent = result.data.dossiers;
            }
        });
    }

    // =====================================================
    // CRUD PROFESSEURS
    // =====================================================
    async function saveProfesseur(e) {
        e.preventDefault();
        const form = document.getElementById('form-professeur');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.module = 'professeurs';
        data.action = data.id ? 'update' : 'create';
        
        const result = await ajaxRequest(data);
        if (result.success) {
            showAlert(result.message);
            closeModal('professeur');
            location.reload();
        } else {
            showAlert(result.message, 'error');
        }
    }

    async function editProfesseur(id) {
        const result = await ajaxRequest({ action: 'read', module: 'professeurs', id: id });
        if (result.success && result.data) {
            const p = result.data;
            document.getElementById('prof-id').value = p.id;
            document.getElementById('prof-matricule').value = p.matricule;
            document.getElementById('prof-nom').value = p.nom;
            document.getElementById('prof-prenom').value = p.prenom;
            document.getElementById('prof-email').value = p.email || '';
            document.getElementById('prof-telephone').value = p.telephone || '';
            document.getElementById('prof-specialite').value = p.specialite || '';
            document.getElementById('prof-niveau').value = p.niveau_enseignement || '';
            document.getElementById('prof-date-naissance').value = p.date_naissance || '';
            document.getElementById('prof-date-embauche').value = p.date_embauche || '';
            document.getElementById('prof-adresse').value = p.adresse || '';
            document.getElementById('prof-statut').value = p.statut;
            
            document.getElementById('modal-professeur-title').textContent = 'Modifier le Professeur';
            document.getElementById('modal-professeur').classList.add('active');
        }
    }

    async function viewProfesseur(id) {
        const result = await ajaxRequest({ action: 'read', module: 'professeurs', id: id });
        if (result.success && result.data) {
            const p = result.data;
            document.getElementById('modal-detail-title').textContent = 'Détails du Professeur';
            document.getElementById('modal-detail-content').innerHTML = `
                <div class="detail-card">
                    <div class="detail-row"><span class="detail-label">Matricule</span><span class="detail-value">${p.matricule}</span></div>
                    <div class="detail-row"><span class="detail-label">Nom complet</span><span class="detail-value">${p.nom} ${p.prenom}</span></div>
                    <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value">${p.email || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Téléphone</span><span class="detail-value">${p.telephone || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Spécialité</span><span class="detail-value">${p.specialite || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Niveau</span><span class="detail-value">${p.niveau_enseignement || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Date de naissance</span><span class="detail-value">${p.date_naissance && p.date_naissance !== '0000-00-00' ? formatDateFR(p.date_naissance) : '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Date d'embauche</span><span class="detail-value">${p.date_embauche ? new Date(p.date_embauche).formatDateFR('fr-FR') : '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Adresse</span><span class="detail-value">${p.adresse || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span class="badge badge-${p.statut === 'actif' ? 'success' : 'secondary'}">${p.statut}</span></span></div>
                </div>
            `;
            document.getElementById('modal-detail').classList.add('active');
        }
    }

    async function deleteProfesseur(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce professeur ?')) {
            const result = await ajaxRequest({ action: 'delete', module: 'professeurs', id: id });
            if (result.success) {
                showAlert(result.message);
                document.querySelector(`#table-professeurs tr[data-id="${id}"]`).remove();
                updateStats();
            } else {
                showAlert(result.message, 'error');
            }
        }
    }

    // Recherche professeurs
    document.getElementById('search-professeurs').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('#table-professeurs tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });

    // =====================================================
    // CRUD EMPLOIS DU TEMPS
    // =====================================================
    async function loadEmplois() {
        const result = await ajaxRequest({ action: 'getAllWithDetails', module: 'emplois' });
        if (result.success) {
            const tbody = document.getElementById('tbody-emplois');
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="empty-state"><i class="fas fa-calendar-times"></i><h3>Aucun horaire</h3><p>Ajoutez un nouvel horaire pour commencer</p></td></tr>';
                return;
            }
            tbody.innerHTML = result.data.map(e => `
                <tr data-id="${e.id}">
                    <td>${e.prof_nom} ${e.prof_prenom}</td>
                    <td>${e.jour_semaine}</td>
                    <td>${e.heure_debut.substring(0,5)} - ${e.heure_fin.substring(0,5)}</td>
                    <td>${e.matiere}</td>
                    <td>${e.classe || '-'}</td>
                    <td>${e.salle || '-'}</td>
                    <td><span class="badge badge-info">${e.type_cours}</span></td>
                    <td class="actions">
                        <button class="btn btn-sm btn-warning" onclick="editEmploi(${e.id})" title="Modifier"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteEmploi(${e.id})" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    }

    async function saveEmploi(e) {
        e.preventDefault();
        const form = document.getElementById('form-emploi');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.module = 'emplois';
        data.action = data.id ? 'update' : 'create';
        
        const result = await ajaxRequest(data);
        if (result.success) {
            showAlert(result.message);
            closeModal('emploi');
            loadEmplois();
        } else {
            showAlert(result.message, 'error');
        }
    }

    async function editEmploi(id) {
        const result = await ajaxRequest({ action: 'read', module: 'emplois', id: id });
        if (result.success && result.data) {
            const e = result.data;
            document.getElementById('emploi-id').value = e.id;
            document.getElementById('emploi-professeur').value = e.professeur_id;
            document.getElementById('emploi-jour').value = e.jour_semaine;
            document.getElementById('emploi-debut').value = e.heure_debut;
            document.getElementById('emploi-fin').value = e.heure_fin;
            document.getElementById('emploi-matiere').value = e.matiere;
            document.getElementById('emploi-classe').value = e.classe || '';
            document.getElementById('emploi-salle').value = e.salle || '';
            document.getElementById('emploi-type').value = e.type_cours;
            document.getElementById('emploi-notes').value = e.notes || '';
            
            document.getElementById('modal-emploi-title').textContent = "Modifier l'Horaire";
            document.getElementById('modal-emploi').classList.add('active');
        }
    }

    async function deleteEmploi(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet horaire ?')) {
            const result = await ajaxRequest({ action: 'delete', module: 'emplois', id: id });
            if (result.success) {
                showAlert(result.message);
                loadEmplois();
            } else {
                showAlert(result.message, 'error');
            }
        }
    }

    // =====================================================
    // CRUD ÉLÈVES
    // =====================================================
    async function saveEleve(e) {
        e.preventDefault();
        const form = document.getElementById('form-eleve');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.module = 'eleves';
        data.action = data.id ? 'update' : 'create';
        
        const result = await ajaxRequest(data);
        if (result.success) {
            showAlert(result.message);
            closeModal('eleve');
            location.reload();
        } else {
            showAlert(result.message, 'error');
        }
    }

    async function editEleve(id) {
        const result = await ajaxRequest({ action: 'read', module: 'eleves', id: id });
        if (result.success && result.data) {
            const e = result.data;
            document.getElementById('eleve-id').value = e.id;
            document.getElementById('eleve-matricule').value = e.matricule;
            document.getElementById('eleve-nom').value = e.nom;
            document.getElementById('eleve-prenom').value = e.prenom;
            document.getElementById('eleve-date-naissance').value = e.date_naissance || '';
            document.getElementById('eleve-sexe').value = e.sexe;
            document.getElementById('eleve-classe').value = e.classe;
            document.getElementById('eleve-niveau').value = e.niveau || '';
            document.getElementById('eleve-ecole').value = e.ecole_origine || '';
            document.getElementById('eleve-parent').value = e.nom_parent || '';
            document.getElementById('eleve-tel-parent').value = e.telephone_parent || '';
            document.getElementById('eleve-email-parent').value = e.email_parent || '';
            document.getElementById('eleve-matieres').value = e.matieres_repetition || '';
            document.getElementById('eleve-date-inscription').value = e.date_inscription || '';
            document.getElementById('eleve-statut').value = e.statut;
            
            document.getElementById('modal-eleve-title').textContent = "Modifier l'Élève";
            document.getElementById('modal-eleve').classList.add('active');
        }
    }

    async function viewEleve(id) {
        const result = await ajaxRequest({ action: 'read', module: 'eleves', id: id });
        if (result.success && result.data) {
            const e = result.data;
            document.getElementById('modal-detail-title').textContent = "Détails de l'Élève";
            document.getElementById('modal-detail-content').innerHTML = `
                <div class="detail-card">
                    <div class="detail-row"><span class="detail-label">Matricule</span><span class="detail-value">${e.matricule}</span></div>
                    <div class="detail-row"><span class="detail-label">Nom complet</span><span class="detail-value">${e.nom} ${e.prenom}</span></div>
                    <div class="detail-row"><span class="detail-label">Sexe</span><span class="detail-value">${e.sexe === 'M' ? 'Masculin' : 'Féminin'}</span></div>
                    <div class="detail-row"><span class="detail-label">Date de naissance</span><span class="detail-value">${e.date_naissance ? new Date(e.date_naissance).formatDateFR('fr-FR') : '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Classe</span><span class="detail-value">${e.classe}</span></div>
                    <div class="detail-row"><span class="detail-label">Niveau</span><span class="detail-value">${e.niveau || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">École d'origine</span><span class="detail-value">${e.ecole_origine || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Matières de répétition</span><span class="detail-value">${e.matieres_repetition || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Parent</span><span class="detail-value">${e.nom_parent || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Téléphone parent</span><span class="detail-value">${e.telephone_parent || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Email parent</span><span class="detail-value">${e.email_parent || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Date d'inscription</span><span class="detail-value">${e.date_inscription ? new Date(e.date_inscription).formatDateFR('fr-FR') : '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span class="badge badge-${e.statut === 'actif' ? 'success' : 'secondary'}">${e.statut}</span></span></div>
                </div>
            `;
            document.getElementById('modal-detail').classList.add('active');
        }
    }

    async function deleteEleve(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')) {
            const result = await ajaxRequest({ action: 'delete', module: 'eleves', id: id });
            if (result.success) {
                showAlert(result.message);
                document.querySelector(`#table-eleves tr[data-id="${id}"]`).remove();
                updateStats();
            } else {
                showAlert(result.message, 'error');
            }
        }
    }

    // Recherche élèves
    document.getElementById('search-eleves').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('#table-eleves tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });

    // =====================================================
    // CRUD AFFECTATIONS
    // =====================================================
    async function loadAffectations() {
        const result = await ajaxRequest({ action: 'getAllWithDetails', module: 'affectations' });
        if (result.success) {
            const tbody = document.getElementById('tbody-affectations');
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><i class="fas fa-link"></i><h3>Aucune affectation</h3><p>Créez une affectation professeur-élève</p></td></tr>';
                return;
            }
            tbody.innerHTML = result.data.map(a => `
                <tr data-id="${a.id}">
                    <td>${a.prof_nom} ${a.prof_prenom}</td>
                    <td>${a.eleve_nom} ${a.eleve_prenom}</td>
                    <td>${a.classe}</td>
                    <td>${a.matiere || '-'}</td>
                    <td>${a.date_debut ? new Date(a.date_debut).formatDateFR('fr-FR') : '-'}</td>
                    <td><span class="badge badge-${a.statut === 'en_cours' ? 'success' : a.statut === 'termine' ? 'info' : 'warning'}">${a.statut.replace('_', ' ')}</span></td>
                    <td class="actions">
                        <button class="btn btn-sm btn-warning" onclick="editAffectation(${a.id})" title="Modifier"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAffectation(${a.id})" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    }

    async function saveAffectation(e) {
        e.preventDefault();
        const form = document.getElementById('form-affectation');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.module = 'affectations';
        data.action = data.id ? 'update' : 'create';
        
        const result = await ajaxRequest(data);
        if (result.success) {
            showAlert(result.message);
            closeModal('affectation');
            loadAffectations();
        } else {
            showAlert(result.message, 'error');
        }
    }

    async function editAffectation(id) {
        const result = await ajaxRequest({ action: 'read', module: 'affectations', id: id });
        if (result.success && result.data) {
            const a = result.data;
            document.getElementById('affectation-id').value = a.id;
            document.getElementById('affectation-professeur').value = a.professeur_id;
            document.getElementById('affectation-eleve').value = a.eleve_id;
            document.getElementById('affectation-matiere').value = a.matiere || '';
            document.getElementById('affectation-debut').value = a.date_debut || '';
            document.getElementById('affectation-statut').value = a.statut;
            
            document.getElementById('modal-affectation-title').textContent = "Modifier l'Affectation";
            document.getElementById('modal-affectation').classList.add('active');
        }
    }

    async function deleteAffectation(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette affectation ?')) {
            const result = await ajaxRequest({ action: 'delete', module: 'affectations', id: id });
            if (result.success) {
                showAlert(result.message);
                loadAffectations();
            } else {
                showAlert(result.message, 'error');
            }
        }
    }

    // =====================================================
    // CRUD VOYAGES
    // =====================================================
    async function saveVoyage(e) {
        e.preventDefault();
        const form = document.getElementById('form-voyage');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.module = 'voyages';
        data.action = data.id ? 'update' : 'create';
        
        const result = await ajaxRequest(data);
        if (result.success) {
            showAlert(result.message);
            closeModal('voyage');
            location.reload();
        } else {
            showAlert(result.message, 'error');
        }
    }

    async function editVoyage(id) {
        const result = await ajaxRequest({ action: 'read', module: 'voyages', id: id });
        if (result.success && result.data) {
            const v = result.data;
            document.getElementById('voyage-id').value = v.id;
            document.getElementById('voyage-code').value = v.code_voyage;
            document.getElementById('voyage-intitule').value = v.intitule;
            document.getElementById('voyage-destination').value = v.destination;
            document.getElementById('voyage-pays').value = v.pays || '';
            document.getElementById('voyage-depart').value = v.date_depart;
            document.getElementById('voyage-retour').value = v.date_retour;
            document.getElementById('voyage-cout').value = v.cout_par_eleve || '';
            document.getElementById('voyage-places').value = v.nombre_places || '';
            document.getElementById('voyage-responsable').value = v.responsable_voyage || '';
            document.getElementById('voyage-description').value = v.description || '';
            document.getElementById('voyage-objectif').value = v.objectif_pedagogique || '';
            document.getElementById('voyage-documents').value = v.documents_requis || '';
            document.getElementById('voyage-statut').value = v.statut;
            
            document.getElementById('modal-voyage-title').textContent = 'Modifier le Voyage';
            document.getElementById('modal-voyage').classList.add('active');
        }
    }

    async function viewVoyage(id) {
        const result = await ajaxRequest({ action: 'read', module: 'voyages', id: id });
        if (result.success && result.data) {
            const v = result.data;
            document.getElementById('modal-detail-title').textContent = 'Détails du Voyage';
            document.getElementById('modal-detail-content').innerHTML = `
                <div class="detail-card">
                    <div class="detail-row"><span class="detail-label">Code</span><span class="detail-value">${v.code_voyage}</span></div>
                    <div class="detail-row"><span class="detail-label">Intitulé</span><span class="detail-value">${v.intitule}</span></div>
                    <div class="detail-row"><span class="detail-label">Destination</span><span class="detail-value">${v.destination} ${v.pays ? '(' + v.pays + ')' : ''}</span></div>
                    <div class="detail-row"><span class="detail-label">Dates</span><span class="detail-value">${new Date(v.date_depart).formatDateFR('fr-FR')} - ${new Date(v.date_retour).formatDateFR()('fr-FR')}</span></div>
                    <div class="detail-row"><span class="detail-label">Coût par élève</span><span class="detail-value">${v.cout_par_eleve ? v.cout_par_eleve + ' €' : '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Places disponibles</span><span class="detail-value">${v.nombre_places || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Responsable</span><span class="detail-value">${v.responsable_voyage || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Description</span><span class="detail-value">${v.description || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Objectif pédagogique</span><span class="detail-value">${v.objectif_pedagogique || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Documents requis</span><span class="detail-value">${v.documents_requis || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span class="badge badge-info">${v.statut}</span></span></div>
                </div>
            `;
            document.getElementById('modal-detail').classList.add('active');
        }
    }

    async function deleteVoyage(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce voyage ? Tous les dossiers associés seront également supprimés.')) {
            const result = await ajaxRequest({ action: 'delete', module: 'voyages', id: id });
            if (result.success) {
                showAlert(result.message);
                document.querySelector(`#table-voyages tr[data-id="${id}"]`).remove();
                updateStats();
            } else {
                showAlert(result.message, 'error');
            }
        }
    }

    // =====================================================
    // CRUD DOSSIERS VOYAGE
    // =====================================================
    async function loadDossiers() {
        const result = await ajaxRequest({ action: 'getAllWithDetails', module: 'dossiers' });
        if (result.success) {
            const tbody = document.getElementById('tbody-dossiers');
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><i class="fas fa-folder-open"></i><h3>Aucun dossier</h3><p>Créez un nouveau dossier de voyage</p></td></tr>';
                return;
            }
            tbody.innerHTML = result.data.map(d => `
                <tr data-id="${d.id}">
                    <td>${d.voyage_intitule}</td>
                    <td>${d.eleve_nom} ${d.eleve_prenom}</td>
                    <td>${d.classe}</td>
                    <td><span class="badge badge-${d.autorisation_parentale === 'oui' ? 'success' : d.autorisation_parentale === 'non' ? 'danger' : 'warning'}">${d.autorisation_parentale}</span></td>
                    <td><span class="badge badge-${d.paiement_effectue === 'oui' ? 'success' : d.paiement_effectue === 'partiel' ? 'warning' : 'danger'}">${d.paiement_effectue}</span></td>
                    <td><span class="badge badge-${d.statut_dossier === 'valide' ? 'success' : d.statut_dossier === 'complet' ? 'info' : d.statut_dossier === 'refuse' ? 'danger' : 'warning'}">${d.statut_dossier}</span></td>
                    <td class="actions">
                        <button class="btn btn-sm btn-primary" onclick="viewDossier(${d.id})" title="Voir"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-warning" onclick="editDossier(${d.id})" title="Modifier"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteDossier(${d.id})" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
            updateStats();
        }
    }

    async function saveDossier(e) {
        e.preventDefault();
        const form = document.getElementById('form-dossier');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.module = 'dossiers';
        data.action = data.id ? 'update' : 'create';
        
        const result = await ajaxRequest(data);
        if (result.success) {
            showAlert(result.message);
            closeModal('dossier');
            loadDossiers();
        } else {
            showAlert(result.message, 'error');
        }
    }

    async function editDossier(id) {
        const result = await ajaxRequest({ action: 'read', module: 'dossiers', id: id });
        if (result.success && result.data) {
            const d = result.data;
            document.getElementById('dossier-id').value = d.id;
            document.getElementById('dossier-voyage').value = d.voyage_id;
            document.getElementById('dossier-eleve').value = d.eleve_id;
            document.getElementById('dossier-date').value = d.date_inscription;
            document.getElementById('dossier-autorisation').value = d.autorisation_parentale;
            document.getElementById('dossier-paiement').value = d.paiement_effectue;
            document.getElementById('dossier-montant').value = d.montant_paye || 0;
            document.getElementById('dossier-assurance').value = d.assurance_voyage;
            document.getElementById('dossier-passeport').value = d.numero_passeport || '';
            document.getElementById('dossier-exp-passeport').value = d.date_expiration_passeport || '';
            document.getElementById('dossier-documents').value = d.documents_fournis || '';
            document.getElementById('dossier-allergies').value = d.allergies_medicales || '';
            document.getElementById('dossier-contact').value = d.contact_urgence || '';
            document.getElementById('dossier-tel-urgence').value = d.telephone_urgence || '';
            document.getElementById('dossier-statut').value = d.statut_dossier;
            document.getElementById('dossier-observations').value = d.observations || '';
            
            document.getElementById('modal-dossier-title').textContent = 'Modifier le Dossier';
            document.getElementById('modal-dossier').classList.add('active');
        }
    }

    async function viewDossier(id) {
        const result = await ajaxRequest({ action: 'read', module: 'dossiers', id: id });
        if (result.success && result.data) {
            const d = result.data;
            document.getElementById('modal-detail-title').textContent = 'Détails du Dossier de Voyage';
            document.getElementById('modal-detail-content').innerHTML = `
                <div class="detail-card">
                    <div class="detail-row"><span class="detail-label">Date d'inscription</span><span class="detail-value">${new Date(d.date_inscription).formatDateFR('fr-FR')}</span></div>
                    <div class="detail-row"><span class="detail-label">Autorisation parentale</span><span class="detail-value"><span class="badge badge-${d.autorisation_parentale === 'oui' ? 'success' : 'warning'}">${d.autorisation_parentale}</span></span></div>
                    <div class="detail-row"><span class="detail-label">Paiement</span><span class="detail-value"><span class="badge badge-${d.paiement_effectue === 'oui' ? 'success' : 'warning'}">${d.paiement_effectue}</span> - ${d.montant_paye || 0} €</span></div>
                    <div class="detail-row"><span class="detail-label">Assurance voyage</span><span class="detail-value">${d.assurance_voyage === 'oui' ? 'Oui' : 'Non'}</span></div>
                    <div class="detail-row"><span class="detail-label">N° Passeport</span><span class="detail-value">${d.numero_passeport || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Expiration passeport</span><span class="detail-value">${d.date_expiration_passeport ? new Date(d.date_expiration_passeport).formatDateFR('fr-FR') : '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Documents fournis</span><span class="detail-value">${d.documents_fournis || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Allergies/Médical</span><span class="detail-value">${d.allergies_medicales || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Contact urgence</span><span class="detail-value">${d.contact_urgence || '-'} ${d.telephone_urgence ? '(' + d.telephone_urgence + ')' : ''}</span></div>
                    <div class="detail-row"><span class="detail-label">Statut dossier</span><span class="detail-value"><span class="badge badge-info">${d.statut_dossier}</span></span></div>
                    <div class="detail-row"><span class="detail-label">Observations</span><span class="detail-value">${d.observations || '-'}</span></div>
                </div>
            `;
            document.getElementById('modal-detail').classList.add('active');
        }
    }

    async function deleteDossier(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?')) {
            const result = await ajaxRequest({ action: 'delete', module: 'dossiers', id: id });
            if (result.success) {
                showAlert(result.message);
                loadDossiers();
            } else {
                showAlert(result.message, 'error');
            }
        }
    }

    // =====================================================
    // INITIALISATION
    // =====================================================
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();
        
        // Fermer les modals en cliquant à l'extérieur
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });
    });
    </script>
</body>
</html>