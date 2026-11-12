# Panduan Deploy Cepat di CentOS

1. Ekstrak folder `crud_produk` ke `/var/www/html/`:
   ```bash
   unzip crud_produk.zip -d /var/www/html/
   ```

2. Import database MariaDB:
   ```bash
   mysql -u root -p < /var/www/html/crud_produk/database.sql
   ```

3. Atur permission & SELinux agar upload foto berjalan normal:
   ```bash
   chown -R apache:apache /var/www/html/crud_produk
   chmod -R 755 /var/www/html/crud_produk
   chmod -R 775 /var/www/html/crud_produk/uploads
   chcon -R -t httpd_sys_rw_content_t /var/www/html/crud_produk/uploads
   ```
