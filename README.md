# multicurtest-final-scenario


## Description
This is final module for test-task for Divan.ru thats uses other services 
for execute final scenario (at `/test-scenario`). 
Implements required stores on mysql-database.


## Submodules

#### pantagruel74/multicurtest-currency-manager
https://github.com/mnemesong/multicurtest-currency-manager
Manager implements currency creation, store and calculation logic, thats
need for all other modules (look unit tests in project repo)

#### pantagruel74/multicurtest-bank-management-service
https://github.com/mnemesong/multicurtest-bank-management-service
Service implements logic of total bank management: total currencies
and currency-rates management and global account operations 
(look unit tests in project repo)

#### pantagruel74/multicurtest-private-operations-service
https://github.com/mnemesong/multicurtest-private-operations-service
Service implements logic of account private operations
(look unit tests in project repo)

#### pantagruel74/multicurtest-account-administrations-service
https://github.com/mnemesong/multicurtest-account-administrations-service
Service implements logic of account management operations.
(look unit tests in project repo)


## Requirements
1. PHP >=7.4
2. Composer >=2.0
3. Apache or NGINX server
4. Mysql or any sql database


## Installation
1. Copy project files to target folder
2. Run command `composer update`
3. Correct configs use your sql access data and switch on sql database
4. Run migrations by command `php yii migrate`


## Run test scenario
Run test scenario by command `composer test:scenario`


## Author
- Anatoly Starodubtsev <Pantagruel74>
- Tostar74@mail.ru