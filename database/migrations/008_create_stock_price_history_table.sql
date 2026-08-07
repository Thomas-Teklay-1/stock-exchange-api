CREATE TABLE stock_price_history (
    price_history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    stock_id INT UNSIGNED NOT NULL,

    old_price DECIMAL(12,2) NOT NULL,

    new_price DECIMAL(12,2) NOT NULL,

    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_price_history_stock
        FOREIGN KEY (stock_id)
        REFERENCES stocks(stock_id)
        ON DELETE CASCADE
);