System requirement
-------------------
1. PHP(>=7.2.5)
2. Mysql(>=8) or MariaDB(>=10)
3. Composer(>=2.5)

How to run
-----------
1. Install all dependency "composer install".
2. Change host name, db name, db user name, db password, version from .env file
    Example: mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4
    Here,
    username = app, 
    password = !ChangeMe! (Keep it blank if password is blank),
    host name with port = 127.0.0.1:3306
    version = 8
3. Then run "php bin/console doctrine:database:create" to create database.
4. Run migration command to create tables "php bin/console doctrine:migrations:migrate".
5. Run "php bin/console app:create-shopkeeper" command to create a shopkeeper for books and other management.




