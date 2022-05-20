<header>
    <a class="logo" href="/">BLOG APP</a>
    <ul class="header-menu">
        <li class="<?= $SERVER['REQUEST_URI'] === 'add-article.php' ? 'active' : '' ?>">
            <a href="/add-article.php">Ecrire un article</a>
        </li>
    </ul>
</header>