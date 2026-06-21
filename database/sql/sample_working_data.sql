-- Pad Sync: working sample data
-- Import this after migrations to restore a usable dataset quickly.

SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE audit_logs;
TRUNCATE TABLE receipt_confirmations;
TRUNCATE TABLE distributions;
TRUNCATE TABLE donations;
TRUNCATE TABLE donors;
TRUNCATE TABLE inventories;
TRUNCATE TABLE shortfall_reports;
TRUNCATE TABLE enrollments;
TRUNCATE TABLE users;
TRUNCATE TABLE schools;

SET FOREIGN_KEY_CHECKS=1;

INSERT INTO schools (school_id, school_name, school_location, enrollment, created_at, updated_at) VALUES
(1, 'Nairobi High School', 'Nairobi, Kenya', 640, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 'Kibera Secondary School', 'Kibera, Nairobi', 480, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 'Mombasa Girls Secondary', 'Mombasa, Kenya', 530, '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO users (id, name, email, email_verified_at, password, username, role, school_id, status, remember_token, created_at, updated_at) VALUES
(1, 'System Admin One', 'admin1@padsync.com', NULL, '$2y$10$cmQQgnWhHhDqRyXGoZMUsOTBTGszNV9gfqiGTY7GiSGq6g8fZ9eqS', 'admin1', 'Admin', NULL, 'Approved', NULL, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 'Program Manager One', 'pm1@padsync.com', NULL, '$2y$10$cmQQgnWhHhDqRyXGoZMUsOTBTGszNV9gfqiGTY7GiSGq6g8fZ9eqS', 'pm1', 'Program Manager', NULL, 'Approved', NULL, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 'Coordinator One', 'coordinator1@padsync.com', NULL, '$2y$10$cmQQgnWhHhDqRyXGoZMUsOTBTGszNV9gfqiGTY7GiSGq6g8fZ9eqS', 'coordinator1', 'Coordinator', 1, 'Approved', NULL, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(4, 'Coordinator Two', 'coordinator2@padsync.com', NULL, '$2y$10$cmQQgnWhHhDqRyXGoZMUsOTBTGszNV9gfqiGTY7GiSGq6g8fZ9eqS', 'coordinator2', 'Coordinator', 2, 'Pending', NULL, '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO enrollments (enrollment_id, school_id, girl_count, government_pads_received, academic_year, month, created_at, updated_at) VALUES
(1, 1, 320, 80, '2026', 'June', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 2, 240, 60, '2026', 'June', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 3, 290, 75, '2026', 'June', '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO shortfall_reports (report_id, school_id, report_date, required_pads, available_pads, government_pads_received, shortfall, status, created_at, updated_at) VALUES
(1, 1, '2026-06-21', 300, 120, 40, 140, 'Submitted', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 2, '2026-06-20', 260, 90, 35, 135, 'Draft', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 3, '2026-06-19', 280, 105, 30, 145, 'Received', '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO inventories (inventory_id, quantity_available, allocated_stock, reorder_level, created_at, updated_at) VALUES
(1, 1000, 150, 100, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 700, 90, 120, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 430, 75, 110, '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO donors (id, name, email, pad_count, donor_type, organization_name, created_at, updated_at) VALUES
(1, 'Jane Doe', 'jane.doe@example.com', 100, 'Individual', NULL, '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 'Rotary Club Nairobi', 'rotary@example.com', 500, 'Organization', 'Rotary Club', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 'Safi Foundation', 'safi.foundation@example.com', 350, 'Organization', 'Safi Foundation', '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO donations (donation_id, donor_id, pad_count, pledge_date, expected_delivery_date, fulfillment_date, notes, created_at, updated_at) VALUES
(1, 1, 200, '2026-06-16', '2026-06-26', NULL, 'Community-led drive', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 2, 450, '2026-06-14', '2026-06-24', NULL, 'Quarterly commitment', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 3, 300, '2026-06-18', '2026-06-28', NULL, 'Emergency top-up', '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO distributions (distribution_id, school_id, quantity_distributed, distribution_date, status, created_at, updated_at) VALUES
(1, 1, 180, '2026-06-21', 'Dispatched', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(2, 2, 220, '2026-06-20', 'Pending', '2026-06-21 09:00:00', '2026-06-21 09:00:00'),
(3, 3, 200, '2026-06-19', 'Received', '2026-06-21 09:00:00', '2026-06-21 09:00:00');

INSERT INTO receipt_confirmations (confirmation_id, distribution_id, coordinator_id, received_quantity, confirmation_date) VALUES
(1, 1, 3, 175, '2026-06-21 10:00:00'),
(2, 2, 4, 210, '2026-06-21 09:00:00'),
(3, 3, 3, 198, '2026-06-21 08:00:00');

INSERT INTO audit_logs (log_id, user_id, user_role, action_performed, ip_address, created_at) VALUES
(1, 1, 'Admin', 'Seeded baseline data', '127.0.0.1', '2026-06-21 10:00:00'),
(2, 2, 'Program Manager', 'Reviewed June donation commitments', '127.0.0.1', '2026-06-21 09:50:00'),
(3, 3, 'Coordinator', 'Confirmed school receipt for dispatched pads', '127.0.0.1', '2026-06-21 09:40:00');
