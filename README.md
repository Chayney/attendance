# Atte
「Atte」は勤怠管理アプリです。会員登録することで日々の勤怠を登録することが出来ます。日付別の勤怠を閲覧することが出来、またユーザーごとの勤怠一覧を閲覧することが可能になっています。

# 作成した目的
Laravel学習のまとめとして作成いたしました。提示された要件や成果物のイメージをもとに設計・コーディングを行いました。

# 機能一覧
## 会員登録機能→名前、メールアドレス、パスワード、確認用パスワードが入力項目となっています
## ログイン機能→メールアドレスとパスワードで認証出来、ログアウト機能もついています(fortifyを使用して設定しています)
## 勤怠の打刻機能

# 機能に関する注意点
出勤と退勤は1日に1回のみの打刻制限
休憩開始を押すと休憩終了以外は押せない

# テーブル設計
![image](https://github.com/Chayney/attendance/assets/158685403/ab5f8bd1-796a-4b54-a6b1-1f6e57f15431)


# ER図
![テーブルのリレーション図](https://github.com/Chayney/attendance/assets/158685403/08e6a1f4-9c36-4c1f-85d5-cc0383ec488d)

# 環境構築

## Dockerビルド
1. docker-compose up -d -build

## Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .envで環境変数を変更
4. php artisan key:generate
5. php artisan migrate

## 使用技術
1. PHP 7.4.9
2. Laravel v8.83.8
3. mysql:8.0.26
