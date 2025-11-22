-- Database schema V2 - Multi Department/TV System


USE wcb;

-- Bảng bộ phận/departments
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Mã bộ phận: FO, RESTAURANT, CHRYSAN, LOTUS, JASMIN',
    name VARCHAR(100) NOT NULL COMMENT 'Tên bộ phận',
    description TEXT COMMENT 'Mô tả',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng TV/màn hình
CREATE TABLE IF NOT EXISTS tv_screens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Mã TV: FO_TV1, FO_TV2, RESTAURANT_TV1, etc.',
    name VARCHAR(100) NOT NULL COMMENT 'Tên TV',
    location VARCHAR(255) COMMENT 'Vị trí đặt TV',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_department (department_id),
    INDEX idx_code (code),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng welcome boards (cập nhật)
CREATE TABLE IF NOT EXISTS welcome_boards (
    id VARCHAR(50) PRIMARY KEY,
    event_date DATE NOT NULL,
    event_title VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    upload_time DATETIME NOT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_event_date (event_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng phân bổ board cho TV (many-to-many)
CREATE TABLE IF NOT EXISTS board_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id VARCHAR(50) NOT NULL,
    tv_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_assignment (board_id, tv_id),
    INDEX idx_board (board_id),
    INDEX idx_tv (tv_id),
    INDEX idx_status (status),
    
    FOREIGN KEY (board_id) REFERENCES welcome_boards(id) ON DELETE CASCADE,
    FOREIGN KEY (tv_id) REFERENCES tv_screens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert departments
INSERT INTO departments (code, name, description) VALUES
('BASEMENT', 'Tầng hầm', 'Tầng hầm - 1 TV (tối đa 3 WCB)'),
('FO', 'Front Office', 'Bộ phận lễ tân - 2 TV'),
('RESTAURANT', 'Nhà hàng', 'Bộ phận nhà hàng - 1 TV'),
('CHRYSAN', 'Chrysan', 'Phòng Chrysan - 1 TV'),
('LOTUS', 'Lotus', 'Phòng Lotus - 1 TV'),
('JASMIN', 'Jasmin', 'Phòng Jasmin - 1 TV');

-- Insert TV screens (7 TVs total)
INSERT INTO tv_screens (department_id, code, name, location) VALUES
-- Basement: 1 TV (max 3 WCB - đặc biệt)
(1, 'BASEMENT_TV1', 'Tầng hầm - TV 1', 'Tầng hầm'),
-- FO: 2 TVs (max 1 WCB each)
(2, 'FO_TV1', 'FO - TV 1', 'Lễ tân chính'),
(2, 'FO_TV2', 'FO - TV 2', 'Lễ tân phụ'),
-- Restaurant: 1 TV (max 1 WCB)
(3, 'RESTAURANT_TV1', 'Nhà hàng - TV 1', 'Khu vực nhà hàng'),
-- Chrysan: 1 TV (max 1 WCB)
(4, 'CHRYSAN_TV1', 'Chrysan - TV 1', 'Phòng Chrysan'),
-- Lotus: 1 TV (max 1 WCB)
(5, 'LOTUS_TV1', 'Lotus - TV 1', 'Phòng Lotus'),
-- Jasmin: 1 TV (max 1 WCB)
(6, 'JASMIN_TV1', 'Jasmin - TV 1', 'Phòng Jasmin');

-- View để xem thống kê
CREATE OR REPLACE VIEW stats_overview AS
SELECT 
    COUNT(DISTINCT wb.id) as total_boards,
    COUNT(DISTINCT CASE WHEN ba.status = 'active' THEN ba.id END) as active_assignments,
    COUNT(DISTINCT tv.id) as total_tvs,
    COUNT(DISTINCT CASE WHEN tv.status = 'active' THEN tv.id END) as active_tvs,
    COUNT(DISTINCT d.id) as total_departments
FROM welcome_boards wb
LEFT JOIN board_assignments ba ON wb.id = ba.board_id
LEFT JOIN tv_screens tv ON ba.tv_id = tv.id
LEFT JOIN departments d ON tv.department_id = d.id;

-- View để xem board assignments
CREATE OR REPLACE VIEW board_assignments_view AS
SELECT 
    wb.id as board_id,
    wb.event_title,
    wb.event_date,
    wb.filepath,
    d.code as department_code,
    d.name as department_name,
    tv.code as tv_code,
    tv.name as tv_name,
    ba.status as assignment_status,
    ba.assigned_at
FROM welcome_boards wb
JOIN board_assignments ba ON wb.id = ba.board_id
JOIN tv_screens tv ON ba.tv_id = tv.id
JOIN departments d ON tv.department_id = d.id
ORDER BY ba.assigned_at DESC;

SELECT 'Database V2 created successfully!' as message;
SELECT * FROM departments;
SELECT * FROM tv_screens;
