-- Table: cafe
CREATE TABLE cafe (
    id int  NOT NULL AUTO_INCREMENT,
    name varchar(64)  NOT NULL,
    address varchar(256)  NOT NULL,
    contact_number varchar(16)  NOT NULL,
    CONSTRAINT cafe_pk PRIMARY KEY (id)
);

-- Table: roles
CREATE TABLE roles (
    id int  NOT NULL AUTO_INCREMENT,
    name varchar(16)  NOT NULL,
    CONSTRAINT roles_pk PRIMARY KEY (id)
);

-- Table: types
CREATE TABLE types (
    id int  NOT NULL AUTO_INCREMENT,
    name varchar(16) NOT NULL,
    CONSTRAINT types_pk PRIMARY KEY (id)
);

-- Table: users
CREATE TABLE users (
    id int  NOT NULL AUTO_INCREMENT,
    role_id int  NOT NULL,
    username varchar(64)  NOT NULL,
    password varchar(256)  NOT NULL,
    email varchar(64)  NOT NULL,
    created_at datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT users_pk PRIMARY KEY (id),
    CONSTRAINT users_roles_fk FOREIGN KEY (role_id)
        REFERENCES roles (id)
);

-- Table: products
CREATE TABLE products (
    id int  NOT NULL AUTO_INCREMENT,
    cafe_id int  NOT NULL,
    type_id int NOT NULL,
    name varchar(64)  NOT NULL,
    description varchar(256)  NOT NULL,
    image varchar(256)  NOT NULL,
    price int  NOT NULL,
    status ENUM('available', 'unavailable') 
        NOT NULL DEFAULT 'available',
    CONSTRAINT products_pk PRIMARY KEY (id),
    CONSTRAINT products_cafe_fk FOREIGN KEY (cafe_id)
        REFERENCES cafe (id),
    CONSTRAINT products_types_fk FOREIGN KEY (type_id)
        REFERENCES types (id)
);

-- Table: reviews
CREATE TABLE reviews (
    id INT NOT NULL AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment VARCHAR(255),
    CONSTRAINT reviews_pk PRIMARY KEY (id),
    CONSTRAINT reviews_products_fk FOREIGN KEY (product_id) 
        REFERENCES products (id),
    CONSTRAINT reviews_users_fk FOREIGN KEY (user_id) 
        REFERENCES users (id)
);

-- Table: carts
CREATE TABLE carts (
    user_id int  NOT NULL,
    product_id int  NOT NULL,
    quantity int  NOT NULL,
    CONSTRAINT carts_pk PRIMARY KEY (user_id,product_id),
    CONSTRAINT carts_products_fk FOREIGN KEY (product_id)
        REFERENCES products (id) ON DELETE CASCADE,
    CONSTRAINT carts_users_fk FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
);

-- Table: orders
CREATE TABLE orders (
    id int  NOT NULL AUTO_INCREMENT,
    order_code VARCHAR(30) NOT NULL UNIQUE,
    user_id int  NOT NULL,
    order_date datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    customer_name VARCHAR(100) NOT NULL,
    cashier_id INT NULL,
    subtotal INT NOT NULL,
    discount INT NOT NULL DEFAULT 0,
    total INT NOT NULL,
    status ENUM('pending', 'paid', 'done') NOT NULL DEFAULT 'pending',
    CONSTRAINT orders_pk PRIMARY KEY (id),
    CONSTRAINT orders_users_fk FOREIGN KEY (user_id)
        REFERENCES users (id),
    CONSTRAINT orders_cashier_fk FOREIGN KEY (cashier_id)
        REFERENCES users (id)
);

-- Table: order_details
CREATE TABLE order_details (
    id int  NOT NULL AUTO_INCREMENT,
    order_id int  NOT NULL,
    product_id int  NOT NULL,
    quantity int  NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    unit_price INT NOT NULL,
    CONSTRAINT order_details_pk PRIMARY KEY (id),
    CONSTRAINT order_details_orders_fk FOREIGN KEY (order_id)
        REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT order_details_products_fk FOREIGN KEY (product_id)
        REFERENCES products (id)
);