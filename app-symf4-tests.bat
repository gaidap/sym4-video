@rem app-symf4-tests.bat
@echo off

IF "%2"=="-db" (
ECHO "rebuilding database ..."
php bin\console doctrine:schema:drop -n -q --force --full-database
del migrations\*.php
php bin\console make:migration
php bin\console doctrine:migrations:migrate -n -q
php bin\console doctrine:fixtures:load -n -q
)

IF "%1" == [] (
php bin\phpunit
) ELSE (
php bin\phpunit "%1"
)
