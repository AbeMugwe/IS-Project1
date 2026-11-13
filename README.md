# IS Project 1 — PHP Student Portal

Apache/PHP/MariaDB lab project with signup, login and course-feedback form.

## Structure
- `public/`: Apache document root (the only publicly served files)
- `src/bootstrap.php`: PDO database connections, sessions, form helpers
- `schema.sql`: CAT1 users and CAT2 feedback tables
- `config.example.php`: sample configuration; copy to `config.php` (ignored by Git)
- `.github/workflows/serverless-demo.yml`: GitHub Actions ephemeral compute example

## Installation (Debian/Ubuntu)
1. Install `apache2 mariadb-server php libapache2-mod-php php-mysql git`.
2. `sudo mariadb < schema.sql` (or run SQL from MariaDB shell).
3. Create an application DB user with privileges on CAT1 and CAT2 (see task guide).
4. Copy `config.example.php` to `config.php` and configure the database password.
5. Configure Apache with `DocumentRoot /var/www/is-project1/public`, enable the site and reload Apache.
6. Open `/signup.php`, `/login.php`, and `/feedback.php` on the VM's IP.

Passwords are stored using PHP password_hash; SQL queries use prepared statements.
For a production application, add HTTPS, more extensive authorization and logging.
