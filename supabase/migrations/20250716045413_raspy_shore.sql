-- ฐานข้อมูลระบบรายงานการทำงานแผนกไอที
-- สำหรับ XAMPP MySQL

CREATE DATABASE IF NOT EXISTS it_work_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE it_work_system;

-- ตารางเจ้าหน้าที่ไอที
CREATE TABLE IF NOT EXISTS it_staff (
    staff_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    hire_date DATE,
    status ENUM('ปฏิบัติงาน', 'ลาป่วย', 'ลาพักร้อน', 'ไม่อยู่') DEFAULT 'ปฏิบัติงาน',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ตารางประเภทงาน
CREATE TABLE IF NOT EXISTS task_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    color_code VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตารางงาน/โครงการ
CREATE TABLE IF NOT EXISTS tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    task_title VARCHAR(200) NOT NULL,
    task_description TEXT,
    category_id INT,
    assigned_to INT,
    priority ENUM('สูงมาก', 'สูง', 'ปานกลาง', 'ต่ำ') DEFAULT 'ปานกลาง',
    status ENUM('รอดำเนินการ', 'กำลังดำเนินการ', 'เสร็จสิ้น', 'มีปัญหา', 'ยกเลิก') DEFAULT 'รอดำเนินการ',
    start_date DATE,
    due_date DATE,
    completion_date DATE,
    estimated_hours DECIMAL(5,2),
    actual_hours DECIMAL(5,2),
    progress_percentage INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES task_categories(category_id),
    FOREIGN KEY (assigned_to) REFERENCES it_staff(staff_id),
    FOREIGN KEY (created_by) REFERENCES it_staff(staff_id)
);

-- ตารางรายงานการทำงานรายวัน
CREATE TABLE IF NOT EXISTS daily_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    report_date DATE NOT NULL,
    work_summary TEXT,
    tasks_completed TEXT,
    tasks_in_progress TEXT,
    problems_encountered TEXT,
    solutions_applied TEXT,
    next_day_plan TEXT,
    working_hours DECIMAL(4,2),
    overtime_hours DECIMAL(4,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES it_staff(staff_id),
    UNIQUE KEY unique_staff_date (staff_id, report_date)
);

-- ตารางปัญหาและการแก้ไข
CREATE TABLE IF NOT EXISTS issues (
    issue_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    issue_title VARCHAR(200) NOT NULL,
    issue_description TEXT,
    severity ENUM('วิกฤต', 'สูง', 'ปานกลาง', 'ต่ำ') DEFAULT 'ปานกลาง',
    reported_by INT,
    assigned_to INT,
    status ENUM('เปิด', 'กำลังแก้ไข', 'รอทดสอบ', 'แก้ไขแล้ว', 'ปิด') DEFAULT 'เปิด',
    resolution TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (reported_by) REFERENCES it_staff(staff_id),
    FOREIGN KEY (assigned_to) REFERENCES it_staff(staff_id)
);

-- ข้อมูลตัวอย่างเจ้าหน้าที่ไอที
INSERT INTO it_staff (staff_name, position, email, phone, hire_date, status) VALUES
('นายสมชาย เทคโนโลยี', 'หัวหน้าแผนกไอที', 'somchai.tech@company.com', '081-234-5678', '2020-01-15', 'ปฏิบัติงาน'),
('นางสาวสุดา โปรแกรม', 'นักพัฒนาระบบ', 'suda.program@company.com', '082-345-6789', '2021-03-10', 'ปฏิบัติงาน'),
('นายวิชัย เน็ตเวิร์ค', 'ผู้ดูแลระบบเครือข่าย', 'wichai.network@company.com', '083-456-7890', '2021-06-20', 'ปฏิบัติงาน');

-- ข้อมูลตัวอย่างประเภทงาน
INSERT INTO task_categories (category_name, description, color_code) VALUES
('พัฒนาระบบ', 'งานพัฒนาและปรับปรุงระบบซอฟต์แวร์', '#28a745'),
('บำรุงรักษา', 'งานบำรุงรักษาระบบและอุปกรณ์', '#ffc107'),
('แก้ไขปัญหา', 'งานแก้ไขปัญหาระบบและซอฟต์แวร์', '#dc3545'),
('ติดตั้งระบบ', 'งานติดตั้งระบบและอุปกรณ์ใหม่', '#17a2b8'),
('ฝึกอบรม', 'งานฝึกอบรมและถ่ายทอดความรู้', '#6f42c1'),
('ประชุม', 'งานประชุมและประสานงาน', '#fd7e14');

-- ข้อมูลตัวอย่างงาน
INSERT INTO tasks (task_title, task_description, category_id, assigned_to, priority, status, start_date, due_date, estimated_hours, progress_percentage, created_by) VALUES
('พัฒนาระบบจัดการสินค้าคลัง', 'พัฒนาระบบจัดการสินค้าคลังแบบ Real-time', 1, 2, 'สูงมาก', 'กำลังดำเนินการ', '2025-01-10', '2025-02-28', 120.00, 65, 1),
('อัพเกรดเซิร์ฟเวอร์หลัก', 'อัพเกรดเซิร์ฟเวอร์หลักและติดตั้ง OS ใหม่', 2, 3, 'สูง', 'กำลังดำเนินการ', '2025-01-15', '2025-01-25', 40.00, 30, 1),
('แก้ไขปัญหาระบบ Email', 'แก้ไขปัญหาระบบ Email ที่ส่งช้า', 3, 1, 'สูง', 'เสร็จสิ้น', '2025-01-12', '2025-01-14', 8.00, 100, 1),
('ติดตั้งระบบรักษาความปลอดภัย', 'ติดตั้งและตั้งค่าระบบ Firewall ใหม่', 4, 3, 'สูงมาก', 'รอดำเนินการ', '2025-01-20', '2025-01-30', 24.00, 0, 1),
('ฝึกอบรมการใช้ระบบใหม่', 'ฝึกอบรมพนักงานใช้ระบบจัดการสินค้าคลัง', 5, 2, 'ปานกลาง', 'รอดำเนินการ', '2025-03-01', '2025-03-05', 16.00, 0, 1);

-- ข้อมูลตัวอย่างรายงานรายวัน
INSERT INTO daily_reports (staff_id, report_date, work_summary, tasks_completed, tasks_in_progress, working_hours) VALUES
(1, '2025-01-15', 'ประชุมวางแผนโครงการและตรวจสอบความคืบหน้า', 'ตรวจสอบและอนุมัติแผนการพัฒนาระบบ', 'กำกับดูแลโครงการพัฒนาระบบจัดการสินค้าคลัง', 8.00),
(2, '2025-01-15', 'พัฒนาโมดูลการจัดการสินค้าเข้า-ออก', 'เขียนโค้ดโมดูลการรับสินค้าเข้าคลัง', 'ทำโมดูลการจ่ายสินค้าออกจากคลัง', 8.50),
(3, '2025-01-15', 'เตรียมการอัพเกรดเซิร์ฟเวอร์', 'สำรองข้อมูลเซิร์ฟเวอร์เดิม', 'วางแผนการอัพเกรดและทดสอบระบบ', 8.00);

-- ข้อมูลตัวอย่างปัญหา
INSERT INTO issues (task_id, issue_title, issue_description, severity, reported_by, assigned_to, status) VALUES
(1, 'ระบบช้าในช่วงเวลาเร่งด่วน', 'ระบบทำงานช้าเมื่อมีผู้ใช้งานพร้อมกันมากกว่า 50 คน', 'สูง', 2, 2, 'กำลังแก้ไข'),
(2, 'ไม่สามารถเชื่อมต่อ Remote ได้', 'ไม่สามารถเชื่อมต่อ Remote Desktop ไปยังเซิร์ฟเวอร์ได้', 'ปานกลาง', 3, 3, 'เปิด');