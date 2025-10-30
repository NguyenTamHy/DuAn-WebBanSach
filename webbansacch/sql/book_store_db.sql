CREATE DATABASE IF NOT EXISTS book_store_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE book_store_db;

-- Bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('User','Staff','Admin') DEFAULT 'User',
    address VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng thể loại
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Bảng sách
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100),
    publisher VARCHAR(100),
    price DECIMAL(10,2),
    stock INT DEFAULT 0,
    category_id INT,
    cover_image VARCHAR(255),
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Bảng đơn hàng
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    payment_method VARCHAR(50),
    status ENUM('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Chi tiết đơn hàng
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    book_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Bảng đánh giá
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Dữ liệu mẫu
INSERT INTO categories (name, description) VALUES
('Tiểu thuyết', 'Các tác phẩm tiểu thuyết Việt Nam và nước ngoài'),
('Khoa học', 'Sách khoa học và khám phá'),
('Kinh tế', 'Sách về tài chính, quản trị, kinh doanh');

INSERT INTO books (title, author, publisher, price, stock, category_id, cover_image, description)
VALUES
('Tuổi trẻ đáng giá bao nhiêu', 'Rosie Nguyễn', 'NXB Trẻ', 89000, 10, 1, 'cover1.jpg', 'Cuốn sách truyền cảm hứng sống cho giới trẻ.'),
('Sapiens: Lược sử loài người', 'Yuval Noah Harari', 'NXB Thế giới', 199000, 5, 2, 'cover2.jpg', 'Khám phá lịch sử tiến hóa của loài người.'),
('Cha giàu cha nghèo', 'Robert Kiyosaki', 'NXB Trẻ', 150000, 8, 3, 'cover3.jpg', 'Bí quyết tài chính cá nhân nổi tiếng.');
