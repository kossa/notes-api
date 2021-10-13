# Notes api using Laravel and TDD
A simple CRUD api to manage Notes by logged users with fresh laravel installation(No extra packages)


### Postman documentation
https://documenter.getpostman.com/view/256183/UV5TEJrK


### Demo
Can be found: https://notes-api.bel4.com/api

> Username: hadjikouceyla@gmail.com

> Password: password


### Installation on local?
```sh
git clone https://github.com/kossa/notes-api.git

cd notes-api

composer install # Install backend dependencies

sudo chmod 777 storage/ -R # Chmod Storage

cp .env.example .env # Update database credentials configuration

php artisan key:generate # Generate new key for Laravel

php artisan migrate:fresh --seed # Run migration and seed users and some notes for testing

php artisan test # Optional: Run tests
```

### Check github action(For tests)
https://github.com/kossa/notes-api/actions
