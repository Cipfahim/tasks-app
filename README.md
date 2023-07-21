## Prerequisites

- Make sure you have PHP installed on your system. You can download it from the [official PHP website](https://www.php.net/downloads.php).
- Install Composer, the PHP package manager. You can find installation instructions on the [Composer website](https://getcomposer.org/download/).
- Install a database server like MySQL or PostgreSQL, and create a new database for your Laravel application.
- Set up a mail server or use a service like [Mailtrap](https://mailtrap.io/) for testing email functionality.

## 1. Clone or Download The Project

```bash
git clone https://github.com/Cipfahim/tasks-app.git
cd tasks-app
```


## 2. Environment Configuration

Rename the .env.example file in the root directory to .env and configure the necessary environment variables, including database and mail settings. Update the following lines in the .env file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=your_smtp_port
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email_address
MAIL_FROM_NAME="${APP_NAME}"
```

## 3. Install Dependencies

Open a terminal or command prompt, navigate to your project directory, and run the following command to install the required PHP dependencies defined in composer.json:

```bash
composer install
```

## 4. Migrate Database And Seed dummy data

Run the database migrations to create the necessary tables in your database:

```bash
php artisan migrate --seed
```

## 5. Start Local Development Server

Start the development server by running the following command:

```bash
php artisan serve
```

## 6. Access Application

Application should now be running locally. Open your web browser and go to http://localhost:8000 (or the URL shown in the terminal after running php artisan serve) to access your application.

## 7. Run queue server

```bash
php artisan queue:work
```
