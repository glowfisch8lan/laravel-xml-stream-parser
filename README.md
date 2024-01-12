<h1>Laravel XML stream parser</h1>
<h2>Stream download xml from url, save, then parse and convert to structure CSV</h2>

<h2>Installation</h2>
In the root directory
0. Specify Gateway Port in the .env file
1. docker-compose up -d
   In the container:
2. cd app;
3. php composer.phar install

<h2>Launch:</h2>
In the container in the app folder:
````shell
php artisan app:fetch-products {--uuid= : UUID xml file to parse} {--url= : URL to xml file}
````