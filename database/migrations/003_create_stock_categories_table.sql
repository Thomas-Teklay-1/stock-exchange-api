CREATE TABLE stock_categories (
    category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    category_name VARCHAR(100)
        NOT NULL
        UNIQUE
);