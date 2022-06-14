<?php

/**
 * @var ArticleDAO
 */

$articleDAO = require_once './database/models/ArticleDAO.php';


$pdo = require_once './database.php';
$statement = $pdo->prepare('DELETE FROM article WHERE id=:id');


$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';


if ($id) {
    $articleDAO->deleteOne($id);
}
header('Location:/');