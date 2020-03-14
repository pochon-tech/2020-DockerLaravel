## Vue-Laravel Tutorial

### 概要

- [命名規則](https://webdevetc.com/blog/laravel-naming-conventions)
- [DBからEloquent Model自動生成](https://github.com/krlove/eloquent-model-generator)

### 機能一覧

- 写真一覧
- 写真投稿 (会員のみ)
- 写真いいね付与 (会員のみ)
- 写真いいね除外 (会員のみ)
- 写真コメント追加 (会員のみ)
- 写真いいね数表示
- 写真コメント表示
- ログイン
- ログアウト (会員のみ)

### DB設計

**users**
- 認証機能があるのでユーザテーブルを作成
- Laravelのデフォルトのものを使用
- users : likes = 1 : n
- users : photos = 1 : n
- users : comments = 1 : n

|  列名  |  型  |  PRIMARY  |  UNIQUE  |  NOT NULL  |  FOREIGN  |
| ---- | ---- | ---- | ---- | ---- | ---- |
|  id  |  SERIAL  |  🔑  |  ✅  |  ✅  |  -  |
|  name  |  VARCHAR(255)  |  -  |  -  |  ✅  |  -  |
|  email  |  VARCHAR(255)  |  -  |  -  |  ✅  |  -  |
|  password  |  VARCHAR(255)  |  -  |  -  |  ✅  |  -  |
|  remember_token  |  VARCHAR(100)  |  -  |  -  |  -  |  -  |
|  email_verified_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |
|  created_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |
|  updated_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |

**photos**
- 写真を格納する￥テーブル
- photos : likes = 1 : n
- photos : comments = 1: n
- table

|  列名  |  型  |  PRIMARY  |  UNIQUE  |  NOT NULL  |  FOREIGN  |
| ---- | ---- | ---- | ---- | ---- | ---- |
|  id  |  VARCHAR(255)  |  🔑  |  ✅  |  ✅  |  -  |
|  user_id  |  INTEGER  |  -  |  -  |  ✅  |  users(id)  |
|  filename  |  VARCHAR(255)  |  -  |  -  |  ✅  |  -  |
|  created_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |
|  updated_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |

**likes**
- 写真に対してのいいねを格納するテーブル
- 複数のユーザがいいねをすることを想定し、photosにはカラムを設けない

|  列名  |  型  |  PRIMARY  |  UNIQUE  |  NOT NULL  |  FOREIGN  |
| ---- | ---- | ---- | ---- | ---- | ---- |
|  id  |  SERIAL  |  🔑  |  ✅  |  ✅  |  -  |
|  photo_id  |  VARCHAR(255)  |  -  |  -  |  ✅  |  photos(id)  |
|  user_id  |  INTEGER  |  -  |  -  |  ✅  |  users(id)  |
|  created_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |
|  updated_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |

**comments**
- 写真に対してのコメントを格納するテーブル

|  列名  |  型  |  PRIMARY  |  UNIQUE  |  NOT NULL  |  FOREIGN  |
| ---- | ---- | ---- | ---- | ---- | ---- |
|  id  |  SERIAL  |  🔑  |  ✅  |  ✅  |  -  |
|  photo_id  |  VARCHAR(255)  |  -  |  -  |  ✅  |  photos(id)  |
|  user_id  |  INTEGER  |  -  |  -  |  ✅  |  users(id)  |
|  content  |  TEXT  |  -  |  -  |  ✅  |  -  |
|  created_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |
|  updated_at  |  TIMESTAMP  |  -  |  -  |  ✅  |  -  |


### URL設計
- URL が目的語で HTTP メソッドが動詞
- API

|  URL  |  メソッド  |  認証  |  内容 |
| ---- | ---- | ---- | ---- |
|  /api/photos  |  GET  |  -  |  写真一覧取得 |
|  /api/photos  |  POST  |  🔒  |  写真登録 |
|  /api/photos/{写真ID}  |  GET  |   -  |  写真詳細取得 |
|  /api/photos/{写真ID}/like   |  PUT  |  🔒  |  写真(いいね付与) |
|  /api/photos/{写真ID}/like   |  DELETE  |  🔒  |  写真(いいね削除) |
|  /api/photos/{写真ID}/comments   |  DELETE  |  🔒  |  写真更新(コメント付与) |
|  /api/register  |  POST  |  -  |  会員登録 |
|  /api/login  |  POST  |  -  |  ログイン |
|  /api/logout  |  POST  |  🔒  |  ログアウト |
|  /api/user  |  GET  |  -  |  認証ユーザー取得 |

- API以外

|  URL  |  メソッド  |  認証  |  内容 |
| ---- | ---- | ---- | ---- |
|  /  |  GET  |  -  |  HTML返却 |
|  /photos/{写真ID}/download  |  GET  |  -  |  写真ダウンロード |

- フロント

|  URL  | 内容 |
| ---- | ---- |
|  /  |  写真一覧ページ |
|  /photos/{写真ID} | 写真詳細 |
|  /login |  ログイン・会員登録ページ |


### 環境構築
**Dockerfile**
```Dockerfile:
FROM php:7.3-apache

RUN apt update && apt-get install -y git libzip-dev
RUN docker-php-ext-install pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN a2enmod rewrite

WORKDIR /var/www

# Install opcache apcu
RUN docker-php-ext-install opcache
RUN pecl install apcu
RUN docker-php-ext-enable apcu

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
RUN apt-get install -y nodejs
```
**docker-compose.yml**
```yaml:
version: '3.4'
x-logging:
  &default-logging
  driver: "json-file"
  options:
    max-size: "100k"
    max-file: "3"
volumes:
  mysql_data: { driver: local }
services:

  mysql:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: pass
      MYSQL_DATABASE: db
      MYSQL_USER: user
      MYSQL_PASSWORD: pass
      TZ: 'Asia/Tokyo'
    volumes:
      - mysql_data:/var/lib/mysql

  www:
    build: ./
    logging: *default-logging
    volumes:
      - ./www:/var/www
      - ./www/php.ini:/usr/local/etc/php/php.ini
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_DATABASE=db
      - DB_USERNAME=user
      - DB_PASSWORD=pass
    ports:
      - 80:80
      - 3000:3000
```
**container run**
```sh:
apple@appurunoMacBook-Pro 2020-03-DockerLaravelVue % mkdir www
apple@appurunoMacBook-Pro 2020-03-DockerLaravelVue % docker-compose up -d --build
```
**create Laravel project**
```sh:
apple@appurunoMacBook-Pro 2020-03-DockerLaravelVue % docker-compose exec www bash
root@615eb468b2ff:/var/www# composer create-project --prefer-dist "laravel/laravel:^6.9" laravel
```
**setup Laravel**
- edit composer.json
```json:
// ... require
"barryvdh/laravel-debugbar": "*",
"barryvdh/laravel-ide-helper": "*",
```
- update composer & document root symbolic
```sh:
root@615eb468b2ff:/var/www# cd laravel
root@615eb468b2ff:/var/www/laravel# composer update
root@615eb468b2ff:/var/www/laravel# php artisan ide-helper:generate
root@615eb468b2ff:/var/www/laravel# ln -s laravel/public/ ../html
# 開発サーバーを立ち上げる場合
root@615eb468b2ff:/var/www/laravel# php artisan serve --host 0.0.0.0 --port 8000
```
- setup locale (`config/app.php`)
```php:
'locale' => 'ja',
```
- setup env (.env)
```:
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=db
DB_USERNAME=user
DB_PASSWORD=pass
```
- setup .editorconfig
```:
[*.{yml,json,scss,html,js,vue,blade.php}]
indent_size = 2
```
- open blowser
- http://localhost/