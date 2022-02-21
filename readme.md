# Senku bot 🤖

Php based telegram bot

## Installation

Open terminal and put this

```bash
git clone https://github.com/Mateodioev/senku
cd senku
composer install
```

Rename `example.htaccess` to `.htaccess` and `example.env` to `.env` after modified `.env` vars

### Cli terminal

```bash
php senku help        # Show commands
php senku server      # Start php dev server on port 8000
php senku share ngrok # Share port 8000 with ngrok
php senku create      # Autocreate .env and .htaccess files with defaul values
```

### Set webhook

```bash
curl 'https://api.telegram.org/bot<BOT_TOKEN>/setwebhook?url=where your hosted the files'
```
