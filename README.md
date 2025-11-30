
# API de Gestión de Productos – Laravel

Esta es una API REST construida con **Laravel** que permite gestionar productos mediante operaciones **CRUD** (Crear, Leer, Actualizar y Eliminar).

## Requisitos

- PHP >= 8.1  
- Composer  
- Laravel >= 10  
- MySQL u otro motor compatible  
- Extensiones típicas necesarias: OpenSSL, PDO, Mbstring, Tokenizer, etc.

## Instalación


```bash
git clone https://github.com/CanonSh/api-gestion-productos.git
cd api-gestion-productos

composer install
cp .env.example .env
php artisan key:generate
