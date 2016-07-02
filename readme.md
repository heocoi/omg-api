## OMG, a.k.a Oh My Guide


### Intro

Project we created when joined SB Cloud Hackathon 20160702-03
* AlibabaCloud ECS
* LAMP
* Laravel5 framework

### Environment

#### Requirements
* CentOS 6.5 (Create new instance from ECS console)
* Apache Apache/2.2.15
* MySQL 5.1.73
* PHP 5.6
* Git 2.2
* [Composer](https://getcomposer.org/) (PHP package manager)

#### DB Settings
* Create new database for this project. (We use "omg" as database for testing)

#### Deploy project
```bash

# dir to workspace
# clone project from Github
git clone -b master https://github.com/heocoi/omg-api.git

cd omg-api

# edit environment config
vi .env
#APP_ENV=local
#APP_DEBUG=true
#APP_KEY=SomeRandomString

#DB_HOST=localhost
#DB_DATABASE=omg
#DB_USERNAME={your username}
#DB_PASSWORD={your password}

#CACHE_DRIVER=file
#SESSION_DRIVER=file
#QUEUE_DRIVER=sync

#MAIL_DRIVER=smtp
#MAIL_HOST=mailtrap.io
#MAIL_PORT=2525
#MAIL_USERNAME=null
#MAIL_PASSWORD=null


# install packages
composer install
php artisan config:cache
php artisan route:cache

# database migration and seed records
php artisan migrate
php artisan db:seed

# move to Apache's document root path
cd /var/www/html
ln -s {project_dir}/public omg

```

#### Testing

* Use Postman or curl
```
POST http://{host_name}/omg/api/login
Headers:
    Content-type: application/json
Request body:
    {"email":"ho@ge.com", "password":"hoge"}

---

Expected response:
    {"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIsImlzcyI6Imh0dHA6XC9cLzQ3Ljg4LjEzNy4xNjVcL29tZ1wvYXBpXC9sb2dpbiIsImlhdCI6MTQ2NzQ1NDE3NiwiZXhwIjoxNDY4NjYzNzc2LCJuYmYiOjE0Njc0NTQxNzYsImp0aSI6ImFiYWY0Y2MzNTRkMTJiNDQ3Y2Q0ODIwNGFlMzFhNWIzIn0.DvkVCRiPB8mYtOod-H2z2lVVapoR-S2guxepsvO1Kvc"}

```

### Team Coffee07
