CREATE TABLE akm_partners(
    id INT AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    inscription DATE,
    revenue INT NOT NULL,
    website VARCHAR(30) NOT NULL,
    id_sponsor INT NULL,
    id_user INT NOT NULL,
    api_token CHAR(30),
    FOREIGN KEY (id_sponsor) REFERENCES akm_partners(id),
    PRIMARY KEY (id)
);

CREATE TABLE akm_sponsor_code(
    id INT AUTO_INCREMENT PRIMARY KEY,
	id_sponsor INT NOT NULL,
    used BOOLEAN NOT NULL,
    code CHAR(10) NOT NULL,
    date_used DATE,
    FOREIGN KEY (id_sponsor) REFERENCES akm_partners(id)
);

CREATE TABLE akm_users(
    id INT AUTO_INCREMENT,
    lastname VARCHAR(30) NOT NULL,
    firstname VARCHAR(30) NOT NULL,
    birthdate DATE NOT NULL,
    phone CHAR(10) NOT NULL,
    email VARCHAR(30) NOT NULL,
    password VARCHAR(200) NOT NULL,
    inscription DATE NOT NULL,
    id_partner INT,
    points INT NOT NULL,
    FOREIGN KEY (id_partner) REFERENCES akm_partners(id),
    PRIMARY KEY (id),
    token CHAR(16),
    token_end DATETIME
);

ALTER TABLE akm_partners ADD FOREIGN KEY (id_user) REFERENCES akm_users(id);

CREATE TABLE akm_prestation(
    id INT AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(2000) NOT NULL,
    price FLOAT NOT NULL,
    id_partner INT NOT NULL,
    stripe_product_id VARCHAR (64) NOT NULL,
    stripe_price_id VARCHAR (64) NOT NULL,
    FOREIGN KEY (id_partner) REFERENCES akm_partners(id),
    PRIMARY KEY (id)
);

CREATE TABLE akm_order(
    id INT AUTO_INCREMENT,
    id_user INT NOT NULL,
    order_time DATETIME,
    cost INT,
    ordered BOOLEAN NOT NULL,
    confirm_code CHAR (32) NOT NULL,
    FOREIGN KEY (id_user) REFERENCES akm_users(id),
    PRIMARY KEY (id)
);

CREATE TABLE akm_cart(
    id_order INT NOT NULL,
    id_prestation INT NOT NULL,

    amount INT,

    FOREIGN KEY (id_order) REFERENCES akm_order(id),
    FOREIGN KEY (id_prestation) REFERENCES akm_prestation(id),

    PRIMARY KEY (id_order, id_prestation)
);