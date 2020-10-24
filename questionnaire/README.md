# アンケートフォーム

## 目的

- PHP で DB 操作する
- PHP で CSV ダウンロードする
- HTML フォームの基本的なタグを使用する（例：input, select）

## 機能概要
- アンケートに答える
- アンケートの内容を一覧で表示する
- 一覧に表示された内容をダウンロードする
- HTMLはレスポンシブ対応

## 画面遷移

<img src='diagram.png' alt='画面遷移図' width='50%' />

## 環境

環境構築手順は [PROCEDURE.md](PROCEDURE.md) を参照 。  

- Mac 10.13.6（ホストOS）
- Virtual Box 5.2.6
- CentOS 7.8.2003（ゲストOS）
- Apache HTTP Server 2.4.6
- PHP 7.4.11
  - PDO
- MariaDB 10.5.6
- JavaScript
  - jQuery
  - jQuery Validation Plugin
  - riversun / sortable-table
- CSS
  - bootstrap

## テーブル構成

### shops

| 物理名      | データ型          | 主キー | NOT NULL | 備考 |
| ---------- | ---------------- | ----- | ---------| ---- |
| id         | int(10) unsigned | YES   | YES      | AUTO INCREMENT |
| name       | varchar(50)      | -     | YES      | UNIQUE |
| is_enabled | boolean          | -     | YES      | |
| created_at | timestamp        | -     | YES      | デフォルトで現在日時を設定 |
| uodated_at | timestamp        | -     | YES      | デフォルトで現在日時を設定 |

### questionnaires

| 物理名      | データ型             | 主キー | NOT NULL | 備考 |
| ---------- | ------------------- | ----- | ---------| ---- |
| id         | int(10) unsigned    | YES   | YES      | AUTO INCREMENT |
| shop_id    | int(10) unsigned    | -     | YES      | 外部キー：shops.id |
| item       | varchar(50)         | -     | YES      | |
| flavour    | tinyint(1) unsigned | -     | YES      | 1: 悪い、3: 普通、5: 良い |
| opinion    | varchar(500)        | -     | -        | |
| created_at | timestamp           | -     | YES      | デフォルトで現在日時を設定 |
| uodated_at | timestamp           | -     | YES      | デフォルトで現在日時を設定 |

## 作業中によく使ったコマンド

### エラーログの出力と参照

PHPコード  
```
error_log("ログです。");
```

エラーログ参照
```
$ sudo tail -f /var/log/httpd/error_log
```

## お世話になったサイト

- [404エラーのページをPHPのheader関数でリダイレクトするのは間違っている](https://dev-lib.com/php-header-404-redirect/)
- [【JavaScript】【jquery】jquery.validate.jsの基本的な使い方](https://yu-ya4.hatenablog.com/entry/2015/07/04/130627)
- [HTMLのtableをソートする方法](https://qiita.com/riversun/items/8c59353af4f16264aedd)
- [ExcelでBOM付きが無双するPHPでのCSVの書き出し方](https://alaki.co.jp/blog/?p=1260)
- [【jQuery】配列をCSVファイルにして、ダウンロードする方法](https://qiita.com/tsukahara-akito/items/976690c099f8f3316a7d)

### そのほか

構文チェック
- Visual Studio Code に以下の拡張機能をインストール
  - W3C Validation（HTML/CSS 構文チェック）
  - ESLint（JavaScript 構文チェック）
  - PHP IntelliSense（PHP 構文チェック）
