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
            <button onclick="document.getElementById('mes-quiz').scrollIntoView({ behavior: 'smooth' })">Accéder</button>
        </div>

        <div class="card">
            <h2>Résultats</h2>
            <p>Suivre les performances des étudiants.</p>
            <button>Accéder</button>
        </div>

    </div>

    <section id="mes-quiz" style="margin-top: 2rem;">
        <h2 style="margin-bottom: 15px;">Mes quiz</h2>
        <div class="dashboard-grid" id="liste-quiz"></div>
    </section>

    <div class="logout-section">
        <button class="logout-btn" onclick="deconnexion()">
            Déconnexion
        </button>
    </div>

</div>

</body>
</html>
<script>

    async function chargerQuiz(){
        const liste = document.getElementById('liste-quiz');

        try {
            const response = await fetch('../api/get_quiz.php');
            const result = await response.json();

            if(!result.success){
                liste.innerHTML = '<div class="card"><p>Impossible de charger vos quiz.</p></div>';
                return;
            }

            if(result.quiz.length === 0){
                liste.innerHTML = '<div class="card"><p>Vous n\'avez pas encore créé de quiz.</p></div>';
                return;
            }

            result.quiz.forEach(function(q){
                const carte = document.createElement('div');
                carte.className = 'card';

                const titre = document.createElement('h2');
                titre.textContent = q.titre;

                const description = document.createElement('p');
                description.textContent = q.description + ' (' + q.nb_questions + (q.nb_questions == 1 ? ' question)' : ' questions)');

                const bouton = document.createElement('button');
                bouton.textContent = 'Voir les détails';

                // Un clic sur la carte (ou sur le bouton) ouvre la page de details du quiz
                carte.style.cursor = 'pointer';
                carte.onclick = function(){
                    window.location.href = '../quiz_details.php?quiz_id=' + q.id;
                };

                carte.append(titre, description, bouton);
                liste.appendChild(carte);
            });

        } catch (error) {
            console.error('Erreur réseau:', error);
            liste.innerHTML = '<div class="card"><p>Erreur de connexion au serveur.</p></div>';
        }
    }

    chargerQuiz();

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