-- Table: cafe
CREATE TABLE cafe (
    id int  NOT NULL,
    name varchar(64)  NOT NULL,
    address varchar(256)  NOT NULL,
    contact_number varchar(16)  NOT NULL,
    CONSTRAINT cafe_pk PRIMARY KEY (id)
);

-- Table: roles
CREATE TABLE roles (
    id int  NOT NULL,
    name varchar(16)  NOT NULL,
    CONSTRAINT roles_pk PRIMARY KEY (id)
);

-- Table: types
CREATE TABLE types (
    id int NOT NULL,
    name varchar(16) NOT NULL,
    CONSTRAINT types_pk PRIMARY KEY (id)
);

-- Table: users
CREATE TABLE users (
    id int  NOT NULL,
    role_id int  NOT NULL,
    username varchar(64)  NOT NULL,
    password varchar(256)  NOT NULL,
    email varchar(64)  NOT NULL,
    created_at datetime  NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (id),
    CONSTRAINT users_roles_fk FOREIGN KEY (role_id)
        REFERENCES roles (id)
);

-- Table: products
CREATE TABLE products (
    id int  NOT NULL,
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
    id int  NOT NULL,
    user_id int  NOT NULL,
    order_date datetime  NOT NULL,
    total_items int  NOT NULL,
    total_price int  NOT NULL,
    status ENUM('processing', 'delayed', 'ready', 'cancelled', 'completed') 
        NOT NULL DEFAULT 'processing',
    CONSTRAINT orders_pk PRIMARY KEY (id),
    CONSTRAINT orders_users_fk FOREIGN KEY (user_id)
        REFERENCES users (id)
);

-- Table: order_details
CREATE TABLE order_details (
    id int  NOT NULL,
    order_id int  NOT NULL,
    product_id int  NOT NULL,
    quantity int  NOT NULL,
    items_price int  NOT NULL,
    CONSTRAINT order_details_pk PRIMARY KEY (id),
    CONSTRAINT order_details_orders_fk FOREIGN KEY (order_id)
        REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT order_details_products_fk FOREIGN KEY (product_id)
        REFERENCES products (id)
);