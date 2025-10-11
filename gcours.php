<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Matières</title>
    <style>
        /* Styles CSS */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin: 5px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: #27ae60;
        }
        
        .btn-success:hover {
            background-color: #219653;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-warning {
            background-color: #f39c12;
        }
        
        .btn-warning:hover {
            background-color: #d35400;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input {
            width: auto;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            position: sticky;
            top: 0;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .actions .btn {
            padding: 6px 12px;
            font-size: 14px;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-available {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-unavailable {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .currency {
            text-align: right;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 14px;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestion des Matières</h1>
            <p>Application CRUD pour la gestion du catalogue des matières</p>
        </header>

        <?php
        // Configuration de la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "nva+";

        // Connexion à la base de données
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "<div class='message error'>Erreur de connexion: " . $e->getMessage() . "</div>";
        }

        // Variables pour le formulaire
        $id = $matiere = $niveau = $professeur = $prix_heure = $disponible = "";
        $isEditing = false;
        $message = "";

        // Traitement du formulaire
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['create']) || isset($_POST['update'])) {
                // Récupération des données du formulaire
                $matiere = trim($_POST['matiere']);
                $niveau = trim($_POST['niveau']);
                $professeur = trim($_POST['professeur']);
                $prix_heure = $_POST['prix_heure'];
                $disponible = isset($_POST['disponible']) ? 1 : 0;
                
                // Validation des données
                $errors = [];
                
                if (empty($matiere)) {
                    $errors[] = "Le nom de la matière est requis.";
                }
                
                if (empty($niveau)) {
                    $errors[] = "Le niveau est requis.";
                }
                
                if (!empty($prix_heure) && !is_numeric($prix_heure)) {
                    $errors[] = "Le prix par heure doit être un nombre valide.";
                }
                
                // Si pas d'erreurs, on procède à l'insertion ou mise à jour
                if (empty($errors)) {
                    try {
                        if (isset($_POST['create'])) {
                            // Insertion d'une nouvelle matière
                            $sql = "INSERT INTO matieres (matiere, niveau, professeur, prix_heure, disponible) 
                                    VALUES (:matiere, :niveau, :professeur, :prix_heure, :disponible)";
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':matiere', $matiere);
                            $stmt->bindParam(':niveau', $niveau);
                            $stmt->bindParam(':professeur', $professeur);
                            $stmt->bindParam(':prix_heure', $prix_heure);
                            $stmt->bindParam(':disponible', $disponible);
                            
                            if ($stmt->execute()) {
                                $message = "<div class='message success'>Matière créée avec succès!</div>";
                                // Réinitialiser le formulaire
                                $matiere = $niveau = $professeur = $prix_heure = "";
                                $disponible = 1;
                            }
                        } elseif (isset($_POST['update'])) {
                            // Mise à jour d'une matière existante
                            $id = $_POST['id'];
                            
                            $sql = "UPDATE cours 
                                    SET matiere = :matiere, niveau = :niveau, professeur = :professeur, 
                                        prix_heure = :prix_heure, disponible = :disponible 
                                    WHERE id = :id";
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':id', $id);
                            $stmt->bindParam(':matiere', $matiere);
                            $stmt->bindParam(':niveau', $niveau);
                            $stmt->bindParam(':professeur', $professeur);
                            $stmt->bindParam(':prix_heure', $prix_heure);
                            $stmt->bindParam(':disponible', $disponible);
                            
                            if ($stmt->execute()) {
                                $message = "<div class='message success'>Matière mise à jour avec succès!</div>";
                                $isEditing = false;
                                // Réinitialiser le formulaire
                                $id = $matiere = $niveau = $professeur = $prix_heure = "";
                                $disponible = 1;
                            }
                        }
                    } catch(PDOException $e) {
                        $message = "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
                    }
                } else {
                    $message = "<div class='message error'>" . implode("<br>", $errors) . "</div>";
                }
            }
        }

        // Suppression d'une matière
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            
            try {
                $sql = "DELETE FROM cours WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                
                if ($stmt->execute()) {
                    $message = "<div class='message success'>Matière supprimée avec succès!</div>";
                }
            } catch(PDOException $e) {
                $message = "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
            }
        }

        // Édition d'une matière
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            
            try {
                $sql = "SELECT * FROM cours WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                
                if ($stmt->rowCount() == 1) {
                    $matiere_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id = $matiere_data['id'];
                    $matiere = $matiere_data['matiere'];
                    $niveau = $matiere_data['niveau'];
                    $professeur = $matiere_data['professeur'];
                    $prix_heure = $matiere_data['prix_heure'];
                    $disponible = $matiere_data['disponible'];
                    $isEditing = true;
                }
            } catch(PDOException $e) {
                $message = "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
            }
        }

        // Toggle disponibilité
        if (isset($_GET['toggle'])) {
            $id = $_GET['toggle'];
            
            try {
                // Récupérer l'état actuel
                $sql = "SELECT disponible FROM cours WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                
                if ($stmt->rowCount() == 1) {
                    $current = $stmt->fetch(PDO::FETCH_ASSOC);
                    $new_status = $current['disponible'] ? 0 : 1;
                    
                    $sql = "UPDATE cours SET disponible = :disponible WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':disponible', $new_status);
                    $stmt->bindParam(':id', $id);
                    
                    if ($stmt->execute()) {
                        $message = "<div class='message success'>Statut de disponibilité mis à jour!</div>";
                    }
                }
            } catch(PDOException $e) {
                $message = "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
            }
        }
        ?>

        <!-- Affichage des messages -->
        <?php echo $message; ?>

        <!-- Formulaire de création/édition -->
        <div class="form-container">
            <h2><?php echo $isEditing ? 'Modifier la matière' : 'Ajouter une nouvelle matière'; ?></h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="matiere">Matière *:</label>
                    <input type="text" id="matiere" name="matiere" value="<?php echo htmlspecialchars($matiere); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="niveau">Niveau *:</label>
                    <select id="niveau" name="niveau" required>
                        <option value="">Sélectionnez un niveau</option>
                        <option value="Primaire" <?php echo ($niveau == 'Primaire') ? 'selected' : ''; ?>>Primaire</option>
                        <option value="Collège" <?php echo ($niveau == 'Collège') ? 'selected' : ''; ?>>Collège</option>
                        <option value="Lycée" <?php echo ($niveau == 'Lycée') ? 'selected' : ''; ?>>Lycée</option>
                        <option value="Université" <?php echo ($niveau == 'Université') ? 'selected' : ''; ?>>Université</option>
                        <option value="Autre" <?php echo ($niveau == 'Autre') ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="professeur">Professeur:</label>
                    <input type="text" id="professeur" name="professeur" value="<?php echo htmlspecialchars($professeur); ?>">
                </div>
                
                <div class="form-group">
                    <label for="prix_heure">Prix par heure (FCFA):</label>
                    <input type="number" step="0.01" id="prix_heure" name="prix_heure" value="<?php echo $prix_heure; ?>" placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="disponible" name="disponible" value="1" <?php echo ($disponible == 1) ? 'checked' : ''; ?>>
                        <label for="disponible">Matière disponible</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <?php if ($isEditing): ?>
                        <button type="submit" name="update" class="btn btn-success">Mettre à jour</button>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Annuler</a>
                    <?php else: ?>
                        <button type="submit" name="create" class="btn">Créer</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Liste des matières -->
        <div class="form-container">
            <h2>Liste des matières</h2>
            <div class="table-container">
                <?php
                try {
                    $sql = "SELECT * FROM cours ORDER BY niveau, matiere";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        echo "<table>";
                        echo "<tr>
                                <th>ID</th>
                                <th>Matière</th>
                                <th>Niveau</th>
                                <th>Professeur</th>
                                <th>Prix/heure</th>
                                <th>Disponible</th>
                                <th>Actions</th>
                              </tr>";
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Formater le prix
                            $prix_formate = $row['prix_heure'] ? number_format($row['prix_heure'], 2, ',', ' ') . ' FCFA' : 'Non défini';
                            
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['matiere']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['niveau']) . "</td>";
                            echo "<td>" . ($row['professeur'] ? htmlspecialchars($row['professeur']) : 'Non assigné') . "</td>";
                            echo "<td class='currency'>" . $prix_formate . "</td>";
                            echo "<td class='" . ($row['disponible'] ? 'status-available' : 'status-unavailable') . "'>";
                            echo $row['disponible'] ? '✅ Disponible' : '❌ Indisponible';
                            echo "</td>";
                            echo "<td class='actions'>";
                            echo "<a href='?edit=" . $row['id'] . "' class='btn btn-warning'>Modifier</a>";
                            echo "<a href='?toggle=" . $row['id'] . "' class='btn btn-secondary'>" . ($row['disponible'] ? 'Désactiver' : 'Activer') . "</a>";
                            echo "<a href='?delete=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette matière?\")'>Supprimer</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    } else {
                        echo "<p>Aucune matière trouvée.</p>";
                    }
                } catch(PDOException $e) {
                    echo "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Script pour confirmer la suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette matière?");
        }
        
        // Formatage automatique du prix lors de la saisie
        document.getElementById('prix_heure')?.addEventListener('blur', function(e) {
            let value = parseFloat(e.target.value);
            if (!isNaN(value) && value > 0) {
                e.target.value = value.toFixed(2);
            }
        });
    </script>
</body>
</html>