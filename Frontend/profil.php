
<?php
session_start();

if (!isset($_SESSION['nom'], $_SESSION['role'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - KWIZ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <h1>Mon profil</h1>

        <div>
            <label for="nom">Nom d'utilisateur</label>
            <input
                type="text"
                id="nom"
                value="<?= htmlspecialchars($_SESSION['nom']) ?>"
                disabled
            >
        </div>

        <div>
            <label for="role">Rôle</label>
            <input
                type="text"
                id="role"
                value="<?= htmlspecialchars($_SESSION['role']) ?>"
                disabled
            >
        </div>

        <h2>Changer le mot de passe</h2>

        <p id="message" role="status"></p>

        <form id="profilForm">
            <div>
                <label for="mot_de_passe_actuel">Mot de passe actuel</label>
                <input
                    type="password"
                    id="mot_de_passe_actuel"
                    autocomplete="current-password"
                    required
                >
                <span class="erreur" id="erreur_mot_de_passe_actuel"></span>
            </div>

            <div>
                <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
                <input
                    type="password"
                    id="nouveau_mot_de_passe"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >
                <span class="erreur" id="erreur_nouveau_mot_de_passe"></span>
            </div>

            <div>
                <label for="confirmation">Confirmer le nouveau mot de passe</label>
                <input
                    type="password"
                    id="confirmation"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >
                <span class="erreur" id="erreur_confirmation"></span>
            </div>

            <button type="submit" id="btnModifier">
                Modifier le mot de passe
            </button>
        </form>
    </main>

    <script>
        const formulaire = document.getElementById('profilForm');
        const message = document.getElementById('message');
        const bouton = document.getElementById('btnModifier');

        const champs = [
            'mot_de_passe_actuel',
            'nouveau_mot_de_passe',
            'confirmation'
        ];

        function effacerErreurs() {
            message.textContent = '';

            champs.forEach(champ => {
                document.getElementById(champ).classList.remove('input-erreur');
                document.getElementById('erreur_' + champ).textContent = '';
            });
        }

        function afficherErreurs(erreurs) {
            Object.entries(erreurs).forEach(([champ, texte]) => {
                if (champs.includes(champ)) {
                    document.getElementById(champ).classList.add('input-erreur');
                    document.getElementById('erreur_' + champ).textContent = texte;
                }
            });
        }

        formulaire.addEventListener('submit', async function(e) {
            e.preventDefault();
            effacerErreurs();

            const donnees = {
                mot_de_passe_actuel: document.getElementById('mot_de_passe_actuel').value,
                nouveau_mot_de_passe: document.getElementById('nouveau_mot_de_passe').value,
                confirmation: document.getElementById('confirmation').value
            };

            bouton.disabled = true;
            bouton.textContent = 'Modification en cours...';

            try {
                const reponse = await fetch('../api/profil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(donnees)
                });

                const resultat = await reponse.json();

                message.textContent = resultat.message || 'Réponse invalide du serveur.';
                message.style.color = resultat.succes ? 'green' : 'red';

                if (resultat.erreurs) {
                    afficherErreurs(resultat.erreurs);
                }

                if (resultat.succes) {
                    formulaire.reset();
                }

                if (reponse.status === 401) {
                    message.textContent = 'Votre session a expiré. Redirection...';
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }

            } catch (erreur) {
                message.textContent = 'Impossible de contacter le serveur. Veuillez réessayer.';
                message.style.color = 'red';

            } finally {
                bouton.disabled = false;
                bouton.textContent = 'Modifier le mot de passe';
            }
        });

        champs.forEach(champ => {
            document.getElementById(champ).addEventListener('input', function() {
                this.classList.remove('input-erreur');
                document.getElementById('erreur_' + champ).textContent = '';
            });
        });
    </script>

</body>
</html>
