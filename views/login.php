<?php
// 登入頁面
if (isset($_SESSION['logged_in'])) {
    header('Location: index.php?action=account');
    exit;
}
?>

<?php require_once __DIR__ . '/../header.php' ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">會員登入</h3>
                </div>
                <div class="card-body">
                    <form id="login-form">
                        <div class="mb-3">
                            <label for="phone" class="form-label">電話</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">密碼</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">登入</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <p>還沒有帳號？ <a href="index.php?action=register">立即註冊</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#login-form').submit(function (e) {
            e.preventDefault();

            const phone = $('#phone').val();
            const password = $('#password').val();

            $.ajax({
                url: 'api/auth.php',
                method: 'POST',
                data: {
                    action: 'login',
                    phone: phone,
                    password: password
                },
                success: function (response) {
                    if (response.success) {
                        window.location.href = 'index.php';
                    } else {
                        alert(response.message || '登入失敗，請檢查電話和密碼');
                    }
                }
            });
        });
    });
</script>