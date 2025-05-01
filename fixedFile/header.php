<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="imgs/webimg.ico" type="image/x-icon">
    <title>歐印精品-</title>
</head>

<body>
    <?php require_once __DIR__ . '/../api/header.php'; ?>
    <div class="fixed-top bg-white m-0 border-bottom border-2">
        <nav class="container position-relative">
            <!-- 電腦版導航 (xl以上) -->
            <div class="d-none d-xl-flex flex-wrap align-items-center justify-content-between py-2 m-0">
                <a href="index.php" class="d-flex align-items-center mb-0 text-dark text-decoration-none">
                    <img src="imgs/title.png" alt="" height="49">
                </a>

                <ul class="nav justify-content-center mb-xl-0 gap-4">
                    <?php renderNavItems(getNavItems(), 'px-2 link-dark'); ?>
                </ul>

                <div class="text-center gap-2 d-flex flex-row flex-nowrap">
                    <?php renderUserControls(false); ?>
                </div>
            </div>

            <!-- 移動版導航 (xl以下) -->
            <div class="d-flex d-xl-none flex-wrap align-items-center justify-content-between py-2 m-0">
                <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNavHeader"
                    aria-controls="collapseNavHeader" aria-expanded="false">
                    <span class="navbar-toggler-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                            class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                        </svg>
                    </span>
                </button>

                <a href="index.php" class="d-flex align-items-center mb-0 text-dark text-decoration-none">
                    <img src="imgs/title.png" alt="" height="49">
                </a>

                <div class="text-center gap-2">
                    <?php renderUserControls(true); ?>
                </div>
            </div>
        </nav>

        <div class="collapse bg-white border-top" id="collapseNavHeader">
            <div class="container py-3">
                <ul class="navbar-nav nav-pills flex-column mb-auto">
                    <?php renderNavItems(getNavItems(), 'px-3'); ?>
                </ul>
                <div class="d-grid gap-2 mb-3">
                    <?php renderUserControls(false); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fulid pt-5 mt-3">