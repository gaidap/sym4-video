@rem app-symf4-rebuild-db.bat
@echo off

ECHO "rebuilding database ..."
php bin\console doctrine:schema:drop -n -q --force --full-database
del migrations\*.php
php bin\console make:migration
php bin\console doctrine:migrations:migrate -n -q
php bin\console doctrine:fixtures:load -n -q
