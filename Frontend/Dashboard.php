<?php

require_once '../middleware/auth.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Enseignant</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php require_once 'header.php'; ?>

<div class="dashboard">

    <div class="welcome-card">
        <h1>Bienvenue Enseignant</h1>
        <p>Gérez vos quiz et consultez les résultats de vos étudiants.</p>
    </div>

    <div class="dashboard-grid">

        <div class="card">
            <h2>Créer un Quiz</h2>
            <p>Commencer une nouvelle évaluation.</p>
            <button onclick="window.location.href='creer_quiz.php'">Accéder</button>
        </div>

        <div class="card">
            <h2>Mes Quiz</h2>
            <p>Modifier ou consulter vos quiz.</p>
            <button>Accéder</button>
        </div>

        <div class="card">
            <h2>Résultats</h2>
            <p>Suivre les performances des étudiants.</p>
            <button>Accéder</button>
        </div>

    </div>

    <div class="logout-section">
        <button class="logout-btn" onclick="deconnexion()">
            Déconnexion
        </button>
    </div>

</div>

</body>
</html>
<script>

    async function deconnexion(){

        try {
            const response = await fetch('../api/deconnexion.php', {
                method : 'POST'
            });

            const result = await response.json();
            

            if(result.success){
                window.location.href = result.redirect;
            }
            
        } catch (error) {
            console.error('Erreur réseau:', error);
        }
    }


</script>