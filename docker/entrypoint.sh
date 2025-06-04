#!/bin/bash

# Đợi database sẵn sàng
echo "Đang chờ database..."
until nc -z -v -w30 $DB_HOST $DB_PORT
do
  echo "Đợi kết nối tới DB tại $DB_HOST:$DB_PORT..."
  sleep 2
done

# Dọn cấu hình cũ, migrate và seed
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

# Khởi động Apache
exec apache2-foreground
