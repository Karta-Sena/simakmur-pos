<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SiMakmur</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <nav class="sidebar">
        <div class="brand">SM</div>
        <div class="nav-group">
            <div class="nav-item active" onclick="Admin.nav('dashboard')" title="Dashboard"><i data-feather="grid"></i></div>
            <div class="nav-item" onclick="Admin.nav('products')" title="Menu"><i data-feather="coffee"></i></div>
            <div class="nav-item" onclick="window.location.href='../index.php'" title="Keluar"><i data-feather="log-out"></i></div>
        </div>
        <div class="user-avatar"><img src="https://ui-avatars.com/api/?name=Admin&background=6B1C23&color=fff"></div>
    </nav>

    <main class="main-pane" id="mainContent">
        <div style="padding:50px; text-align:center;">Memuat Dashboard...</div>
    </main>

    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/api.js"></script>
    <script src="js/app.js"></script>

</body>
</html>