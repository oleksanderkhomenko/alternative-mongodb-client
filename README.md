# alternative-mongodb-client

## Installation
1. Run "composer install" from the project folder
2. If you have problems with composer installation - please
install mongodb - http://php.net/manual/ru/mongodb.installation.pecl.php

## Usage
1. Open terminal
2. Enter command "php index.php"
3. Enter database name (name should be without spaces and be not empty)
4. Enter SQL
4.1. Base words like SELECT, FROM, ... must be used only 1 time
4.2. List of base words: SELECT, FROM, WHERE, ORDER BY, SKIP, LIMIT
4.3. To form WHERE condition you can use AND|OR several times
4.4. Each word or symbol should be separeted by space (rule is optional for arguments after SELECT)
4.5. Example of valid SQL - "SELECT firs_name,tags.0 FROM users WHERE first_name = Mongo AND last_name = Kongo ORDER BY last_name ASC SKIP 10 LIMIT 5",
"SELECT * FROM users LIMIT 1"
5. To run unit tests - enter "./vendor/bin/phpunit"
6. To form code coverage - enter "./vendor/bin/phpunit --coverage-html coverage/"
6.1. This will create coverage folder with results inside project folder
