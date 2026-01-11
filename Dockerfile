# ใช้ PHP 8.2 กับ Apache
FROM php:8.2-apache

# เปิด mod_rewrite (ถ้าใช้ Laravel หรือ URL Rewriting)
RUN a2enmod rewrite

# คัดลอกโค้ดโปรเจคไปยัง container
COPY . /var/www/html/

# ตั้ง permission ถ้าจำเป็น (เช่น Laravel storage)
RUN chown -R www-data:www-data /var/www/html

# ใช้พอร์ต 80 ของ Apache
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
