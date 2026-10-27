<?php
    require_once '../middleware/auth.php';
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php require_once 'header.php'; ?>

    <main>
        <form action="" method="post" id="creationQuizForm">
            <h2>Créer un nouveau quiz</h2>
            <div>
                <label for="titre">Titre du quiz : </label>
                <input type="text" name="titre" id="titre" required>
                <p class="erreur" id="erreur-titre"></p>
            </div>

            <div>
                <label for="description">Description :</label>
                <textarea name="description" id="description" class="input-description"></textarea>
            </div>

            <button onclick="window.location.href='ajouter-questions.php'" type="submit" >Suivant</button>
            
            <p class="erreur" id="erreur-bd"></p>
        </form>
    </main>

    <script>
        document.getElementById('creationQuizForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('decription').value.trim();
            const erreurTitre = document.getElementById('erreur-titre');
            const erreurDescription = document.getElementById('erreur-description');
            const erreurBd = document.getElementById('erreur-bd');

            erreurTitre.textContent = '';
            erreurDescription = '';
            erreurBd.textContent = '';

            if(!titre){
                erreurTitre.textContent = 'Le titre est obligatoire.';
                return;
            }
            if(!description){
                erreurDescription.textContent = 'La description est obligatoire.';
                return;
            }

            try{
                const response = await fetch('api/save_quiz.php', {
                    method : 'POST',
                    headers: {'Content-Type' : 'application/json'},
                    body: JSON.stringify({titre, description})
                });

                const data = await response.json();

                if(response.ok && data.success){
                    window.location.href = `ajouter-questions.php?quiz_id=${data.quiz_id}`;
                } else {
                    erreurBd.textContent = data.message || 'Une erreur est survenue lors de l\'enregistrement.';
                }
            } catch (error){
                console.error(error);
                erreurBd.textContent = 'Erreur de connexion au serveur.';
            }
        });

    </script>
    
</body>
</html>
