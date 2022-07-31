# invest-stat

[![CI-BUILD-TEST](https://github.com/simba77/invest-stat/workflows/CI-BUILD-TEST/badge.svg?branch=master)](https://github.com/simba77/invest-stat/actions)
[![DEPLOY](https://github.com/simba77/invest-stat/workflows/DEPLOY/badge.svg?branch=master)](https://github.com/simba77/invest-stat/actions)

The personal project for tracking investment statistics

## Installation

```bash
git clone git@github.com:simba77/invest-stat.git invest-stat.loc
cd invest-stat.loc
composer install
```

Copy the .env file and change the database connection settings

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

```bash
npm install
```

```bash
npm run build
```

For development mode, use the command

```bash
npm run dev
```

## License

[MIT license](https://opensource.org/licenses/MIT)
