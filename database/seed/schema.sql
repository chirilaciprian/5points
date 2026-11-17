-- Product Management System: database schema.
-- Usage: mysql -u <user> -p < database/seed/schema.sql

CREATE DATABASE IF NOT EXISTS 5points
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE 5points;

CREATE TABLE IF NOT EXISTS products (
    id                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name              VARCHAR(255)  NOT NULL,
    description       TEXT          NULL,
    price             DECIMAL(10,2) NOT NULL,
    image             VARCHAR(255)  NULL,
    availability_date DATE          NULL,
    in_stock          TINYINT(1)    NOT NULL DEFAULT 0,
    created_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT chk_products_price CHECK (price >= 0)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
