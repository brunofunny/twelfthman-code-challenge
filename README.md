# Twelfthman - Developer Test

### Challenge

All details at https://twelfthman.co/interview-tests/developers/

### Tech used

- **PHP 7.x**
- **Laravel**
- **Angular**
- **SASS**

### Requirements

- **GIT**
- **PHP 7.x**
- **Composer**
- **Angular-Cli**
- **Node/NPM**

### Setup

Clone this repo:
```bash
    git clone https://github.com/brunofunny/twelfthman-code-challenge.git
```

### Backend

1. Go to directory:
```bash
    cd twelfthman-code-challenge/backend/
```
2. Install the requirements
```bash
    composer install
``` 
3. Copy the file .env-example to .env (And configure with your local/host database settings, usually just host/username/password)
```bash
    cp .env.example .env
```
4. Generate application key
```bash
    php artisan key:generate
```
5. You must install the database tables now, run the line below
```bash
    php artisan migrate:install; php artisan migrate
```
5. Then now we populate the database with some data (this file (load.zip) also contains some images that are suppose to return errors (exceed size limit))
```bash
    php artisan import:image load.zip
```
5. Run the server using the default params
```bash
    php artisan serve
```
6. Access this url in the browser:

    http://127.0.0.1:8000/

### Frontend

1. Go to directory:
```bash
    cd ../frontend-ng/
```
2. Install the requirements
```bash
    npm install
```
5. Run the server using the default params
```bash
    ng serve --env=dev
```
6. Access this url in the browser:

    http://127.0.0.1:4200/