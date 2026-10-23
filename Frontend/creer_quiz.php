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

            <button type="submit">Suivant</button>
            <p class="erreur" id="erreur-bd"></p>
        </form>
    </main>

    <script>
        

    </script>
    
</body>
</html>
