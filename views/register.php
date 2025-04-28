<?php
// 註冊頁面
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
                    <h3 class="text-center">會員註冊</h3>
                </div>
                <div class="card-body">
                    <form id="register-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">姓名</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">電話</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">密碼</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">確認密碼</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">地址</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">註冊</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <p>已經有帳號？ <a href="index.php?action=login">立即登入</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#register-form').submit(function (e) {
            e.preventDefault();

            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                alert('密碼與確認密碼不相符');
                return;
            }

            const formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                password: password,
                address: $('#address').val()
            };

            $.ajax({
                url: 'api/auth.php',
                method: 'POST',
                data: {
                    action: 'register',
                    ...formData
                },
                success: function (response) {
                    if (response.success) {
                        alert('註冊成功，請登入');
                        window.location.href = 'index.php?action=login';
                    } else {
                        alert(response.message || '註冊失敗，請稍後再試');
                    }
                }
            });
        });
    });
</script>