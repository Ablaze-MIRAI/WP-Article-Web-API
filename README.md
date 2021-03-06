# WP Article Web API
これはWordPressの最新記事情報の取得を簡易化するためのAPIです。Webサイト等に表示する際などに簡単に実装することができます。

つくったひと→ [@code-raisan](https://github.com/code-raisan)

## How to use
Ablazeでホストしているものをお使いいただくこともできますが、自鯖でホストする事で少しオプションを設定することができます。その時々の条件に合わせてご使用ください。

[共通仕様](#共通仕様)

[Ablazeでホストされているものを使用](#Ablazeでホストされているものを使用する)

[自分でホスト](#自分でホストする)

## 共通仕様
> Ablogではもっと嚙み砕いて書いていますので難しい方は是非ご覧ください。(近日公開予定)

> APIエンドポイントや自鯖でのホストについては [Ablazeでホストされているものを使用](#Ablazeでホストされているものを使用する)や[自分でホスト](#自分でホストする)をご覧ください。

ご使用のWordPressサイトのURLが必要となります。`home` パラメーターにWordPressのトップページのURLを指定してください。このパラメーターは必須になります。

例
```
[API ROOT]/?home=https://blog.example.com/
```

基本的には上記のものだけで動作します。しかし、NEWSカテゴリなどだけをWebサイト等に埋め込みたいなどのカテゴリ別で絞り込みたい場合には `categories` パラメーターに **カテゴリID** を指定してください。また複数指定する場合は `+` で繋いでください。

> **カテゴリID**はカテゴリ名とは別のものです

例
```
[API ROOT]/?home=https://blog.example.com/&categories=10+12
```

レスポンス例([Ablog](https://blog.ablaze.one)より)
```
{
  "code": 200,
  "categories": false,
  "items": [
    {
      "title": "おすすめの Firefox アドオンのご紹介！タブ管理から、プライバシーまで",
      "date": "2022-03-18T21:12:13",
      "author": "すらーぷの妖精",
      "link": "https://blog.ablaze.one/?p=1437",
      "description": "どうも、こんにちは！すらーぷの妖精です！今回は、Twitter でリクエストをいただいたので、おすすめで便利なアドオンを紹介します。ランキングはつけません。 ",
      "image": "https://blog.ablaze.one/wp-content/uploads/2022/02/ABlog.png"
    },
    {
      "title": "libadwaitaに対するAlexandriteOSの考え",
      "date": "2022-03-12T12:45:41",
      "author": "nexryai",
      "link": "https://blog.ablaze.one/?p=1406",
      "description": "注意: この考えはあくまでAlexandriteOSの開発者の考えであり、Ablazeを代表するものでは一切ありません Gnome42で本格的にlibadw ",
      "image": "https://blog.ablaze.one/wp-content/uploads/2022/03/Screenshot-from-2022-03-12-12-44-27.png"
    }
  ]
}
```

### カテゴリIDの取得方法
このAPIには `DEV OPTION` というページがあり、そこで `カテゴリID` と `カテゴリ名` の対応表を見ることができます。カテゴリ表は `dev` パラメーターを付与したURLにWebブラウザからアクセスする事で見ることができます。

※このとき `home` パラメーターが設定されている状態である必要があります。

例
```
[API ROOT]/?home=https://blog.example.com/&dev
```

## Ablazeでホストされているものを使用する
```
https://wpapi.ablaze.one/
```
上記のURLがAPIエンドポイントです。このURLを[共通仕様](#共通仕様)の `[API ROOT]` に置き換えることで直ぐにご使用いただけます。

## 自分でホストする
このAPIはOSSなのでこのリポジトリをクローンして自分でホストすることが可能です。

### デプロイの仕方
動作環境

- PHP 7.4以降
- cURL拡張

まずこのリポジトリをクローンやダウンロードしてサーバーに展開します。`api` フォルダにスクリプトが格納されているので、`api` フォルダの中身を任意の場所に展開する事でデプロイできます。

### 各種設定オプションについて
オプションを変更する場合は `api/index.php` の上部のパラメーターを変更する事で変更できます。初期設定は下記のようになっています。

```php
<?php

//========================CONFIG========================
$WP_HOME_URL = null;
$ALLOW_ORIGIN = "*";
$DEV_OPTION = false;
//======================================================

```

- `$WP_HOME_URL`

これは `home` **クエリ**パラメーターを省略することのできるオプションです。[共通仕様](#共通仕様)の `home` **クエリ**パラメーターを全て省略することができます。

オプションの記述例
```php
$WP_HOME_URL = "https://blog.example.com/";
//こちらも同様にWordPressのトップページのURLを記述してください。
```

アクセス時のパラメーター例
```
[API ROOT]/?categories=10+12
```

- `$ALLOW_ORIGIN`

これはCORS用のヘッダーの出力設定です。初期設定では `*` になっておりどのオリジンからの接続でも受け付けるように設定されていますが、自鯖で使う場合は他からのアクセスを防ぐためにオリジンを制限することをお勧めします。

オプションの記述例
```
$ALLOW_ORIGIN = "example.com about.example.com";
```

- `$DEV_OPTION`

これは[カテゴリIDの取得方法](#カテゴリIDの取得方法)で使われる `DEV OPTION`　を有効、無効を設定できます。常に使うものではなく、カテゴリ一覧を見るだけならAblazeでホストされている物を使えばいいので初期設定では `false` (無効)に設定されいます。もし有効化する場合は `true` に変更してください。

> 本番環境では不必要な機能なため基本的には無効にすることをお勧めします。

有効化する場合の記述例
```
$DEV_OPTION = true;
```

## LICENSE
ライセンスはMIT Licenseで公開しています。
