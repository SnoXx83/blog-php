<?php
// création de constantes pour les erreurs
const ERROR_REQUIRED = "Veuillez renseigner ce champ";
const ERROR_TITLE_TOO_SHORT = "Le titre est trop court";
const ERROR_CONTENT_TOO_SHORT = "L'article est trop court";
const ERROR_IMAGE_URL = "L'image doit être une url valide";

$filename = __DIR__ . '/data/articles.json'; //permet de récupérer le chemin du fichier que l’on execute.
$articles = [];
$category= '';

// tableau associatif qui va lister nos erreurs (si il y en a)
$errors = [
    'title' => '',
    'image' => '',
    'category' => '',
    'content' => ''
];

if (file_exists($filename)) {
    $articles = json_decode(file_get_contents($filename), true) ?? []; // recupere le contenu mais si il est vide je met un tableau vide
}

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if ($id) {
    $articleIdx = array_search($id, array_column($articles, 'id'));
    $article = $articles[$articleIdx];

    $title = $article['title'];
    $image = $article['image'];
    $category = $article['category'];
    $content = $article['content'];
}

// permet de filter le INPUT_POST (print_r($_SERVER)pour plus d'infos) (C'est à dire filtrer tout ce qui passe avec la methode POST dans le formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = filter_input_array(INPUT_POST, [
        'title' => FILTER_SANITIZE_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'category' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'content' => [
            'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
            'flag' => FILTER_FLAG_NO_ENCODE_QUOTES
        ]
    ]);
    // recuperation du formulaire avec la methode POST
    $title = $_POST['title'] ?? '';
    $image = $_POST['image'] ?? '';
    $category = $_POST['category'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!$title) {
        $errors['title'] = ERROR_REQUIRED;
    } elseif (mb_strlen($title) < 5) { // fonction qui permet de compter le nombre caractère
        $errors['title'] = ERROR_TITLE_TOO_SHORT;
    }

    if (!$image) {
        $errors['image'] = ERROR_REQUIRED;
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) { // Filtre une variable avec un filtre spécifique qui est ici filter validate url
        $errors['image'] = ERROR_IMAGE_URL;
    }

    if (!$category) {
        $errors['category'] = ERROR_REQUIRED;
    }

    if (!$content) {
        $errors['content'] = ERROR_REQUIRED;
    } elseif (mb_strlen($content) < 20) {
        $errors['content'] = ERROR_CONTENT_TOO_SHORT;
    }

    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
       
        if($id){
            // On reecrit l'article dans le json avec les nouvelles donnees
            $articles[$articleIdx]['title']= $title;
            $articles[$articleIdx]['image']= $image;
            $articles[$articleIdx]['category']= $category;
            $articles[$articleIdx]['content']= $content;
            

        }else{
            // Vous creez un nouvel article
            $articles = [...$articles, [ //spread operator
                'title' => $title,
                'image' => $image,
                'category' => $category,
                'content' => $content,
                'id' => time()
    
            ]];
        }

        file_put_contents($filename, json_encode($articles)); // cree le fichier si il existe pas
        header('Location: /'); // redirection
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/form-article.css">
    <title><?= $id ? 'Editer' : 'Créer' ?> un article</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1><?= $id ? 'Modifier' : 'Ecrire' ?> un article</h1>
                <form action="/form-article.php<?= $id ? "?id=$id" : '' ?>" method="POST">
                    <div class="form-control">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="<?= $title ?? '' ?>">
                        <?php if ($errors['title']) : ?>
                        <p class="text-danger"><?= $errors['title'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value="<?= $image ?? '' ?>">
                        <?php if ($errors['image']) : ?>
                        <p class="text-danger"><?= $errors['image'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-control">
                        <label for="category">Categorie</label>
                        <select name="category" id="category">
                            <option <?= !$category || $category === 'Technologie' ? 'selected' : '' ?>
                                value="Technologie">Technologie</option>
                            <option <?= !$category || $category === 'Nature' ? 'selected' : '' ?> value="Nature">Nature
                            </option>
                            <option <?= !$category || $category === 'Politique' ? 'selected' : '' ?> value="Politique">
                                Politique</option>
                        </select>
                        <?php if ($errors['category']) : ?>
                        <p class="text-danger"><?= $errors['category'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-control">
                        <label for="content">Contenu</label>
                        <textarea name="content" id="content"><?= $content ?? '' ?></textarea>
                        <?php if ($errors['content']) : ?>
                        <p class="text-danger"><?= $errors['content'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-action">
                        <a href="/" class="btn btn-secondary" type="button">Annuler</a>
                        <button class="btn btn-primary" type="submit"><?= $id ? 'Sauvegarder' : 'Publier' ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>

</html>