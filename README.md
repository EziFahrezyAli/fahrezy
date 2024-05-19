# Instalation Step

## URL `http://fahrezy.online/api`

## Documentation

[API Documentation](https://documenter.getpostman.com/view/12558616/2sA3QmCuY3)

Or you can download and import to your Postman APP from json file in `API Documentaion for postman` folder

## Spesification to run this project

-   Laravel 9
-   Php 8.1
-   Mysql

## Installation

-   clone this project

```bash
   git clone https://github.com/EziFahrezyAli/fahrezy.git
```

-   update and install dependencies

```bash
   composer update

   composer install
```

-   make file `.env` from `.env.example`

```bash
   cp .env.example .env
```

-   generate app key

```bash
   php artisan key:generate
```

-   change database name, username and password in `.env` file
-   change `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` and `GOOGLE_CALLBACK` with your google cloud console credential, you can find it [here](https://console.cloud.google.com/)
-   run migration with seeder

```bash
   php artisan migration:fresh --seed
```

-   then you can start the localhost server

```bash
   php artisan serve
```
