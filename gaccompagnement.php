<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Dossiers d'Accompagnement</title>
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
            max-width: 1400px;
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
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin: 2px;
            transition: background-color 0.3s;
            white-space: nowrap;
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
            min-width: 1000px;
        }
        
        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
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
            flex-wrap: nowrap;
        }
        
        .actions .btn {
            flex-shrink: 0;
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
        
        .hidden {
            display: none;
        }
        
        .currency-note {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        /* Styles spécifiques pour les colonnes */
        .col-etudiant {
            width: 100px;
        }
        
        .col-type {
            width: 150px;
        }
        
        .col-universite {
            width: 120px;
        }
        
        .col-programme {
            width: 140px;
        }
        
        .col-budget {
            width: 130px;
        }
        
        .col-date {
            width: 120px;
        }
        
        .col-statut {
            width: 100px;
        }
        
        .col-creation {
            width: 140px;
        }
        
        .col-actions {
            width: 180px;
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
                padding: 6px 10px;
                font-size: 12px;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestion des Dossiers d'Accompagnement</h1>
            <p>Application CRUD pour la gestion des dossiers étudiants</p>
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
        $etudiant_id = $type_accompagnement = $universite_cible = $programme_souhaite = $budget_estime = $date_depart_souhaite = $statut_dossier = "";
        $isEditing = false;
        $message = "";

        // Traitement du formulaire
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['create']) || isset($_POST['update'])) {
                // Récupération des données du formulaire
                $etudiant_id = $_POST['etudiant_id'];
                $type_accompagnement = $_POST['type_accompagnement'];
                $universite_cible = $_POST['universite_cible'];
                $programme_souhaite = $_POST['programme_souhaite'];
                $budget_estime = $_POST['budget_estime'];
                $date_depart_souhaite = $_POST['date_depart_souhaite'];
                $statut_dossier = $_POST['statut_dossier'];
                
                // Validation des données
                $errors = [];
                
                if (empty($etudiant_id)) {
                    $errors[] = "L'ID étudiant est requis.";
                }
                
                if (empty($type_accompagnement)) {
                    $errors[] = "Le type d'accompagnement est requis.";
                }
                
                if (empty($statut_dossier)) {
                    $errors[] = "Le statut du dossier est requis.";
                }
                
                // Si pas d'erreurs, on procède à l'insertion ou mise à jour
                if (empty($errors)) {
                    try {
                        if (isset($_POST['create'])) {
                            // Vérifier si l'étudiant existe déjà
                            $check_sql = "SELECT * FROM accompagnement_france WHERE etudiant_id = :etudiant_id";
                            $check_stmt = $conn->prepare($check_sql);
                            $check_stmt->bindParam(':etudiant_id', $etudiant_id);
                            $check_stmt->execute();
                            
                            if ($check_stmt->rowCount() > 0) {
                                $message = "<div class='message error'>Un dossier existe déjà pour cet étudiant (ID: $etudiant_id).</div>";
                            } else {
                                // Insertion d'un nouveau dossier
                                $sql = "INSERT INTO accompagnement_france (etudiant_id, type_accompagnement, universite_cible, programme_souhaite, budget_estime, date_depart_souhaite, statut_dossier, date_creation) 
                                        VALUES (:etudiant_id, :type_accompagnement, :universite_cible, :programme_souhaite, :budget_estime, :date_depart_souhaite, :statut_dossier, NOW())";
                                
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':etudiant_id', $etudiant_id);
                                $stmt->bindParam(':type_accompagnement', $type_accompagnement);
                                $stmt->bindParam(':universite_cible', $universite_cible);
                                $stmt->bindParam(':programme_souhaite', $programme_souhaite);
                                $stmt->bindParam(':budget_estime', $budget_estime);
                                $stmt->bindParam(':date_depart_souhaite', $date_depart_souhaite);
                                $stmt->bindParam(':statut_dossier', $statut_dossier);
                                
                                if ($stmt->execute()) {
                                    $message = "<div class='message success'>Dossier créé avec succès!</div>";
                                    // Réinitialiser le formulaire
                                    $etudiant_id = $type_accompagnement = $universite_cible = $programme_souhaite = $budget_estime = $date_depart_souhaite = $statut_dossier = "";
                                }
                            }
                        } elseif (isset($_POST['update'])) {
                            // Mise à jour d'un dossier existant
                            $old_etudiant_id = $_POST['old_etudiant_id'];
                            
                            $sql = "UPDATE accompagnement_france 
                                    SET etudiant_id = :etudiant_id, type_accompagnement = :type_accompagnement, universite_cible = :universite_cible, 
                                        programme_souhaite = :programme_souhaite, budget_estime = :budget_estime, 
                                        date_depart_souhaite = :date_depart_souhaite, statut_dossier = :statut_dossier 
                                    WHERE etudiant_id = :old_etudiant_id";
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':old_etudiant_id', $old_etudiant_id);
                            $stmt->bindParam(':etudiant_id', $etudiant_id);
                            $stmt->bindParam(':type_accompagnement', $type_accompagnement);
                            $stmt->bindParam(':universite_cible', $universite_cible);
                            $stmt->bindParam(':programme_souhaite', $programme_souhaite);
                            $stmt->bindParam(':budget_estime', $budget_estime);
                            $stmt->bindParam(':date_depart_souhaite', $date_depart_souhaite);
                            $stmt->bindParam(':statut_dossier', $statut_dossier);
                            
                            if ($stmt->execute()) {
                                $message = "<div class='message success'>Dossier mis à jour avec succès!</div>";
                                $isEditing = false;
                                // Réinitialiser le formulaire
                                $etudiant_id = $type_accompagnement = $universite_cible = $programme_souhaite = $budget_estime = $date_depart_souhaite = $statut_dossier = "";
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

        // Suppression d'un dossier
        if (isset($_GET['delete'])) {
            $etudiant_id = $_GET['delete'];
            
            try {
                $sql = "DELETE FROM accompagnement_france WHERE etudiant_id = :etudiant_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':etudiant_id', $etudiant_id);
                
                if ($stmt->execute()) {
                    $message = "<div class='message success'>Dossier supprimé avec succès!</div>";
                }
            } catch(PDOException $e) {
                $message = "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
            }
        }

        // Édition d'un dossier
        if (isset($_GET['edit'])) {
            $etudiant_id = $_GET['edit'];
            
            try {
                $sql = "SELECT * FROM accompagnement_france WHERE etudiant_id = :etudiant_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':etudiant_id', $etudiant_id);
                $stmt->execute();
                
                if ($stmt->rowCount() == 1) {
                    $dossier = $stmt->fetch(PDO::FETCH_ASSOC);
                    $etudiant_id = $dossier['etudiant_id'];
                    $type_accompagnement = $dossier['type_accompagnement'];
                    $universite_cible = $dossier['universite_cible'];
                    $programme_souhaite = $dossier['programme_souhaite'];
                    $budget_estime = $dossier['budget_estime'];
                    $date_depart_souhaite = $dossier['date_depart_souhaite'];
                    $statut_dossier = $dossier['statut_dossier'];
                    $isEditing = true;
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
            <h2><?php echo $isEditing ? 'Modifier le dossier' : 'Ajouter un nouveau dossier'; ?></h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="old_etudiant_id" value="<?php echo $etudiant_id; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="etudiant_id">ID Étudiant:</label>
                    <input type="number" id="etudiant_id" name="etudiant_id" value="<?php echo $etudiant_id; ?>" <?php echo $isEditing ? 'readonly' : ''; ?> required>
                    <?php if ($isEditing): ?>
                        <div class="currency-note">L'ID étudiant ne peut pas être modifié</div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="type_accompagnement">Type d'accompagnement:</label>
                    <input type="text" id="type_accompagnement" name="type_accompagnement" value="<?php echo $type_accompagnement; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="universite_cible">Université cible:</label>
                    <input type="text" id="universite_cible" name="universite_cible" value="<?php echo $universite_cible; ?>">
                </div>
                
                <div class="form-group">
                    <label for="programme_souhaite">Programme souhaité:</label>
                    <input type="text" id="programme_souhaite" name="programme_souhaite" value="<?php echo $programme_souhaite; ?>">
                </div>
                
                <div class="form-group">
                    <label for="budget_estime">Budget estimé (FCFA):</label>
                    <input type="number" step="0.01" id="budget_estime" name="budget_estime" value="<?php echo $budget_estime; ?>">
                    <div class="currency-note">Le budget doit être saisi en Francs CFA (FCFA)</div>
                </div>
                
                <div class="form-group">
                    <label for="date_depart_souhaite">Date de départ souhaitée:</label>
                    <input type="date" id="date_depart_souhaite" name="date_depart_souhaite" value="<?php echo $date_depart_souhaite; ?>">
                </div>
                
                <div class="form-group">
                    <label for="statut_dossier">Statut du dossier:</label>
                    <select id="statut_dossier" name="statut_dossier" required>
                        <option value="En attente" <?php echo ($statut_dossier == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                        <option value="En cours" <?php echo ($statut_dossier == 'En cours') ? 'selected' : ''; ?>>En cours</option>
                        <option value="Accepté" <?php echo ($statut_dossier == 'Accepté') ? 'selected' : ''; ?>>Accepté</option>
                        <option value="Refusé" <?php echo ($statut_dossier == 'Refusé') ? 'selected' : ''; ?>>Refusé</option>
                        <option value="Terminé" <?php echo ($statut_dossier == 'Terminé') ? 'selected' : ''; ?>>Terminé</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <?php if ($isEditing): ?>
                        <button type="submit" name="update" class="btn btn-success">Mettre à jour</button>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-warning">Annuler</a>
                    <?php else: ?>
                        <button type="submit" name="create" class="btn">Créer</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Liste des dossiers -->
        <div class="form-container">
            <h2>Liste des dossiers</h2>
            <div class="table-container">
                <?php
                try {
                    $sql = "SELECT * FROM accompagnement_france ORDER BY date_creation DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        echo "<table>";
                        echo "<tr>
                                <th class='col-etudiant'>ID Étudiant</th>
                                <th class='col-type'>Type d'accompagnement</th>
                                <th class='col-universite'>Université cible</th>
                                <th class='col-programme'>Programme souhaité</th>
                                <th class='col-budget'>Budget estimé</th>
                                <th class='col-date'>Date départ souhaitée</th>
                                <th class='col-statut'>Statut</th>
                                <th class='col-creation'>Date création</th>
                                <th class='col-actions'>Actions</th>
                              </tr>";
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Formater le budget avec séparateurs de milliers
                            $budget_formate = number_format($row['budget_estime'], 2, ',', ' ');
                            
                            echo "<tr>";
                            echo "<td>" . $row['etudiant_id'] . "</td>";
                            echo "<td>" . $row['type_accompagnement'] . "</td>";
                            echo "<td>" . $row['universite_cible'] . "</td>";
                            echo "<td>" . $row['programme_souhaite'] . "</td>";
                            echo "<td>" . $budget_formate . " FCFA</td>";
                            echo "<td>" . $row['date_depart_souhaite'] . "</td>";
                            echo "<td>" . $row['statut_dossier'] . "</td>";
                            echo "<td>" . $row['date_creation'] . "</td>";
                            echo "<td class='actions'>";
                            echo "<a href='?edit=" . $row['etudiant_id'] . "' class='btn btn-warning'>Modifier</a>";
                            echo "<a href='?delete=" . $row['etudiant_id'] . "' class='btn btn-danger' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce dossier?\")'>Supprimer</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    } else {
                        echo "<p>Aucun dossier trouvé.</p>";
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
            return confirm("Êtes-vous sûr de vouloir supprimer ce dossier?");
        }
        
        // Formatage automatique du budget lors de la saisie
        document.getElementById('budget_estime')?.addEventListener('blur', function(e) {
            let value = parseFloat(e.target.value);
            if (!isNaN(value)) {
                e.target.value = value.toFixed(2);
            }
        });
    </script>
</body>
</html>