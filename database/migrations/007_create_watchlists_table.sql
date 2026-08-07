CREATE TABLE watchlists (
    watch_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    buyer_id INT UNSIGNED NOT NULL,

    stock_id INT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (buyer_id, stock_id),

    CONSTRAINT fk_watchlist_buyer
        FOREIGN KEY (buyer_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_watchlist_stock
        FOREIGN KEY (stock_id)
        REFERENCES stocks(stock_id)
        ON DELETE CASCADE
);