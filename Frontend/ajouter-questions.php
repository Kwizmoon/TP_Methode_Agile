<?php
    require_once '../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter les questions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<?php require_once 'header.php'; ?>

<main class="main-builder">
    <form action="" method="post">
        <h2>Ajouter une question</h2>
        <div>
            <label for="question">Question : </label>
            <input type="text" name="question" id="question" required>
            <p class="erreur" id="erreur-question"></p>
        </div>

        <div class="reponses-section">
            <h3>Réponses (Maximum : 5)</h3>
            <div id="liste-reponses">

            </div>
            <button type="button" id="btn-ajouter-reponse" class="btn-secondaire">+ Ajouter une réponse</button>
            <p class="erreur" id="erreur-reponses"></p>
        </div>

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" id="btn-valider">Valider la question</button>
        </div>
        <p class="erreur" id="erreur-global"></p>
    </form>
</main>

</body>
</html>