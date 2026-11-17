
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../config/Database.php';

function repondre($succes, $message, $code = 200, $erreurs = []) {
    http_response_code($code);

    echo json_encode([
        'succes' => $succes,
        'message' => $message,
        'erreurs' => $erreurs
    ]);

    exit;
}

// Vérifier la connexion
if (!isset($_SESSION['nom'])) {
    repondre(false, 'Votre session a expiré. Veuillez vous reconnecter.', 401);
}

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    repondre(false, 'Méthode non autorisée.', 405);
}

// Lire les données JSON
$donnees = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($donnees)) {
    repondre(false, 'Les données reçues sont invalides.', 400);
}

$motDePasseActuel = $donnees['mot_de_passe_actuel'] ?? null;
$nouveauMotDePasse = $donnees['nouveau_mot_de_passe'] ?? null;
$confirmation = $donnees['confirmation'] ?? null;

// Vérifier les types
if (
    !is_string($motDePasseActuel) ||
    !is_string($nouveauMotDePasse) ||
    !is_string($confirmation)
) {
    repondre(false, 'Les données fournies sont invalides.', 400);
}

// Validation des champs
$erreurs = [];

if ($motDePasseActuel === '') {
    $erreurs['mot_de_passe_actuel'] = 'Veuillez entrer votre mot de passe actuel.';
}

if ($nouveauMotDePasse === '') {
    $erreurs['nouveau_mot_de_passe'] = 'Veuillez entrer un nouveau mot de passe.';
} elseif (mb_strlen($nouveauMotDePasse) < 8) {
    $erreurs['nouveau_mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caractères.';
} elseif (strlen($nouveauMotDePasse) > 4096) {
    $erreurs['nouveau_mot_de_passe'] = 'Le mot de passe est trop long.';
}

if ($confirmation === '') {
    $erreurs['confirmation'] = 'Veuillez confirmer votre nouveau mot de passe.';
} elseif ($nouveauMotDePasse !== $confirmation) {
    $erreurs['confirmation'] = 'Les mots de passe ne correspondent pas.';
}

if (!empty($erreurs)) {
    repondre(false, 'Veuillez corriger les erreurs.', 422, $erreurs);
}

// Communication avec la BD
try {
    $stmt = $pdo->prepare(
        "SELECT mot_de_passe FROM users WHERE nom = ?"
    );

    $stmt->execute([$_SESSION['nom']]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        repondre(false, 'Utilisateur introuvable.', 404);
    }

    // Vérifier le mot de passe actuel
    if (!password_verify($motDePasseActuel, $utilisateur['mot_de_passe'])) {
        repondre(
            false,
            'Le mot de passe actuel est incorrect.',
            422,
            ['mot_de_passe_actuel' => 'Mot de passe incorrect.']
        );
    }

    // Empêcher de réutiliser le même mot de passe
    if (password_verify($nouveauMotDePasse, $utilisateur['mot_de_passe'])) {
        repondre(
            false,
            'Veuillez choisir un mot de passe différent.',
            422,
            ['nouveau_mot_de_passe' => 'Ce mot de passe est déjà utilisé.']
        );
    }

    // Hasher et enregistrer le nouveau mot de passe
    $hash = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);

    if ($hash === false) {
        throw new RuntimeException('Échec du hachage du mot de passe.');
    }

    $stmt = $pdo->prepare(
        "UPDATE users SET mot_de_passe = ? WHERE nom = ?"
    );

    $stmt->execute([$hash, $_SESSION['nom']]);

    repondre(true, 'Votre mot de passe a été modifié avec succès.');

} catch (PDOException $e) {
    error_log($e->getMessage());
    repondre(false, 'Une erreur est survenue lors de la communication avec la base de données.', 500);

} catch (Throwable $e) {
    error_log($e->getMessage());
    repondre(false, 'Une erreur inattendue est survenue. Veuillez réessayer.', 500);
}
