CREATE TABLE addresses (
    address_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    country VARCHAR(100) DEFAULT 'Ethiopia',

    region VARCHAR(100) NOT NULL,

    city VARCHAR(100) NOT NULL,

    subcity VARCHAR(100),

    woreda VARCHAR(100),

    kebele VARCHAR(100),

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);