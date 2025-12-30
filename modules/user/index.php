<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: /lab11_php_oop/modules/auth/login');
    exit;
}

// include class Barang
include_once __DIR__ . "/../../class/Barang.php";

// instance Barang
$barangObj = new Barang();
$barangList = $barangObj->getBarang();

// base url
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/lab11_php_oop/';

// fungsi badge stok
function getStockBadge($stok) {
    if ($stok >= 10) {
        return 'stock-high';
    } elseif ($stok >= 5) {
        return 'stock-medium';
    } else {
        return 'stock-low';
    }
}
?>

<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">

<div class="main-content">
    <div class="card">

        <div class="card-header">
            <h1><i class="fas fa-boxes"></i> Data Barang</h1>
            
            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                <a href="/lab11_php_oop/user/tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Barang Baru
                </a>
            <?php endif; ?>
            
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                $msg = [
                    'tambah' => 'Data berhasil ditambahkan!',
                    'ubah' => 'Data berhasil diubah!',
                    'hapus' => 'Data berhasil dihapus!'
                ];
                echo $msg[$_GET['success']] ?? 'Operasi berhasil!';
                ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>

                <?php if (!empty($barangList)) : ?>
                    <?php foreach ($barangList as $row) : ?>

                        <?php
                        $gambar = $row->gambar;
                        $path_gambar = $_SERVER['DOCUMENT_ROOT'] . '/lab11_php_oop/assets/gambar/' . $gambar;
                        $gambar_ada = ($gambar && file_exists($path_gambar));
                        ?>

                        <tr>
                            <td>
                                <?php if ($gambar_ada) : ?>
                                    <img src="<?= $base_url . 'assets/gambar/' . $row->gambar ?>"
                                         width="60" height="60" alt="<?= htmlspecialchars($row->nama) ?>"
                                         onerror="this.onerror=null; this.src='<?= $base_url ?>assets/gambar/default.jpg';">
                                <?php else : ?>
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row->nama); ?></td>
                            <td><?= $row->kategori; ?></td>
                            <td>Rp <?= number_format($row->harga_beli, 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($row->harga_jual, 0, ',', '.'); ?></td>

                            <td>
                                <span class="stock-badge <?= getStockBadge($row->stok); ?>">
                                    <?= $row->stok; ?>
                                </span>
                            </td>

                            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <td class="action-buttons">
                                <a href="/lab11_php_oop/user/edit?id=<?= $row->id_barang; ?>" 
                                   class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/lab11_php_oop/user/delete?id=<?= $row->id_barang; ?>"
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Yakin ingin menghapus barang <?= htmlspecialchars($row->nama) ?>?')"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="<?= $_SESSION['user']['role'] == 'admin' ? '7' : '6' ?>" style="text-align:center; padding: 20px;">
                            <div class="empty-state">
                                <i class="fas fa-box-open" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                                <p>Belum ada data barang</p>
                                <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                                    <a href="/lab11_php_oop/barang/tambah" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Barang Pertama
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image errors
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = '<?= $base_url ?>assets/images/default.jpg';
            this.alt = 'Gambar tidak tersedia';
        });
    });
    
    // Styling untuk badge stok
    const badges = document.querySelectorAll('.stock-badge');
    badges.forEach(badge => {
        const stok = parseInt(badge.textContent);
        if (stok >= 10) {
            badge.style.backgroundColor = '#d4edda';
            badge.style.color = '#155724';
        } else if (stok >= 5) {
            badge.style.backgroundColor = '#fff3cd';
            badge.style.color = '#856404';
        } else {
            badge.style.backgroundColor = '#f8d7da';
            badge.style.color = '#721c24';
        }
    });
});
</script>