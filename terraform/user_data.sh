#!/bin/bash
# Actualizar el sistema
sudo yum update -y

# Instalar Apache y PHP
sudo yum install -y httpd php

# Habilitar y arrancar Apache
sudo systemctl enable httpd
sudo systemctl start httpd

# Limpiar el directorio web
sudo rm -rf /var/www/html/*

# Clonar y configurar la aplicación web
cd /var/www/html
sudo dnf install -y git
sudo git clone https://github.com/ferminromero00/Ejercicio4-Fermin.git

# Configurar permisos
sudo chown -R apache:apache /var/www/html
sudo chmod -R 755 /var/www/html

#Hacemos la documentacion de php a traves de docker
cd /var/www/html/php
sudo yum install docker -y
sudo service docker start
sudo usermod -a -G docker ec2-user

sudo mkdir docs
sudo docker run --rm -v "$(pwd)":/app -w /app phpdoc/phpdoc -d . -t ./docs

# Configurar Apache para PHP
echo "AddType application/x-httpd-php .php" | sudo tee /var/www/html/.htaccess
echo "DirectoryIndex index.php index.html" | sudo tee -a /var/www/html/.htaccess

# Reiniciar Apache
sudo systemctl restart httpd