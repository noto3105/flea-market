# coachtechフリマ
## 環境構築
### dockerビルド
git clone git@github.com:/noto3105/flea-market.git  
docker-compose up -d -build  

## laravel環境構築
1 docker-compose exec php bash  
2 composer install  
3 .env.exampleから.envファイルをコピーし、環境変数を変更  
~~~
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
~~~
4 アプリケーションキーの作成  
php artisan key:generate  
5 マイグレーションの実行  
php artisan migrate  
6 シーディングの実行  
php artisan db:seed

## 使用技術
php7.4.9  
laravel8.83.8  
Mysql 8.0.26  

## ER図
![furimaER](https://github.com/user-attachments/assets/74bb422d-ede8-484c-8bf9-8b40eba7e7fe)


## URL
開発環境：http://localhost/ 
phpMyAdmin:http://localhost:8080/ 
