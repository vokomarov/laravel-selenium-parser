# Laravel Selenium Parser

Web application to easily parse content of third party sites.

## Local Test

```bash
$ docker-compose up -d
$ cp .env.example .env
$ ./cmd composer install
$ ./cmd npm install
$ ./cmd npm run prod
$ ./cmd php artisan key:generate
$ ./cmd php artisan migrate

```

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
