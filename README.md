#contact-form-app

COACHTECH お問い合わせフォーム

### 概要

## 目的

今までやってきた勉強がどこまで自分のものにできているか、
逆にどこができていないのか、自分の力がどれほどか理解するため。

---

## お問い合わせ管理システム（機能仕様

本システムは、ユーザーからのお問い合わせ受付（フロントエンド）から、管理者によるお問い合わせデータの管理・運用（バックエンド）までを一元管理できるWebアプリケーションです。

# 1. ユーザー側機能（お問い合わせフロー）

ユーザーがお問い合わせを安全かつスムーズに行うための一連の画面と機能です。

**お問い合わせ入力機能**
氏名（姓・名）、性別（ラジオボタン）、メールアドレス、電話番号、住所、建物名、お問い合わせの種類（プルダウン）、タグ（チェックボックス）、お問い合わせ内容を入力・選択する機能。
入力内容のバリデーションチェックをクリア後、確認画面へ遷移。
**入力内容確認機能**
送信前にユーザーが入力した内容に誤りがないか最終確認する画面。
送信」ボタンにより、データの送信ができます。
必須項目が空欄の場合はエラーメッセージが出力されるようになっています
**完了（サンクス）ページ表示機能**
送信が正常に完了したことをユーザーに通知する画面。
「HOME」ボタンを配置し、トップページへのスムーズな遷移を誘導。

# 2. 管理者認証機能

管理画面のセキュリティを担保し、不正なアクセスを防ぐための認証システムです。

**管理者アカウント登録機能**
新規の管理者アカウント（お名前、メールアドレス、パスワード、確認用パスワード）を発行・登録する機能。
必須項目が空欄の場合はエラーメッセージが出力されるようになっています
**\*ログイン・認証機能**
登録済みのメールアドレスとパスワードを照合し、管理画面へのアクセス権限を付与する機能。セッション管理によるセキュリティ対策を保持。
必須項目が空欄の場合はエラーメッセージが出力されるようになっています

# 3. 管理画面・データ運用機能

受信したお問い合わせを適切に処理・分析・管理するためのバックオフィス機能です。

**お問い合わせ一覧表示機能**
受信したすべてのお問い合わせをリスト形式（お名前、性別、メールアドレス、お問い合わせの種類、タグ）で一元管理する画面。
**詳細検索・絞り込み機能**
お名前やメールアドレスのキーワード検索に加え、性別、お問い合わせの種類、日付（年月日）による複合的なデータの抽出・絞り込み機能。
**CSVエクスポート機能**
蓄積されたお問い合わせデータをCSVファイル形式で一括ダウンロードし、外部ツールでの集計や分析を可能にする機能。
**詳細閲覧・削除機能**
各お問い合わせの全入力内容（住所や詳細な本文など）を個別ページで閲覧する機能、および不要なデータをシステム内から削除する機能。
**タグ管理・編集機能**
お問い合わせの分類に使用する「タグ（質問、要望、不具合報告など）」を新しく追加・登録したり、既存のタグ名を変更・更新・削除する機能。

---

## ER図

本プロジェクトのデータベース構造です。

mermaid
erDiagram
users {
bigint id
string name
string email
timestamp email_verified_at
string password
string remember_token
timestamp created_at
timestamp updated_at
}

    categories {
        bigint id
        string name
        timestamp created_at
        timestamp updated_at
    }

    contacts {
        bigint id
        string first_name
        string last_name
        integer gender
        string email
        string tel1
        string tel2
        string tel3
        string address
        string building
        bigint category_id
        text body
        timestamp created_at
        timestamp updated_at
    }

    tags {
        bigint id
        string name
        timestamp created_at
        timestamp updated_at
    }

    contact_tags {
        bigint id
        bigint contact_id
        bigint tag_id
        timestamp created_at
        timestamp updated_at
    }

    categories ||--o{ contacts : "1つのカテゴリは複数のお問い合わせを持つ (hasMany)"
    contacts ||--o{ contact_tags : "1つのお問い合わせは複数のタグを持つ (belongsToMany)"
    tags ||--o{ contact_tags : "1つのタグは複数のお問い合わせに紐づく (belongsToMany)"

````

### 💡 各テーブルのリレーション関係

- **`categories` と `contacts` (1対多 / `hasMany`)**: 1つのカテゴリーに、紐づく複数のお問い合わせが紐付きます。
- **`contacts` と `tags` (多対多 / `belongsToMany`)**: 中間テーブル（`contact_tags`）を介して、1つのお問い合わせに対して複数のタグを紐付けることができます。

---

# 環境構築手順書

本プロジェクトの開発環境を構築するための手順です。
以下の手順を上から順番に実行してください。

## 前提条件

事前に以下のツールがローカルPCにインストールされ、起動していることを確認してください。

- **Docker Desktop** （※必ず起動しておいてください）
- **Git**

---

## 1. プロジェクトのクローンと移動

まずは、GitHubからリポジトリをご自身のPCにクローン（ダウンロード）し、プロジェクトのディレクトリに移動します。ターミナルを開き、以下のコマンドを実行してください。

```bash
# リポジトリのクローン
git clone <ここにあなたのご自身のGitHubリポジトリURLを貼り付けてください>

# プロジェクトディレクトリへの移動
cd contact-form-app
````

---

## 2.Laravel Sail（Docker環境）のインストール

Dockerコンテナ内でLaravelを動かすためのパッケージをインストールします。

```bash
# 依存パッケージのインストール
docker run --rm \
    -u "(id -u):(id -g)" \
    -v "\$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

# Sailの設定ファイルをパブリッシュ（※選択肢が出たら「MySQL」を選択してください）
php artisan sail:install
```

---

## 3. .env ファイルの設定

データベースや環境変数を設定するための設定ファイルを作成します。

```bash
# 設定ファイルの複製
cp .env.example .env
```

`.env` ファイルを開き、データベースの接続情報が以下の通りになっているか確認・修正してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

**注意**: `DB_HOST` は `localhost` や `127.0.0.1` ではなく、Dockerコンテナ名である **`mysql`** を指定してください。

---

## 4. Sailの起動とエイリアスの設定

### ① バックグラウンドでコンテナを起動

```bash
./vendor/bin/sail up -d
```

### ② エイリアス（ショートカット）の設定

毎回 `./vendor/bin/sail` と打つのは大変なため、`sail` だけでコマンドが打てるように設定します。

**【Mac / Linux (bash/zsh) の場合】**

```bash
echo 'alias sail="[ -f sail ] && bash sail || bash vendor/bin/sail"' >> ~/.zshrc
source ~/.zshrc
```

**【Windows (Git Bash) の場合】**

```bash
echo 'alias sail="./vendor/bin/sail"' >> ~/.bashrc
source ~/.bashrc
```

---

## 5. アプリケーションキーの生成

Laravelのセキュリティ暗号化に必要なキーを生成します。

```bash
sail artisan key:generate
```

---

## 6. フロントエンドのセットアップ (Vite & Tailwind CSS)

見た目（スタイリング）を適用するためのパッケージをインストールし、設定を行います。

```bash
# NPM依存パッケージのインストール
sail npm install

# Tailwind CSS と必要なプラグインのインストール
sail npm install -D tailwindcss@^3.4.0 postcss autoprefixer
sail npm install alpinejs

# 設定ファイルの生成
sail npx tailwindcss init -p
```

### `tailwind.config.js` の書き換え

生成された `tailwind.config.js` を開き、`content` の部分を以下のように修正します。

```javascript
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
```

---

## 7. テンプレート（Blade）の入れ替え

プロジェクトで提供されている共通のデザインやレイアウト用ファイルを配置します。

```bash
# 既存の resources ディレクトリを削除
rm -rf resources

# 提供リポジトリから resources ディレクトリをクローン
git clone https://github.com resources

# クローンした一時フォルダ内の不要な Git 設定を削除
rm -rf resources/.git
```

---

## 8.Vite 開発サーバーの起動

CSSやJavaScriptの変更をリアルタイムに画面に反映させるためのサーバーを起動します。

```bash
sail npm run dev
```

**重要**: このコマンドを実行したターミナルは、**開発中はずっと起動したまま（閉じない状態）**にしておいてください。新しくコマンドを打つ際は、ターミナルの「＋」ボタンなどから新しいタブを開いて実行してください。

## 9. データベースのマイグレーションと初期データ投入

新しく開いたターミナルのタブで、以下のコマンドを実行し、テーブルの作成とテストデータの投入を行います。

```bash
# データベースのテーブル作成 ＆ 初期データ（シーダー）の投入
sail artisan migrate:fresh --seed
```

---

## 10. 開発・管理ツールのURL一覧

環境構築がすべて完了すると、ブラウザから以下のURLにアクセスできるようになります。

**アプリケーション（トップページ）**：[http://localhost](http://localhost)
**phpMyAdmin（データベース管理画面）**：[http://localhost:8080](http://localhost:8080)

---

## 使用技術（Tech Stack）

本システムの構築に使用している主要な技術、ライブラリ、およびツールの一覧です。

### 1. フロントエンド（Frontend）

**HTML5 / CSS3**（画面レイアウト・装飾の構築）

### 2. バックエンド（Backend）

**PHP 8.2**
*Laravel 10.50.4\*\* （Webアプリケーションフレームワーク）
*Laravel Sail**（Docker開発環境の構築・管理）
\*Eloquent ORM**（お問い合わせ・管理者・タグデータのデータベース操作）
_Blade_（テンプレートエンジンによるビューの制御）

### 3. データベース（Database）

**MySQL 8.4**（ユーザーデータ、管理者アカウント、タグデータの保存）
_Migrations_: データベースのバージョン管理およびテーブル構造の定義
\*Seeders / Factories\*\*: テスト用ダミーデータ（お問い合わせ一覧など）の自動生成

### 4. 開発環境・ツール（Tools & Infrastructure）

**Docker / Docker Compose**（コンテナを用いた一貫した開発環境の構築）
*Composer\*\*（PHPパッケージ・依存関係の管理）
*Git / GitHub\*\*（ソースコードのバージョン管理）

---

## APIエンドポイント一覧

本システムで実装されている、外部連携・SPA用の公開APIエンドポイントの一覧です。

### 📂 お問い合わせ管理 API (AP01)

| HTTPメソッド | エンドポイント (URI) | 説明 | 認証 | 認可 |
| `GET` | `/api/v1/contacts` | お問い合わせ一覧（検索・ページネーション付き）を取得する。 | 不要 | 不要 |
| `GET` | `/api/v1/contacts/{contact}` | お問い合わせ詳細を取得する。（カテゴリ・タグ情報を含む） | 不要 | 不要 |
| `POST` | `/api/v1/contacts` | お問い合わせの新規作成・登録を行う。 | 不要 | 不要 |
| `PUT` | `/api/v1/contacts/{contact}` | 指定したお問い合わせ情報を更新する。 | 不要 | 不要 |
| `DELETE` | `/api/v1/contacts/{contact}` | 指定したお問い合わせデータを削除する。 | 不要 | 不要 |

---

## 🌐 開発環境URL一覧

ローカル開発環境（Docker / Laravel Sail）の起動中にアクセス可能なURL一覧です。

| サービス名                         | アクセスURL                                    | 概要                                                                          |
| :--------------------------------- | :--------------------------------------------- | :---------------------------------------------------------------------------- |
| **phpMyAdmin（データベース管理）** | [http://localhost:8080](http://localhost:8080) | データベース（MySQL）のデータやテーブル構造をブラウザから確認・管理できます。 |

|お問い合わせ入力フォーム｜[http://localhost]｜お問い合わせの作成できます。|
|お問い合わせフォーム確認|[http://localhost/contacts/confirm]|お問い合わせが間違いないか確認できます。|
|サンクスページ|[http://localhost/thanks]|お問い合わせ送信後「お問い合わせありがとうございました」が表示されます。|
|管理者登録画面|[http://localhost/register]|管理者を登録できます。（登録後管理者画面に移行します。|
|管理者ログイン画面|[http://localhost/login]|登録されている管理者はここからログインできます。|
|管理画面|[http://localhost/admim]|どんなお問い合わせが来たか確認できます。タグの追加や削除ができます。|
|お問い合わせ詳細画面|[http://localhost/admin/contacts/{$id}]|お問い合わせの詳細が見れます。削除もできます。|
|タグ編集ページ|[http://localhost/admin/tags/{$id}/edit]|既存のタグを編集できます。|

---

## 作成者

相川　旺輝
