# 勤怠管理システム
ユーザーごとに勤怠の登録が出来、月日ごとにユーザーの勤怠一覧を閲覧することが可能になっています

# 作成した目的
従業員増加にあたりこれまでの勤怠管理システムでは管理体制が不十分であるため、勤怠実績一覧もみることができる新しい勤怠管理システムを作成することになった

# デプロイのURL

# 他のリポジトリ

# 機能一覧
ログイン機能
出勤と退勤は1日に1回のみの打刻制限
月日を検索出来る勤怠実績一覧表
休憩開始を押すと休憩終了以外は押せない

# テーブル設計
![image](https://github.com/Chayney/attendance/assets/158685403/ab5f8bd1-796a-4b54-a6b1-1f6e57f15431)


# ER図
![テーブルのリレーション図](https://github.com/Chayney/attendance/assets/158685403/08e6a1f4-9c36-4c1f-85d5-cc0383ec488d)

# 環境構築

## Dockerビルド
1. git clone https://github.com/Chayney/contactform
2. docker-compose up -d -build

## Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、環境変数を変更
4. php artisan key:generate
5. php artisan migrate
6. php artisan db:seed

## 使用技術
1. PHP 8.3.6
2. Laravel v8.83.8
3. mysql:8.0.26
