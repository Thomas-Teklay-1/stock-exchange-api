CREATE TABLE transactions (
    transaction_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    buyer_id INT UNSIGNED NOT NULL,

    seller_id INT UNSIGNED NOT NULL,

    stock_id INT UNSIGNED NOT NULL,

    transaction_type ENUM('buy','sell') NOT NULL,

    shares INT UNSIGNED NOT NULL,

    price_per_share DECIMAL(12,2) NOT NULL,

    total_amount DECIMAL(14,2) NOT NULL,

    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transaction_buyer
        FOREIGN KEY (buyer_id)
        REFERENCES users(user_id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_transaction_seller
        FOREIGN KEY (seller_id)
        REFERENCES users(user_id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_transaction_stock
        FOREIGN KEY (stock_id)
        REFERENCES stocks(stock_id)
        ON DELETE RESTRICT
);