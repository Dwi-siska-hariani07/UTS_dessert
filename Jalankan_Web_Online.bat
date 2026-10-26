@echo off
title Sweetie Dessert - Online HTTPS Cloudflare Tunnel
color 0D
cls
echo ======================================================================
echo          SWEETIE DESSERT - AKTIFKAN WEB ONLINE HTTPS RESMI
echo ======================================================================
echo.
echo [1] Pastikan XAMPP (Apache dan MySQL) sudah AKTIF (Start)!
echo [2] Menghubungkan web Sweetie Dessert ke Cloudflare Tunnel...
echo.
echo Tunggu beberapa detik sampai muncul kotak link HTTPS seperti ini:
echo +--------------------------------------------------------------------+
echo ^| https://xxxx-xxxx-xxxx.trycloudflare.com                           ^|
echo +--------------------------------------------------------------------+
echo.
echo CATATAN PENTING:
echo - Link tersebut bisa langsung kamu buka di HP, Laptop, atau iPad!
echo - Bagikan link tersebut ke dosen atau teman kamu saat presentasi.
echo - Jangan tutup jendela hitam ini selama kamu ingin web tetap online.
echo ----------------------------------------------------------------------
echo.

cd /d "C:\xampp\htdocs\UTS_dessert"
cloudflared.exe tunnel --url http://localhost:80 --protocol http2
pause
