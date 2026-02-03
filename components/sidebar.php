<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-sack-dollar fa-2x"></i>
        <div class="brand-text">
            <span>PAYROLL</span>
            <small>System v1.0</small>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="dashboard.php" class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <div class="icon"><i class="fa-solid fa-gauge-high"></i></div>
            <span>Dashboard</span>
        </a>
        
        <a href="data_pegawai.php" class="<?php echo ($page == 'pegawai') ? 'active' : ''; ?>">
            <div class="icon"><i class="fa-solid fa-users-gear"></i></div>
            <span>Data Pegawai</span>
        </a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') : ?>
            <a href="manajemen_user.php" class="<?php echo ($page == 'user') ? 'active' : ''; ?>">
                <div class="icon"><i class="fa-solid fa-user-shield"></i></div>
                <span>Manajemen Akun</span>
            </a>
        <?php endif; ?>
        
        <a href="../auth/logout.php" class="logout-btn">
            <div class="icon"><i class="fa-solid fa-power-off"></i></div>
            <span>Logout</span>
        </a>
    </div>
</div>