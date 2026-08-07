CREATE TABLE portfolio_holdings (
    holding_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    buyer_id INT UNSIGNED NOT NULL,

    stock_id INT UNSIGNED NOT NULL,

    shares_owned INT UNSIGNED NOT NULL,

    average_buy_price DECIMAL(12,2) NOT NULL,

    total_investment DECIMAL(14,2) NOT NULL,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE (buyer_id, stock_id),

    CONSTRAINT fk_holding_buyer
        FOREIGN KEY (buyer_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_holding_stock
        FOREIGN KEY (stock_id)
        REFERENCES stocks(stock_id)
        ON DELETE CASCADE
);