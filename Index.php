<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulaire pour soumettre une URL à IndexNow.">
    <title>Soumission IndexNow</title>
    <link href="css/style.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <h1>Soumettre une URL à IndexNow</h1>
        <form method="POST" name="formURL" id="formURL" class="top50">
            <div class="row">
                <div class="col-sm-3">
                    <input type="url" class="form-control" name="url" id="url" 
                           placeholder="https://"
                           required>
                </div>
                <div class="col-sm-3">
                    <button type="submit" class="btn">Envoyer à IndexNow</button>
                </div>
            </div>
        </form>

        <div class="message">
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
                    $submittedUrl = filter_var($_POST['url'], FILTER_SANITIZE_URL);

                    if (filter_var($submittedUrl, FILTER_VALIDATE_URL)) {
                        $key = "0123456789abcdef";
                        $endpoint = "https://api.indexnow.org/IndexNow";
                        $data = [
                            "host" => parse_url($submittedUrl, PHP_URL_HOST),
                            "key" => $key,
                            
                            // Modifier avec le chemin et le nom du fichier
                            "keyLocation" => "http://mywebsite.com/0123456789abcdef.txt",
                            
                            
                            "urlList" => [$submittedUrl]
                        ];

                        $ch = curl_init($endpoint);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json; charset=utf-8',
                            'Host: api.indexnow.org'
                        ]);

                        $response = curl_exec($ch);

                        if (curl_errno($ch)) {
                            echo "<div class='error'>Erreur cURL : " . curl_error($ch) . "</div>";
                        } else {
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            if ($httpCode === 200) {
                                echo "<div class='success'>Succès : L'URL a été envoyée avec succès à IndexNow.</div>";
                            } elseif ($httpCode === 202) {
                                echo "<div class='success'>Succès partiel : L'URL a été acceptée par IndexNow et sera traitée sous peu.</div>";
                            } else {
                                echo "<div class='error'>Erreur HTTP ($httpCode) : " . htmlspecialchars($response) . "</div>";
                            }
                        }

                        curl_close($ch);
                    } else {
                        echo "<div class='error'>Erreur : L'URL soumise n'est pas valide.</div>";
                    }
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo "<div class='error'>Erreur : Aucune URL soumise.</div>";
                }
            ?>
        </div>
    </div>
</body>
</html>
