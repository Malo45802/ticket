<?php
// ---------- Connexion à la base de données (MySQLi) ----------
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli("localhost", "root", "", "ticket");
    $mysqli->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Connexion impossible à la base de données.");
}

// ---------- Traitement du formulaire ----------
$message  = "";
$articles = [];   // sert uniquement pour le code 0 (liste complète)

if (isset($_POST['code'])) {
    $saisie = trim($_POST['code']);

    if (!ctype_digit($saisie)) {
        $message = "Le code doit être un nombre entier positif.";
    } else {
        $code = (int) $saisie;

        if ($code === 0) {
            // Cas particulier : le code 0 liste tous les articles
            $resultat = $mysqli->query("SELECT code, article FROM articles ORDER BY code");
            while ($ligne = $resultat->fetch_assoc()) {
                $articles[] = $ligne;
            }
            $resultat->free();
            $message = "Le code 0 est un cas particulier : voici la liste de tous les articles ("
                     . count($articles) . ").";

        } elseif ($code >= 1 && $code <= 999) {
            // Code valide : on cherche l'article correspondant
            $stmt = $mysqli->prepare("SELECT article FROM articles WHERE code = ?");
            $stmt->bind_param("i", $code);
            $stmt->execute();
            $stmt->bind_result($article);

            if ($stmt->fetch()) {
                $message = "Le code $code est correct. L'information correspondante est : "
                         . htmlspecialchars($article) . ".";
            } else {
                $message = "Le code $code est correct mais aucun article ne lui correspond.";
            }
            $stmt->close();

        } else {
            // Plus de 999
            $message = "Le code est incorrect, trop grand !";
        }
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
</head>
<body>
    <h1>Ticket</h1>

    <form method="post" action="index.php">
        <label for="code">Entrez un code (1 à 999, ou 0 pour tout lister) :</label>
        <input type="text" name="code" id="code" autofocus>
        <button type="submit">Valider</button>
    </form>

    <?php if ($message !== ""): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <?php if (!empty($articles)): ?>
        <table border="1" cellpadding="4">
            <tr><th>Code</th><th>Article</th></tr>
            <?php foreach ($articles as $a): ?>
                <tr>
                    <td><?= $a['code'] ?></td>
                    <td><?= htmlspecialchars($a['article']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>