-- First
-- CREATE DATABASE url_shortener;
-- Then run this query
CREATE TABLE urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_url VARCHAR(255) NOT NULL,
    short_code VARCHAR(10) NOT NULL UNIQUE
);
