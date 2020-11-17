php artisan down
composer install
php artisan migrate --force
npm install

php artisan ziggy:generate resources/js/ziggy.js
npm run prod

php artisan horizon:terminate
php artisan up
