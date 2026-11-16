<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <a href="Dashboard.php" class="logo">KWIZ</a>

        <div class="header-actions">
            <a href="profil.php" class="btn-profil">Profil</a>
            <button type="button"class="logout-btn" onclick="deconnexion()">Déconnexion</button>
        </div>
           
    </header>
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