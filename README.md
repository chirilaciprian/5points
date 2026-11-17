# Product Management System

A CRUD application for managing products, built with plain PHP, MySQL and vanilla
JavaScript.

## Requirements

- PHP 8.1+ with the `pdo_mysql`, `mbstring` and `fileinfo` extensions
- MySQL 8+
- Composer (only used for PSR-4 autoloading, there are no dependencies)

## Setup

1. Generate the autoloader:

   ```bash
   composer install
   ```

2. Create the database and load the 10 sample products:

   ```bash
   mysql -u root -p < database/seed/schema.sql
   mysql -u root -p < database/seed/seed.sql
   ```

   On Ubuntu, where the MySQL `root` user logs in through the socket, use `sudo mysql < database/seed/schema.sql`.

3. Point the application at the database. `config.php` defaults to the `5points` database on
   `127.0.0.1:3306` with the user `dev` / password `dev`; either edit it or set the environment variables
   it reads: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

   A dedicated user is recommended (and required when `root` only accepts socket logins):

   ```sql
   CREATE USER 'dev'@'localhost' IDENTIFIED BY 'dev';
   GRANT SELECT, INSERT, UPDATE, DELETE ON `5points`.* TO 'dev'@'localhost';
   ```

4. Start the application:

   ```bash
   php -S localhost:8000 -t public
   ```

## Image uploads

Product images are saved in `public/uploads/products/` (created on the first upload), and the file name is
stored in the `image` column. The web server user needs write access to `public/uploads/`. The size limit
is `uploads.max_size` in `config.php` (2 MB); PHP's `upload_max_filesize` and `post_max_size` must be at
least as large. Replacing or removing an image, or deleting its product, deletes the file from disk.

## Endpoints

| Method | URI         | Purpose                                                              |
|--------|-------------|----------------------------------------------------------------------|
| GET    | `/`         | Product list page (`?search=` and `?page=`)                          |
| GET    | `/products` | Same list as JSON (rendered table + pagination) for AJAX refreshes   |
| GET    | `/product`  | One product as JSON (`?id=`), used to fill the edit form             |
| POST   | `/products` | Create a product                                                     |
| PATCH  | `/product`  | Update a product (sent as POST with `_method=PATCH`)                 |
| DELETE | `/product`  | Delete a product (sent as POST with `_method=DELETE`)                |

Listing, searching, paginating, creating, updating and deleting all happen through AJAX; the page never
reloads. Validation errors come back as `422` with a message per field.

## Security

- **SQL injection**: every query goes through `Core\Database::query()`, which uses PDO prepared statements
  with emulation turned off and typed bound values. No request data is ever concatenated into SQL.
- **Validation**: `Http\Forms\ProductForm` validates and normalises all input on the server
  (required name, numeric price with at most 2 decimals, real calendar date, boolean stock flag,
  valid image type and size).
- **File uploads**: `Core\UploadedImage` accepts only JPG, PNG, GIF and WEBP images of at most 2 MB. The type
  is detected from the file contents (`finfo` + `getimagesize`), never from the name or type sent by the
  browser, and files are stored under a random name with an extension chosen by the server.
  `public/uploads/.htaccess` turns off script execution in the uploads directory.
- **XSS**: all output is escaped with `e()` (`htmlspecialchars`); the JavaScript only writes user data
  through `textContent`.
- **CSRF**: state-changing routes require the session token (`X-CSRF-Token` header), checked by the
  `csrf` middleware.


