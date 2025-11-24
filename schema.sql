-- Crie o banco e a tabela
CREATE DATABASE IF NOT EXISTS produtos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE produtos_db;
DROP database produtos_db;

-- 1. Tabela de Clientes
CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  phone VARCHAR(50) DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabela de Produtos (com estoque e status)
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT 'Geral',
  price DECIMAL(10,2) DEFAULT 0.00,
  stock INT DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabela de Vendas (Registro de Transações)
CREATE TABLE IF NOT EXISTS sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(255) DEFAULT 'Cliente Anônimo',
  total_amount DECIMAL(10,2) NOT NULL,
  sale_details TEXT, -- Salva o JSON ou string dos itens vendidos
  sale_date DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserindo alguns dados iniciais para teste
INSERT INTO products (name, category, price, stock, is_active) VALUES
('Caneta Azul', 'Papelaria', 2.50, 50, TRUE),
('Caderno Universitário', 'Papelaria', 15.00, 20, TRUE),
('Mouse Sem Fio', 'Eletrônicos', 45.90, 15, TRUE);

INSERT INTO clients (name, phone) VALUES
('Aquiles Silva', '1199887766'),
('Bruna Santos', '2198765432');

SELECT * FROM clients;