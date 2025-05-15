# Techreizen

## Setup
1) git clone 
2) composer install
3) npm install
4) create database "techreizen"
5) php artisan migrate:refresh --seed
6) [setup Turnstile](#setup-turnstile)
7) [setup mailing](#setup-mailing)
8) composer run-script dev

### Setup Turnstile

1) Go to [cloudflare dashboard](https://dash.cloudflare.com) (create account if you don't have one.)
2) Click on Turnstile in the sidebar.
3) Click on add widget
   - give the widget a name
   - add hostnames that will be used. Example: localhost or 127.0.0.1 or techreizen.test
4) Scroll to the bottom and click on create.
5) Now copy the site key and secret key and put them in your local .env file:
```dotenv
TURNSTILE_SITE_KEY=your_site_key
TURNSTILE_SECRET_KEY=your_secrte_key
```

### Setup mailing

## About techreizen

techreizen is a web application used to manage study trips and their participants
