<?php
session_start();

// 清除所有 session 變數
$_SESSION = array();

// 如果需要刪除 session cookie，則加上以下代碼
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 銷毀 session
session_destroy();

// 加入header
include __DIR__ . "/fixedFile/header.php";
?>

<div class="container text-center my-5">
    <div class="alert alert-success" role="alert">
        您已成功登出！
    </div>
    <p>正在為您重新導向...</p>
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script>
    // 1.5秒後自動跳轉
    setTimeout(function () {
        window.location.href = "index.php";
    }, 1500);
</script>

<?php
// 加入footer
include __DIR__ . "/fixedFile/footer.php";
?>