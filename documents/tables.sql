
-- Tabela de usuários

CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    created_at DATETIME,
    updated_at DATETIME
);

-- Tabela de produtos

CREATE TABLE products (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    cost_price DECIMAL(10,2),
    retail_price DECIMAL(10,2) NOT NULL,
    composite BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME,
    deleted_at DATETIME
);

-- Tabela auxiliar para Produtos Compostos referência de N:N para a tabela de produtos

CREATE TABLE composite_products (
    composite_id INT NOT NULL,
    simple_id INT NOT NULL,
    amount INT NOT NULL,
    CONSTRAINT fk_composite_product FOREIGN KEY (composite_id) REFERENCES products(id),
    CONSTRAINT fk_simple_product FOREIGN KEY (simple_id) REFERENCES products(id)
);

CREATE TABLE requisitions(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    amount INT(11) NOT NULL,
    requisition_date DATETIME NOT NULL,
    is_exit BOOLEAN,   --falso para entrada verdadeiro para saída
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_product FOREIGN KEY (product_id) REFERENCES products(id)

);

-- Tabela para Estoque

CREATE TABLE stocks (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    amount INT(11) NOT NULL,
    CONSTRAINT fk_product_amount FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Adicionando ACL para a tabela de usuário

ALTER TABLE users ADD COLUMN level ENUM('admin', 'user') NOT NULL DEFAULT 'user';


/*
Trigger para caso houver entrada de produto adiciona no estoque,
se tiver saída, subtrai e também se o estoque for igual a 0, dispara
uma mensagem de erro, dizendo que o estoque está insuficiente

*/

CREATE TRIGGER after_requisition_insert
AFTER INSERT ON requisitions
FOR EACH ROW
BEGIN
    DECLARE stock_amount INT;
    DECLARE insufficient_stock INT DEFAULT 0;
    -- Verifica se a requisição é de entrada
    IF NEW.is_exit = 0 THEN
        -- verifica se o produto é composto
        IF EXISTS (SELECT 1 FROM composite_products WHERE composite_id = NEW.product_id) THEN
            -- atualiza o estoque de cada componente simples
            UPDATE stocks s
            JOIN composite_products cp ON s.product_id = cp.simple_id
            SET s.amount = s.amount + NEW.amount * cp.amount
            WHERE cp.composite_id = NEW.product_id;

            -- insere novo registro caso não exista ainda
            INSERT INTO stocks (product_id, amount)
            SELECT cp.simple_id, NEW.amount * cp.amount
            FROM composite_products cp
            LEFT JOIN stocks s ON cp.simple_id = s.product_id
            WHERE cp.composite_id = NEW.product_id
            AND s.product_id IS NULL;
        ELSE
            
            IF EXISTS (SELECT 1 FROM stocks WHERE product_id = NEW.product_id) THEN
                UPDATE stocks
                SET amount = amount + NEW.amount
                WHERE product_id = NEW.product_id;
            ELSE
                INSERT INTO stocks (product_id, amount)
                VALUES (NEW.product_id, NEW.amount);
            END IF;
        END IF;
    ELSE
       -- atualiza o estoque caso o produto seja produto simples
        IF EXISTS (SELECT 1 FROM composite_products WHERE composite_id = NEW.product_id) THEN
           
            SELECT COUNT(*)
            INTO insufficient_stock
            FROM composite_products cp
            LEFT JOIN stocks s ON cp.simple_id = s.product_id
            WHERE cp.composite_id = NEW.product_id
            AND (s.amount IS NULL OR s.amount < NEW.amount * cp.amount);
      
            IF insufficient_stock > 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estoque insuficiente para um ou mais componentes do produto composto.';
            ELSE         
                UPDATE stocks s
                JOIN composite_products cp ON s.product_id = cp.simple_id
                SET s.amount = s.amount - NEW.amount * cp.amount
                WHERE cp.composite_id = NEW.product_id;
            END IF;
        ELSE      
            SELECT amount INTO stock_amount FROM stocks WHERE product_id = NEW.product_id;
            IF stock_amount IS NULL OR stock_amount < NEW.amount THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estoque insuficiente para este produto.';
            ELSE
                UPDATE stocks
                SET amount = amount - NEW.amount
                WHERE product_id = NEW.product_id;
            END IF;
        END IF;
    END IF;
END;

