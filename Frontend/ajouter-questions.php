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

<main>
    <form action="" method="post"></form>
    <h2>Ajouter une question</h2>
    <div>
        <label for="question">Question : </label>
        <input type="text" name="question" id="question" required>
        <p class="erreur" id="erreur-question"></p>
    </div>
    
</main>

</body>
</html>