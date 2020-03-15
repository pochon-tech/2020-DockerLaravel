## Vue-Laravel Tutorial

### 概要

- [命名規則](https://webdevetc.com/blog/laravel-naming-conventions)
- [DBからEloquent Model自動生成](https://github.com/krlove/eloquent-model-generator)
- VSCODEでの開発の場合に入れておくと良い
  - PHP-intellisense
  - PHPIntelephense
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
FROM php:7.4-apache

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
      - 8000:8000
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
"php": "^7.4",
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

**Frontend**

- setup JavaScript
```sh:
root@615eb468b2ff:/var/www/laravel# npm install
root@615eb468b2ff:/var/www/laravel# npm install -D vue
```

**LaravelMixについて**
- Laravel,Vue,SCSSのコンパイルする仕組みが備わっているのでGripやWebpackの設定が不要になる
- ビルドの際に設定ファイル webpack.mix.js が参照され、 Webpack の設定が動的に生成される

**setup LaravelMix**
```js:
const mix = require('laravel-mix');

// mix.js('resources/js/app.js', 'public/js').sass('resources/sass/app.scss', 'public/css');
mix.browserSync({
    proxy: '0.0.0.0:8000',
    open: false
  })
  .js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css')
  .version()
```
- browserSync: JavaScriptやPHPファイルが変更されたときに自動的にブラウザがリロードされるようになる
- js: JavaScriptとVueコンポーネントをコンパイル
- sass: SCSSをコンパイル (SCSSファイル予め用意していたものを`resources/sass/app.scss`に格納する)
- version: コンパイルしたファイルのバージョニングを有効化
  - ブラウザは一度取得したファイルをキャッシュに保存するので、ファイルの内容を変更してもページに反映されないことがある
  - これを有効にすることで、ビルドのたびにコンパイルしたファイルのURLにランダムな文字列が付けられる
  - この機能はテンプレート側でmix関数と組み合わせて使用する
  ```html:
    <script src="{{ mix('js/app.js') }}" defer></script>
  ```
  - 上記が以下のようなHTMLに変換される
  ```html:
    <script src="/js/app.js?id=87459a9d906e11120dd5" defer=""></script>
  ```
  
### API以外のアクセス制御の実装

- APIのURL以外のアクセス周りを実装する

**ルーティング**
- `routes/web.php`を以下のように編集
```php:
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');
```
- 上記はAPI以外のURLはindexテンプレートを返す (画面遷移はフロントエンドのVueRouterが制御)

**テンプレート**
- `resources/views/index.blade.php`を作成
```php:
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name') }}</title>

  <!-- Scripts -->
  <script src="{{ mix('js/app.js') }}" defer></script>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather|Roboto:400">
  <link rel="stylesheet" href="https://unpkg.com/ionicons@4.2.2/dist/css/ionicons.min.css">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/app.scss') }}">
</head>
<body>
  <div id="app"></div>
</body>
</html>
```

**JavaScript**
- `resources/js/app.js`を作成
```js:
import Vue from 'vue'

new Vue({
  el: '#app',
  template: '<h1>Hello world</h1>'
})
```

**フロントのビルド**
- ビルドコマンドはnpmスクリプトにまとめられているので、以下のコマンドで監視モードのコンパイルが走る
- ファイルの変更があるたびに自動的に再度コンパイルが実行される
- 下記のコマンドを実行することで監視モードに入る
```sh:
root@1d5a5d91dadb:/var/www/laravel# npm run watch
```

<details>
<summary>初回のwatch実行時</summary>

- 下記のようなログの出力後に処理が終わるが
- これは、必要なパッケージをLaravel Mixがインストールしている記録でありエラーではない
- 再度`npm run watch`を実行すれば問題なく動作する
```sh:
root@d885bd7fe28d:/var/www/laravel# npm run watch

> @ watch /var/www/laravel
> npm run development -- --watch


> @ development /var/www/laravel
> cross-env NODE_ENV=development node_modules/webpack/bin/webpack.js --progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js "--watch"

        Additional dependencies must be installed. This will only take a moment.
 
        Running: npm install vue-template-compiler --save-dev --production=false
 
npm WARN optional SKIPPING OPTIONAL DEPENDENCY: fsevents@1.2.11 (node_modules/fsevents):
npm WARN notsup SKIPPING OPTIONAL DEPENDENCY: Unsupported platform for fsevents@1.2.11: wanted {"os":"darwin","arch":"any"} (current: {"os":"linux","arch":"x64"})

        Okay, done. The following packages have been installed and saved to your package.json dependencies list:
 
        - vue-template-compiler
 
        Additional dependencies must be installed. This will only take a moment.
 
        Running: npm install browser-sync browser-sync-webpack-plugin@2.0.1 --save-dev --production=false
 
npm WARN browser-sync-webpack-plugin@2.0.1 requires a peer of webpack@^1 || ^2 || ^3 but none is installed. You must install peer dependencies yourself.
npm WARN optional SKIPPING OPTIONAL DEPENDENCY: fsevents@1.2.11 (node_modules/fsevents):
npm WARN notsup SKIPPING OPTIONAL DEPENDENCY: Unsupported platform for fsevents@1.2.11: wanted {"os":"darwin","arch":"any"} (current: {"os":"linux","arch":"x64"})

        Okay, done. The following packages have been installed and saved to your package.json dependencies list:
 
        - browser-sync
 
        - browser-sync-webpack-plugin@2.0.1
 
        Finished. Please run Mix again.
 
```
</details>


<details>
<summary>LaravelでのBrowsersync</summary>

- `php artisan serve`: Webページをブラウザに提供し、ブラウザがコンパイル済みアセットを取得する
- `npm run watch`: Webpackアセットのコンパイル「監視」を実行し、アセットソースファイルが変更されて再コンパイルされた場合、Webページを提供しない

**Browsersyncとは**
- Laravelを標準的にインストールしたら一緒に入ってくるファイル変更検知で自動ブラウザリロードの仕組み

**Browsersyncを使ったリクエストをさばく仕組み**

- リクエストをインターセプト
  - BrowserSyncはListenしているhost、portでリクエストをインターセプト
- proxy配下へリクエストをバケツリレー、なんか付加してブラウザへレスポンスを返却
  - proxy配下へリクエスト内容をバケツリレー
  - 配下から返却をBrowserSyncが取得
  - BrowserSyncはリロードの仕組みを返却に付与
  - レスポンスをブラウザへ返却

**自動リロードの仕組み**

- 上で、サーバー側からブラウザへ命令を送信できる仕組みできている
- チェック設定のファイルの変更を検知
  - 設定ファイルのfilesに設定した内容でファイルシステムの変更をチェック
- 変更があればブラウザリロード命令を発行
  - レスポンスに付与した仕組みを使ってブラウザへリロード命令を送る
  - ブラウザは画面のリロードを実行

**結論**
- `php artisan serve`で8000番ポートでサーバからブラウザへ命令を送信できる仕組みを作る
- `npm run watch`で3000番ポートを監視状態にして、自動リロードを実現する
- つまり、8000番ポートをPHPサーバーとして起動していないと、3000番でのwatchが実現できない
- 8000番で確認できる画面は自動リロードされる画面ではない(=80番ポートと等しい)

</details>

### VueRouterの実装

- VueRouterをインストール
```sh:
root@1d5a5d91dadb:/var/www/laravel# npm install -D vue-router
```

**ルートコンポーネントの作成**
- `resources/js/App.vue`を作成
```js:
<template>
  <div>
    <main>
      <div class="container">
        <RouterView />
      </div>
    </main>
  </div>
</template>
```
- `<div id="app"></div>`にこのコンポーネントが描画される
- `<RouterView />`: URLに対応するHTML部品が入れ替わって描画される

**ページコンポーネントの作成**
- `resources/js/pages/PhotoList.vue`を作成
```js:
<template>
  <h1>Photo List</h1>
</template>
```
- `resources/js/pages/Login.vue`を作成
```js:
<template>
  <h1>Login</h1>
</template>
```

**ルーティング定義**
- `resources/js/router.js`を作成
```js:
import Vue from 'vue'
import VueRouter from 'vue-router'

import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'

// VueRouterプラグインを使用する (<RouterView />を利用可能にする)
Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    component: PhotoList
  },
  {
    path: '/login',
    component: Login
  }
]

const router = new VueRouter({
  routes
})

// app.jsでインポートするので、VueRouterインスタンスをエクスポート
export default router
```
- `app.js`を編集
```js:
import Vue from 'vue'
import router from './router'
import App from './App.vue'

new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App />'
})
```

### APIの実装
- 認証機能は、デフォルトで備えられているものを使用
- APIと画面のルート定義は別々のファイルに記述した方が分かりやすい
- 前回画面を返却するルートを定義した`routes/web.php`ではなく`api.php`に記載する

**RouteServiceProviderを編集する**
- 今回実装する API は内部からしか呼ばれない上にクッキー認証を行うステートフルなものを想定している
- そのため、ミドルウェアグループは画面と同じwebに設定する (ここは分けても良いかもしれない)
  - apiミドルウェアグループでは本来、外部のアプリケーションから呼び出されるようなステートレスなWebAPIが想定されているので、セッションやクッキー、CSRF トークンを扱うミドルウェアが含まれていない
  - つまり、トークンを利用して認証状態をチェックするものでありステートレスであることが前提である
  - ようするに、サーバーサイドでレンダリングして、一部分だけAPI経由でデータを取得したいがために、ログインセッションを使って認証済みならデータを取得するAPIを作ろうとするときが不便
  - LaravelにおいてセッションIDを利用して簡易的にAPI作るやり方として、今回はミドルウェアグループをwebに設定している
- `RouteServiceProvider`を編集して`routes/api.php`に記述したルート定義に適用されるミドルウェアグループは`api`だが、それを`web`に変更
- `RouteServiceProvider`とはアプリケーションの起動時にルート定義を読み込むためのクラス
- ミドルウェアグループの定義は`app/Http/Kernel.php`に記述されている
- `app/Providers/RouteServiceProvider.php`を下記のように編集する
```php:
protected function mapApiRoutes()
{
  Route::prefix('api')
        ->middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/api.php'));
}
```

**テストの準備**
- ここでは、APIの動作を確かめるためにテストコードを準備する
- そのため失敗するテストケースや入力値バリデーションを一つずつ確かめるテストケースなどは記述しない
- またテストには、インメモリのSQLiteを用いる
- インメモリのSQLiteを利用するのでテスト実行が終わると消去され無駄なデータが残らなくてすむ
- `config/database.php`の`connections`に以下の接続情報を追加
```php:
    'connections' => [
        // テスト用
        'sqlite_testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ],
```
- `phpunit.xml`にDB接続の設定を追記
```xml:
    <php>
        <server name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite_testing"/> <!-- テスト用 -->
```

**会員登録APIの作成**
- リクエスト: name,email,password,password_confirmationを受け取る
- レスポンス: 登録ユーザーの情報を返却 (デフォルトの挙動では登録成功後には定義されたページにリダイレクトするレスポンスを返却)
- まずは、`php artisan make:test RegisterApiTest`を実行してテストコードを作成 
- `tests/Feature/RegisterApiTest.php`が作成されるので以下の内容で編集
```php:
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class RegisterApiTest extends TestCase
{
    /**
     * tips
     * RefreshDatabase:
     *   前のテストがその後のテストデータに影響しないように、各テストの後にデータベースをリセット
     *   インメモリデータベースを使っていても、トラディショナルなデータベースを使用していても、
     *   RefreshDatabaseトレイトにより、マイグレーションに最適なアプローチが取れる
     */
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_新規にユーザを作成して返却()
    {
        $data = [
            'name' => 'Yamda Tarou',
            'email' => 'yamada@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->json('POST', route('register'), $data);
        
        $user = User::first();
        $this->assertEquals($data['name'], $user->name);
        
        /**
         *  tips
         *  assertStatus:
         *    クライアントのレスポンスが指定したHTTPステータスコードであることを宣言
         *  assertJson:
         *    レスポンスを配列へ変換
         *    PHPUnit::assertArraySubsetを使用しアプリケーションへ戻ってきたJSONレスポンスの中に、指定された配列が含まれているかを確認
         */
        $response->assertStatus(201)->assertJson(['name' => $user->name]);
    }
}
```
- ※テスト関数には`@test`を付与するようにする
- `$this->json('METHOD', 'URL', [])`でJSONAPIのテストを行うことができる
- `route('register')`は、ルーティングに定義されたURIを吐き出す
- 続いて、`routes/api.php`にルート定義
```php:
<?php

use Illuminate\Http\Request;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/register', 'Auth\RegisterController@register')->name('register');
```
- 続いて、`app/Http/Controllers/Auth/RegisterController.php`を編集
```php:
    /**
     * tips
     * 下記を追加した理由:
     * トレイトであるIlluminate\Foundation\Auth\RegistersUsersを参照
     * registerメソッドの最後のreturn文でregisteredメソッドが呼ばれ、その戻り値が偽値だった場合にはredirect関数が呼ばれる仕組みになっている
     * registered メソッド自体は中身が実装されていない
     * デフォルトではredirect関数が呼ばれるようになっていて、このレスポンスをカスタマイズしたい場合はトレイトを使用している
     * なので下記でregisteredメソッドの中身を実装して上書きしている
     */
    protected function registered(Request $request, $user)
    {
        return $user;
    }
```
- 最後にテストの実施を行う
- テストコマンドは`./vendor/bin/phpunit --testdox`

<details>
<summary>テストでエラーが起きる場合のデバックエラーについて</summary>

- テストをしてみたらエラーが起きた
```sh:
root@1d5a5d91dadb:/var/www/laravel# ./vendor/bin/phpunit --testdox

 ✘ Should 新規にユーザを作成して返却
   ┐
   ├ Expected status code 201 but received 500.
   ├ Failed asserting that false is true.      
   │
   ╵ /var/www/laravel/vendor/laravel/framework/src/Illuminate/Foundation/Testing/TestResponse.php:185
   ╵ /var/www/laravel/tests/Feature/RegisterApiTest.php:40
   ┴

FAILURES!
Tests: 3, Assertions: 4, Failures: 1.
```
- こういった場合は[dd関数を使うと良い](https://readouble.com/laravel/5.5/ja/helpers.html#method-dd)
- 上記の例だと、`Expected status code 201 but received 500`というエラーがあるのでResponse周りが怪しい
- テストファイルでデバックをしてみる

```php:

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_新規にユーザを作成して返却()
    {
        $data = [
            'name' => 'Yamda Tarou',
            'email' => 'yamada@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->json('POST', route('register'), $data);
        dd($response); // 追加
```
```sh:
root@1d5a5d91dadb:/var/www/laravel# ./vendor/bin/phpunit --testdox

    ]
    +exception: Symfony\Component\Debug\Exception\FatalThrowableError^ {#1477
      -originalClassName: "TypeError"
      #message: "Argument 1 passed to App\Http\Controllers\Auth\RegisterController::registered() must be an instance of App\Http\Controllers\Auth\Request, instance of Illuminate\Http\Request given, called in /var/www/laravel/vendor/laravel/framework/src/Illuminate/Foundation/Auth/RegistersUsers.php on line 37"
      #code: 0
      #file: "./app/Http/Controllers/Auth/RegisterController.php"
      #line: 83
      #severity: E_RECOVERABLE_ERROR
      trace: {
        ./app/Http/Controllers/Auth/RegisterController.php:83 {
          App\Http\Controllers\Auth\RegisterController->registered(Request $request, $user)^
          ›  */
          › protected function registered(Request $request, $user)
          › {
```
- 明らかにRegisterController周りでエラーが起きている
- 原因は`Request $request, $user)`の部分のRequestが不明というエラー
- どうやら`use Illuminate\Http\Request;`が抜けていたらしい
- 対処後に再度テストしたらクリアした
```sh:

root@1d5a5d91dadb:/var/www/laravel# ./vendor/bin/phpunit --testdox
PHPUnit 8.5.2 by Sebastian Bergmann and contributors.

Example (Tests\Unit\Example)
 ✔ Basic test

Example (Tests\Feature\Example)
 ✔ Basic test

Register Api (Tests\Feature\RegisterApi)
 ✔ Should 新規にユーザを作成して返却

Time: 3.62 seconds, Memory: 24.00 MB

OK (3 tests, 5 assertions)
```
</details>

**ログインAPIの作成**

- リクエスト: email,passwordを受け取る
- レスポンス: 登録ユーザーの情報を返却 (デフォルトの挙動では認証成功後には定義されたページにリダイレクトするレスポンスを返却)
- まずは、`php artisan make:test LoginApiTest`を実行してテストコードを作成 
- `tests/Feature/LoginApiTest.php`が作成されるので以下の内容で編集
```php:
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class LoginApiTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // テストユーザー作成
        $this->user = factory(User::class)->create();
    }
    
    /**
     * @test
     */
    public function should_登録済ユーザを認証して返す()
    {
        $response = $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200)->assertJson(['name' => $this->user->name]);
        $this->assertAuthenticatedAs($this->user);
    }
}
```

<details>
<summary>上記の解説</summary>

**RefreshDatabase**
- RefreshDatabaseトレイトを使用してデータベースをクリアリング

**setUp**
- このテストクラスの各メソッドの開始前に必ず呼ばれるメソッド
- メソッドの数だけコールされる
- テストの準備としてテストデータを作成している

**factory()**
- テストファイルのsetup()関数に記述された`$this->user = factory(User::class)->create();`について
- これは、[モデルの保存](https://readouble.com/laravel/5.5/ja/database-testing.html)の方法である
- 引数で指定したモデルインスタンスを生成し、Eloquentのsaveメソッドを使用しデータベースへ保存すると同義
- また作成されるデータは`www/laravel/database/factories/UserFactory.php`に定義されている (passwordは'password'のハッシュ値)

**assertAuthenticatedAs**
- 引数で指定したユーザーが認証されているかどうかの判定を行う

</details>

- 続いて、`routes/api.php`にルート定義
```php:
Route::post('/login', 'Auth\LoginController@login')->name('login');
```

- 続いて、`app/Http/Controllers/Auth/LoginController.php`を編集
```php:
    use Illuminate\Http\Request;
  
    /**
     * tips
     * 下記を追加した理由: (会員登録と同様)
     * トレイトであるIlluminate\Foundation\Auth\AuthenticatesUsersを参照
     * loginメソッドでログイン成功時にsendLoginResponseメソッドが呼ばれる
     * sendLoginResponseメソッドのreturn文でレスポンスが決まる
     * return 文では実装が空の authenticated メソッドが呼ばれて、戻り値が偽であった場合にリダイレクトレスポンスが返されてる
     * なので下記でauthenticated メソッドをオーバーライドすればレスポンスをカスタマイズできる
     */
    protected function authenticated(Request $request, $user)
    {
        return $user;
    }
```

- 最後にテストの実施を行う
- テストコマンドは`./vendor/bin/phpunit --testdox`







