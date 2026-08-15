-- MyUmrahGo authentication/RBAC expansion.
-- Run after the base schema. Existing users.role remains backward compatible.
CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(120) NOT NULL,
  description VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  permission_key VARCHAR(120) NOT NULL UNIQUE,
  label VARCHAR(160) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, role_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO roles (name,label,description) VALUES
('super_admin','Super Admin','Full platform authority'),
('admin','Admin','Operational administration'),
('agent','Agency Owner','Full agency workspace access'),
('sales','Sales Staff','Packages, quotations and CRM'),
('booking','Booking Staff','Booking requests and documents'),
('accounts','Accounts Staff','Invoices, payments and ledgers'),
('marketing','Marketing Staff','Branding and marketing studio'),
('readonly','Read Only','View-only permitted records')
ON DUPLICATE KEY UPDATE label=VALUES(label), description=VALUES(description);

INSERT INTO permissions (permission_key,label) VALUES
('agents.view','View agencies'),('agents.manage','Manage agencies'),('agents.approve','Approve agencies'),
('inventory.flights','Manage flights'),('inventory.hotels','Manage hotels'),('inventory.visa','Manage visa products'),
('inventory.transport','Manage transport'),('inventory.ziyarat','Manage ziyarat'),
('packages.manage','Manage packages'),('bookings.process','Process bookings'),
('payments.verify','Verify payments'),('ledger.manage','Manage ledgers'),
('documents.manage','Manage documents'),('notifications.publish','Publish notifications'),
('templates.manage','Manage templates'),('reports.export','Export reports'),
('audit.view','View audit logs'),('settings.manage','Manage system settings')
ON DUPLICATE KEY UPDATE label=VALUES(label);
