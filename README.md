# Yii2 app skeleton

## Requirements

1. PHP >=7.4
2. Composer >=2.0
3. Apache or NGINX server

or

1. Docker engine v 4.0+
2. Docker-compose


## Installation

### Apache+PHP

1. Copy project files to target folder
2. Run command "composer update"


### Docker compose

1. Run command `docker-compose run --rm php composer install`
2. Run command `chmod 755 yii`
3. Run command `chmod 777 web && chmod 777 web/*`
4. Run command `chmod 777 runtime`
5. Run container by command `docker-compose up -d`


## License

- MIT


## Author

- Anatoly Starodubtsev <Pantagruel74>
- Tostar74@mail.ru