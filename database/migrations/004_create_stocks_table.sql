CREATE TABLE stocks (
    stock_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    seller_id INT UNSIGNED NOT NULL,

    category_id INT UNSIGNED NOT NULL,

    stock_name VARCHAR(150) NOT NULL,

    description TEXT,

    quantity_total INT UNSIGNED NOT NULL,

    quantity_available INT UNSIGNED NOT NULL,

    price_per_share DECIMAL(12,2) NOT NULL,

    image_path VARCHAR(255),

    status ENUM(
        'active',
        'sold_out',
        'archived'
    ) DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_stock_seller
        FOREIGN KEY (seller_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_stock_category
        FOREIGN KEY (category_id)
        REFERENCES stock_categories(category_id)
        ON DELETE RESTRICT
);