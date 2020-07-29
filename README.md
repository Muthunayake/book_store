# book_store
Symfony base shopping cart for bookstore

#PHP Version
7.3.5

#Symfony Version
4.16.5 

# To run the project try floowing steps

# install dependency
composer update

# set local db config to .env
MYSQL_USER=root
MYSQL_PASSWORD=
MYSQL_DATABASE=99x_sp_cart_ass
MYSQL_HOST=127.0.0.1

# create database
php bin/console doctrine:database:create

# create table
php bin/console doctrine:schema:update --force

# run sample data
php bin/console doctrine:fixtures:load

# run the project
symfony server:start or php bin/console server:run

test 1.2.3
