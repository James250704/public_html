<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getNavItems()
{
    $items = [
        ['url' => 'index.php', 'text' => '歐印精品'],
        ['url' => 'profile.php', 'text' => '品牌介紹'],
        ['url' => 'instructions.php', 'text' => '購物說明'],
        ['url' => 'repair.php', 'text' => '維修保固'],
        ['url' => 'about.php', 'text' => '關於我們'],
    ];

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['user_role'] === 'admin') {
        $items[] = ['url' => 'backend.php', 'text' => '後台管理'];
    } else {
        $items[] = ['url' => 'myOrder.php', 'text' => '我的訂單'];
    }

    return $items;
}

function renderNavItems($navItems, $class = '')
{
    foreach ($navItems as $item) {
        echo '<li class="nav-item">';
        echo '<a href="' . $item['url'] . '" class="nav-link text-black ' . $class . '" style="font-size: large;">';
        echo $item['text'];
        echo '</a></li>';
    }
}

function renderUserControls($isMobile = false)
{
    $btnClass = $isMobile ? 'w-100' : 'me-2';
    echo '<a href="shoppingCart.php" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">';
    echo '<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>';
    echo '</svg> 購物車</a>';

    if (!$isMobile) {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            echo '<a href="logout.php" class="btn btn-danger ' . $btnClass . '">登出</a>';
        } else {
            echo '<a href="login.php" class="btn btn-primary ' . $btnClass . '">登入</a>';
        }
    }
}
?>