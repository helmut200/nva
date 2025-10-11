<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants</title>
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
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
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
        
        .email {
            word-break: break-all;
        }
        
        .projet-preview {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Styles spécifiques pour les colonnes */
        .col-id {
            width: 60px;
        }
        
        .col-nom {
            width: 120px;
        }
        
        .col-prenom {
            width: 120px;
        }
        
        .col-email {
            width: 200px;
        }
        
        .col-telephone {
            width: 120px;
        }
        
        .col-niveau {
            width: 120px;
        }
        
        .col-domaine {
            width: 150px;
        }
        
        .col-projet {
            width: 200px;
        }
        
        .col-date {
            width: 140px;
        }
        
        .col-actions {
            width: 200px;
        }
        
        /* Modal pour voir le projet complet */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .btn {
                padding: 6px 10px;
                font-size: 12px;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestion des Étudiants</h1>
            <p>Application CRUD pour la gestion des étudiants</p>
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
        $id = $nom = $prenom = $email = $telephone = $niveau_etude = $domaine_etude = $projet_france = "";
        $isEditing = false;
        $message = "";

        // Traitement du formulaire
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['create']) || isset($_POST['update'])) {
                // Récupération des données du formulaire
                $nom = trim($_POST['nom']);
                $prenom = trim($_POST['prenom']);
                $email = trim($_POST['email']);
                $telephone = trim($_POST['telephone']);
                $niveau_etude = trim($_POST['niveau_etude']);
                $domaine_etude = trim($_POST['domaine_etude']);
                $projet_france = trim($_POST['projet_france']);
                
                // Validation des données
                $errors = [];
                
                if (empty($nom)) {
                    $errors[] = "Le nom est requis.";
                }
                
                if (empty($prenom)) {
                    $errors[] = "Le prénom est requis.";
                }
                
                if (empty($email)) {
                    $errors[] = "L'email est requis.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "L'email n'est pas valide.";
                }
                
                if (!empty($telephone) && !preg_match('/^[0-9+\-\s()]{10,20}$/', $telephone)) {
                    $errors[] = "Le format du téléphone n'est pas valide.";
                }
                
                // Si pas d'erreurs, on procède à l'insertion ou mise à jour
                if (empty($errors)) {
                    try {
                        if (isset($_POST['create'])) {
                            // Vérifier si l'email existe déjà
                            $check_sql = "SELECT id FROM etudiants WHERE email = :email";
                            $check_stmt = $conn->prepare($check_sql);
                            $check_stmt->bindParam(':email', $email);
                            $check_stmt->execute();
                            
                            if ($check_stmt->rowCount() > 0) {
                                $message = "<div class='message error'>Un étudiant avec cet email existe déjà.</div>";
                            } else {
                                // Insertion d'un nouvel étudiant
                                $sql = "INSERT INTO etudiants (nom, prenom, email, telephone, niveau_etude, domaine_etude, projet_france, date_inscription) 
                                        VALUES (:nom, :prenom, :email, :telephone, :niveau_etude, :domaine_etude, :projet_france, NOW())";
                                
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':nom', $nom);
                                $stmt->bindParam(':prenom', $prenom);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':telephone', $telephone);
                                $stmt->bindParam(':niveau_etude', $niveau_etude);
                                $stmt->bindParam(':domaine_etude', $domaine_etude);
                                $stmt->bindParam(':projet_france', $projet_france);
                                
                                if ($stmt->execute()) {
                                    $message = "<div class='message success'>Étudiant créé avec succès!</div>";
                                    // Réinitialiser le formulaire
                                    $nom = $prenom = $email = $telephone = $niveau_etude = $domaine_etude = $projet_france = "";
                                }
                            }
                        } elseif (isset($_POST['update'])) {
                            // Mise à jour d'un étudiant existant
                            $id = $_POST['id'];
                            $old_email = $_POST['old_email'];
                            
                            // Vérifier si l'email a changé et s'il existe déjà
                            if ($email !== $old_email) {
                                $check_sql = "SELECT id FROM etudiants WHERE email = :email AND id != :id";
                                $check_stmt = $conn->prepare($check_sql);
                                $check_stmt->bindParam(':email', $email);
                                $check_stmt->bindParam(':id', $id);
                                $check_stmt->execute();
                                
                                if ($check_stmt->rowCount() > 0) {
                                    $message = "<div class='message error'>Un autre étudiant avec cet email existe déjà.</div>";
                                } else {
                                    updateStudent();
                                }
                            } else {
                                updateStudent();
                            }
                            
                            function updateStudent() {
                                global $conn, $id, $nom, $prenom, $email, $telephone, $niveau_etude, $domaine_etude, $projet_france, $message, $isEditing;
                                
                                $sql = "UPDATE etudiants 
                                        SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, 
                                            niveau_etude = :niveau_etude, domaine_etude = :domaine_etude, projet_france = :projet_france 
                                        WHERE id = :id";
                                
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id', $id);
                                $stmt->bindParam(':nom', $nom);
                                $stmt->bindParam(':prenom', $prenom);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':telephone', $telephone);
                                $stmt->bindParam(':niveau_etude', $niveau_etude);
                                $stmt->bindParam(':domaine_etude', $domaine_etude);
                                $stmt->bindParam(':projet_france', $projet_france);
                                
                                if ($stmt->execute()) {
                                    $message = "<div class='message success'>Étudiant mis à jour avec succès!</div>";
                                    $isEditing = false;
                                    // Réinitialiser le formulaire
                                    $id = $nom = $prenom = $email = $telephone = $niveau_etude = $domaine_etude = $projet_france = "";
                                }
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

        // Suppression d'un étudiant
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            
            try {
                $sql = "DELETE FROM etudiants WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                
                if ($stmt->execute()) {
                    $message = "<div class='message success'>Étudiant supprimé avec succès!</div>";
                }
            } catch(PDOException $e) {
                $message = "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
            }
        }

        // Édition d'un étudiant
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            
            try {
                $sql = "SELECT * FROM etudiants WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                
                if ($stmt->rowCount() == 1) {
                    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id = $etudiant['id'];
                    $nom = $etudiant['nom'];
                    $prenom = $etudiant['prenom'];
                    $email = $etudiant['email'];
                    $telephone = $etudiant['telephone'];
                    $niveau_etude = $etudiant['niveau_etude'];
                    $domaine_etude = $etudiant['domaine_etude'];
                    $projet_france = $etudiant['projet_france'];
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
            <h2><?php echo $isEditing ? 'Modifier l\'étudiant' : 'Ajouter un nouvel étudiant'; ?></h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="old_email" value="<?php echo $email; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *:</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom">Prénom *:</label>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone:</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>" placeholder="+225 XX XX XX XX">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="niveau_etude">Niveau d'étude:</label>
                        <select id="niveau_etude" name="niveau_etude">
                            <option value="">Sélectionnez un niveau</option>
                            <option value="Bac" <?php echo ($niveau_etude == 'Bac') ? 'selected' : ''; ?>>Bac</option>
                            <option value="Bac+1" <?php echo ($niveau_etude == 'Bac+1') ? 'selected' : ''; ?>>Bac+1</option>
                            <option value="Bac+2" <?php echo ($niveau_etude == 'Bac+2') ? 'selected' : ''; ?>>Bac+2</option>
                            <option value="Bac+3" <?php echo ($niveau_etude == 'Bac+3') ? 'selected' : ''; ?>>Bac+3 (Licence)</option>
                            <option value="Bac+4" <?php echo ($niveau_etude == 'Bac+4') ? 'selected' : ''; ?>>Bac+4 (Master 1)</option>
                            <option value="Bac+5" <?php echo ($niveau_etude == 'Bac+5') ? 'selected' : ''; ?>>Bac+5 (Master 2)</option>
                            <option value="Doctorat" <?php echo ($niveau_etude == 'Doctorat') ? 'selected' : ''; ?>>Doctorat</option>
                            <option value="Autre" <?php echo ($niveau_etude == 'Autre') ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="domaine_etude">Domaine d'étude:</label>
                        <input type="text" id="domaine_etude" name="domaine_etude" value="<?php echo htmlspecialchars($domaine_etude); ?>" placeholder="Informatique, Médecine, etc.">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="projet_france">Projet pour la France:</label>
                    <textarea id="projet_france" name="projet_france" placeholder="Décrivez le projet de l'étudiant pour la France..."><?php echo htmlspecialchars($projet_france); ?></textarea>
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

        <!-- Liste des étudiants -->
        <div class="form-container">
            <h2>Liste des étudiants (<?php
                try {
                    $count_sql = "SELECT COUNT(*) as total FROM etudiants";
                    $count_stmt = $conn->query($count_sql);
                    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    echo $total;
                } catch(PDOException $e) {
                    echo "0";
                }
            ?>)</h2>
            <div class="table-container">
                <?php
                try {
                    $sql = "SELECT * FROM etudiants ORDER BY date_inscription DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        echo "<table>";
                        echo "<tr>
                                <th class='col-id'>ID</th>
                                <th class='col-nom'>Nom</th>
                                <th class='col-prenom'>Prénom</th>
                                <th class='col-email'>Email</th>
                                <th class='col-telephone'>Téléphone</th>
                                <th class='col-niveau'>Niveau</th>
                                <th class='col-domaine'>Domaine</th>
                                <th class='col-projet'>Projet France</th>
                                <th class='col-date'>Date inscription</th>
                                <th class='col-actions'>Actions</th>
                              </tr>";
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                            echo "<td class='email'>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . ($row['telephone'] ? htmlspecialchars($row['telephone']) : '-') . "</td>";
                            echo "<td>" . ($row['niveau_etude'] ? htmlspecialchars($row['niveau_etude']) : '-') . "</td>";
                            echo "<td>" . ($row['domaine_etude'] ? htmlspecialchars($row['domaine_etude']) : '-') . "</td>";
                            echo "<td class='projet-preview' title='" . htmlspecialchars($row['projet_france']) . "'>";
                            echo $row['projet_france'] ? htmlspecialchars(substr($row['projet_france'], 0, 50)) . '...' : '-';
                            echo "</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($row['date_inscription'])) . "</td>";
                            echo "<td class='actions'>";
                            echo "<a href='?edit=" . $row['id'] . "' class='btn btn-warning'>Modifier</a>";
                            echo "<a href='#' class='btn btn-secondary' onclick='voirProjet(" . $row['id'] . ", \"" . addslashes(htmlspecialchars($row['nom'])) . "\", \"" . addslashes(htmlspecialchars($row['prenom'])) . "\", \"" . addslashes(htmlspecialchars($row['projet_france'])) . "\")'>Voir projet</a>";
                            echo "<a href='?delete=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet étudiant?\")'>Supprimer</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    } else {
                        echo "<p>Aucun étudiant trouvé.</p>";
                    }
                } catch(PDOException $e) {
                    echo "<div class='message error'>Erreur: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Modal pour voir le projet complet -->
    <div id="projetModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalTitle">Projet pour la France</h3>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Script pour confirmer la suppression
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cet étudiant?");
        }
        
        // Gestion du modal pour voir le projet
        const modal = document.getElementById('projetModal');
        const span = document.getElementsByClassName('close')[0];
        
        function voirProjet(id, nom, prenom, projet) {
            document.getElementById('modalTitle').textContent = 'Projet pour la France - ' + prenom + ' ' + nom;
            document.getElementById('modalContent').textContent = projet || 'Aucun projet spécifié.';
            modal.style.display = 'block';
        }
        
        span.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Formatage automatique du téléphone
        document.getElementById('telephone')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '+225 ' + value;
            }
            e.target.value = value;
        });
    </script>
</body>
</html>