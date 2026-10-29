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
        <form action="" method="post" id="creationQuizForm" novalidate>
            <h2>Créer un nouveau quiz</h2>
            <div>
                <label for="titre">Titre du quiz : </label>
                <input type="text" name="titre" id="titre" required>
                <p class="erreur" id="erreur-titre"></p>
            </div>

            <div>
                <label for="description">Description :</label>
                <textarea name="description" id="description" class="input-description"></textarea>
                <p class="erreur" id="erreur-description"></p>
            </div>

            <button type="submit">Suivant</button>
            
            <p class="erreur" id="erreur-bd"></p>
        </form>
    </main>

    <script>
        document.getElementById('creationQuizForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            // vide les erreurs et les style precedents
            document.getElementById('titre').classList.remove('input-erreur');
            document.getElementById('description').classList.remove('input-erreur');
            document.getElementById('erreur-titre').textContent = '';
            document.getElementById('erreur-description').textContent = '';
            document.getElementById('erreur-bd').textContent = '';


            const data = {
                titre: document.getElementById('titre').value.trim(),
                description: document.getElementById('description').value.trim()
            };

            try{
                const response = await fetch('../api/save_quiz.php', {
                    method : 'POST',
                    headers: {'Content-Type' : 'application/json'},
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if(result.success){
                // rediriction a la page url specifier dans save_quiz.php
                window.location.href = result.redirect;
                } else if (result.erreurs){
                    
                    if (result.erreurs.titre) {
                        document.getElementById('erreur-titre').textContent = result.erreurs.titre;
                        document.getElementById('titre').classList.add('input-erreur');
                    }

                    if (result.erreurs.description) {
                        document.getElementById('erreur-description').textContent = result.erreurs.description;
                        document.getElementById('description').classList.add('input-erreur');
                    }

                    if (result.erreurs.bd) {
                    document.getElementById('erreur-bd').textContent = result.erreurs.bd;
                }
                }
            } catch (error){
                console.error(error);
                
            }
        });

    </script>
    
</body>
</html>
