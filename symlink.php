<?php
/**
 * Script untuk membuat symlink Laravel di cPanel
 */

$targetFolder = '/home/frtljsqz/lazisnu-app/storage/app/public';

$linkFolder = '/home/frtljsqz/public_html/storage';

echo "<h3>Status Pembuatan Symlink:</h3>";

if (!file_exists($targetFolder)) {
    echo "<p style='color:red;'>❌ Gagal: Folder sumber (target) tidak ditemukan. Pastikan folder lazisnu-app/storage/app/public sudah ada.</p>";
    exit;
}

if (file_exists($linkFolder)) {
    echo "<p style='color:orange;'>⚠️ Symlink atau folder 'storage' sudah ada di public_html. Silakan hapus folder 'storage' di public_html terlebih dahulu (jika kosong/hanya file biasa).</p>";
} else {
    if (symlink($targetFolder, $linkFolder)) {
        echo "<p style='color:green;'>✅ BERHASIL! Symlink telah dibuat. File upload Anda sekarang bisa diakses.</p>";
    } else {
        echo "<p style='color:red;'>❌ Gagal membuat symlink. Kemungkinan fungsi symlink() diblokir oleh penyedia hosting.</p>";
    }
}
?>