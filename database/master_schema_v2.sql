-- MyUmrahGo Master Schema v2
-- Derived from the Master Product Roadmap PDF.
-- Credentials/secrets never belong in this file.

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_key VARCHAR(60) NOT NULL DEFAULT 'customer',
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) UNIQUE NULL,
  phone VARCHAR(40) NULL,
  password_hash VARCHAR(255) NULL,
  status ENUM('pending','under_review','active','rejected','suspended','closed') NOT NULL DEFAULT 'pending',
  last_login_at DATETIME NULL,
  session_revoked_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role_status(role_key,status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_key VARCHAR(80) UNIQUE NOT NULL,
  role_name VARCHAR(120) NOT NULL,
  scope_type ENUM('system','agency') NOT NULL DEFAULT 'system',
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  permission_key VARCHAR(120) UNIQUE NOT NULL,
  description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY(user_id,role_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  access_level ENUM('view','edit','full') NOT NULL DEFAULT 'view',
  PRIMARY KEY(role_id,permission_id),
  FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE,
  FOREIGN KEY(permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS agencies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(190) NOT NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  address TEXT NULL,
  registration_no VARCHAR(120) NULL,
  license_no VARCHAR(120) NULL,
  status ENUM('pending','under_review','active','rejected','suspended','closed') NOT NULL DEFAULT 'pending',
  pricing_profile VARCHAR(80) NULL,
  booking_limit DECIMAL(14,2) NULL,
  transaction_limit DECIMAL(14,2) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(owner_user_id) REFERENCES users(id),
  INDEX idx_agencies_status(status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS agency_staff (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  staff_role VARCHAR(80) NOT NULL,
  status ENUM('invited','active','disabled') NOT NULL DEFAULT 'invited',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_agency_staff(agency_id,user_id),
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS agency_branding (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED UNIQUE NOT NULL,
  logo_path VARCHAR(500) NULL,
  primary_color VARCHAR(20) NULL,
  secondary_color VARCHAR(20) NULL,
  tagline VARCHAR(255) NULL,
  whatsapp VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  website VARCHAR(255) NULL,
  social_links JSON NULL,
  bank_details JSON NULL,
  terms_text TEXT NULL,
  co_branding_mode ENUM('platform','agency','hybrid') NOT NULL DEFAULT 'hybrid',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  mobile VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  nationality VARCHAR(100) NULL,
  country VARCHAR(100) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  INDEX idx_customers_agency(agency_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS passengers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id BIGINT UNSIGNED NOT NULL,
  full_name VARCHAR(160) NOT NULL,
  passport_no VARCHAR(80) NULL,
  passport_expiry DATE NULL,
  dob DATE NULL,
  nationality VARCHAR(100) NULL,
  passport_country VARCHAR(100) NULL,
  restricted_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS documents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NULL,
  customer_id BIGINT UNSIGNED NULL,
  passenger_id BIGINT UNSIGNED NULL,
  booking_request_id BIGINT UNSIGNED NULL,
  document_type VARCHAR(80) NOT NULL,
  storage_path VARCHAR(500) NOT NULL,
  original_name VARCHAR(255) NULL,
  mime_type VARCHAR(120) NULL,
  expires_at DATE NULL,
  visibility ENUM('private','agency','admin') NOT NULL DEFAULT 'private',
  status ENUM('active','archived','replaced') NOT NULL DEFAULT 'active',
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE SET NULL,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY(passenger_id) REFERENCES passengers(id) ON DELETE SET NULL,
  FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_documents_agency_type(agency_id,document_type)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS airlines (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  code VARCHAR(20) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS flights (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  airline_id BIGINT UNSIGNED NOT NULL,
  flight_no VARCHAR(30) NOT NULL,
  direction ENUM('outbound','return') NOT NULL,
  origin VARCHAR(8) NOT NULL,
  destination VARCHAR(8) NOT NULL,
  departure_at DATETIME NOT NULL,
  arrival_at DATETIME NOT NULL,
  baggage_kg INT UNSIGNED NULL,
  meal_included TINYINT(1) NOT NULL DEFAULT 0,
  fare DECIMAL(14,2) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  published_at DATETIME NULL,
  FOREIGN KEY(airline_id) REFERENCES airlines(id),
  INDEX idx_flights_dates(departure_at,active)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS flight_blocks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  flight_id BIGINT UNSIGNED NOT NULL,
  block_name VARCHAR(190) NOT NULL,
  total_seats INT UNSIGNED NOT NULL,
  available_seats INT UNSIGNED NOT NULL,
  block_fare DECIMAL(14,2) NULL,
  valid_from DATE NULL,
  valid_to DATE NULL,
  status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
  FOREIGN KEY(flight_id) REFERENCES flights(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS visa_products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  visa_type VARCHAR(80) NOT NULL,
  brn_option VARCHAR(80) NULL,
  transport_option VARCHAR(80) NULL,
  validity_days INT UNSIGNED NULL,
  minimum_pax INT UNSIGNED NULL,
  net_rate DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'SAR',
  status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hotels (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  city ENUM('makkah','madinah') NOT NULL,
  name VARCHAR(190) NOT NULL,
  distance_m INT UNSIGNED NULL,
  stars TINYINT UNSIGNED NULL,
  description TEXT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  published_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hotel_rates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hotel_id BIGINT UNSIGNED NOT NULL,
  room_type ENUM('sharing','quint','quad','triple','double','single') NOT NULL,
  occupancy INT UNSIGNED NULL,
  sar_per_night DECIMAL(14,2) NOT NULL,
  valid_from DATE NULL,
  valid_to DATE NULL,
  availability_count INT UNSIGNED NULL,
  FOREIGN KEY(hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
  INDEX idx_hotel_rates_validity(hotel_id,valid_from,valid_to)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transport_services (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  route_from VARCHAR(120) NULL,
  route_to VARCHAR(120) NULL,
  vehicle_type VARCHAR(100) NULL,
  capacity INT UNSIGNED NULL,
  rate DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'SAR',
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ziyarat_services (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  city ENUM('makkah','madinah') NOT NULL,
  name VARCHAR(190) NOT NULL,
  description TEXT NULL,
  rate DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'SAR',
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS packages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  customer_id BIGINT UNSIGNED NULL,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL,
  duration_nights INT UNSIGNED NOT NULL,
  passenger_count INT UNSIGNED NOT NULL DEFAULT 1,
  net_price DECIMAL(14,2) NULL,
  margin_amount DECIMAL(14,2) NULL,
  final_price DECIMAL(14,2) NULL,
  currency CHAR(3) NOT NULL DEFAULT 'PKR',
  status ENUM('draft','ready','quotation','expired','cancelled','converted') NOT NULL DEFAULT 'draft',
  autosave_step TINYINT UNSIGNED NOT NULL DEFAULT 1,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_package_agency_slug(agency_id,slug),
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY(created_by) REFERENCES users(id),
  INDEX idx_packages_status(agency_id,status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS package_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  package_id BIGINT UNSIGNED NOT NULL,
  item_type ENUM('flight','visa','makkah_hotel','madinah_hotel','transport','ziyarat') NOT NULL,
  reference_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(190) NULL,
  quantity DECIMAL(12,2) NOT NULL DEFAULT 1,
  nights INT UNSIGNED NULL,
  unit_cost DECIMAL(14,2) NOT NULL,
  selling_cost DECIMAL(14,2) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY(package_id) REFERENCES packages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS quotations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  package_id BIGINT UNSIGNED NOT NULL,
  customer_id BIGINT UNSIGNED NULL,
  quotation_no VARCHAR(60) UNIQUE NOT NULL,
  template_id BIGINT UNSIGNED NULL,
  validity_until DATETIME NULL,
  version_no INT UNSIGNED NOT NULL DEFAULT 1,
  total_amount DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'PKR',
  status ENUM('draft','issued','expired','converted','cancelled') NOT NULL DEFAULT 'draft',
  snapshot_json JSON NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id),
  FOREIGN KEY(package_id) REFERENCES packages(id),
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  quotation_id BIGINT UNSIGNED NULL,
  customer_id BIGINT UNSIGNED NULL,
  booking_request_id BIGINT UNSIGNED NULL,
  invoice_no VARCHAR(60) UNIQUE NOT NULL,
  due_date DATE NULL,
  total_amount DECIMAL(14,2) NOT NULL,
  paid_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  currency CHAR(3) NOT NULL DEFAULT 'PKR',
  status ENUM('draft','issued','partially_paid','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id),
  FOREIGN KEY(quotation_id) REFERENCES quotations(id) ON DELETE SET NULL,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS invoice_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id BIGINT UNSIGNED NOT NULL,
  description VARCHAR(255) NOT NULL,
  quantity DECIMAL(12,2) NOT NULL DEFAULT 1,
  unit_price DECIMAL(14,2) NOT NULL,
  line_total DECIMAL(14,2) NOT NULL,
  FOREIGN KEY(invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  invoice_id BIGINT UNSIGNED NULL,
  reference VARCHAR(100) NULL,
  payer_name VARCHAR(190) NULL,
  method VARCHAR(80) NULL,
  amount DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'PKR',
  proof_document_id BIGINT UNSIGNED NULL,
  status ENUM('pending','submitted','under_review','verified','rejected','refunded') NOT NULL DEFAULT 'pending',
  verified_by BIGINT UNSIGNED NULL,
  verified_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id),
  FOREIGN KEY(invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
  FOREIGN KEY(proof_document_id) REFERENCES documents(id) ON DELETE SET NULL,
  FOREIGN KEY(verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ledger_entries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  customer_id BIGINT UNSIGNED NULL,
  invoice_id BIGINT UNSIGNED NULL,
  payment_id BIGINT UNSIGNED NULL,
  entry_type ENUM('debit','credit','adjustment','reversal') NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'PKR',
  reference VARCHAR(100) NULL,
  reason TEXT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id),
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY(invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
  FOREIGN KEY(payment_id) REFERENCES payments(id) ON DELETE SET NULL,
  FOREIGN KEY(created_by) REFERENCES users(id),
  INDEX idx_ledger_agency_date(agency_id,created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS booking_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  package_id BIGINT UNSIGNED NULL,
  quotation_id BIGINT UNSIGNED NULL,
  customer_id BIGINT UNSIGNED NULL,
  booking_no VARCHAR(60) UNIQUE NOT NULL,
  service_type ENUM('universal','flight','visa','hotel','transport','other') NOT NULL DEFAULT 'universal',
  status ENUM('submitted','under_review','processing','confirmed','unavailable','rejected','cancelled','completed') NOT NULL DEFAULT 'submitted',
  cancellation_reason TEXT NULL,
  notes TEXT NULL,
  submitted_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id),
  FOREIGN KEY(package_id) REFERENCES packages(id) ON DELETE SET NULL,
  FOREIGN KEY(quotation_id) REFERENCES quotations(id) ON DELETE SET NULL,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY(submitted_by) REFERENCES users(id),
  INDEX idx_bookings_queue(agency_id,status,created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS booking_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_request_id BIGINT UNSIGNED NOT NULL,
  item_type VARCHAR(80) NOT NULL,
  reference_id BIGINT UNSIGNED NULL,
  status VARCHAR(60) NOT NULL,
  supplier_reference VARCHAR(120) NULL,
  notes TEXT NULL,
  FOREIGN KEY(booking_request_id) REFERENCES booking_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vouchers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_request_id BIGINT UNSIGNED NOT NULL,
  voucher_no VARCHAR(60) UNIQUE NOT NULL,
  service_type VARCHAR(80) NOT NULL,
  document_id BIGINT UNSIGNED NULL,
  status ENUM('issued','revoked','replaced') NOT NULL DEFAULT 'issued',
  issued_by BIGINT UNSIGNED NOT NULL,
  issued_at DATETIME NOT NULL,
  FOREIGN KEY(booking_request_id) REFERENCES booking_requests(id) ON DELETE CASCADE,
  FOREIGN KEY(document_id) REFERENCES documents(id) ON DELETE SET NULL,
  FOREIGN KEY(issued_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  audience_type ENUM('all','agency','role','user') NOT NULL,
  audience_id BIGINT UNSIGNED NULL,
  notification_type VARCHAR(80) NOT NULL,
  title VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  publish_at DATETIME NULL,
  expires_at DATETIME NULL,
  status ENUM('draft','scheduled','published','expired') NOT NULL DEFAULT 'draft',
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS templates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  template_type ENUM('quotation','invoice','voucher','marketing') NOT NULL,
  name VARCHAR(190) NOT NULL,
  config_json JSON NOT NULL,
  protected_fields JSON NULL,
  status ENUM('draft','published','retired') NOT NULL DEFAULT 'draft',
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS marketing_designs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  template_id BIGINT UNSIGNED NULL,
  title VARCHAR(190) NOT NULL,
  payload_json JSON NOT NULL,
  output_png_path VARCHAR(500) NULL,
  output_pdf_path VARCHAR(500) NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(template_id) REFERENCES templates(id) ON DELETE SET NULL,
  FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS support_tickets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  subject VARCHAR(190) NOT NULL,
  priority ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  status ENUM('open','in_progress','waiting_for_agent','resolved','closed') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  agency_id BIGINT UNSIGNED NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id BIGINT UNSIGNED NULL,
  before_json JSON NULL,
  after_json JSON NULL,
  ip_address VARCHAR(64) NULL,
  user_agent VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE SET NULL,
  INDEX idx_audit_entity(entity_type,entity_id),
  INDEX idx_audit_created(created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(120) PRIMARY KEY,
  setting_value TEXT NULL,
  is_secret TINYINT(1) NOT NULL DEFAULT 0,
  updated_by BIGINT UNSIGNED NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO roles(role_key,role_name,scope_type) VALUES
('super_admin','Super Admin','system'),
('operations_admin','Operations Admin','system'),
('b2b_manager','B2B Manager','system'),
('inventory_manager','Inventory Manager','system'),
('visa_officer','Visa Officer','system'),
('booking_officer','Booking Officer','system'),
('finance','Finance','system'),
('content_admin','Content Admin','system'),
('support','Support','system'),
('auditor','Auditor','system'),
('agency_owner','Agency Owner','agency'),
('sales_staff','Sales Staff','agency'),
('booking_staff','Booking Staff','agency'),
('accounts_staff','Accounts Staff','agency'),
('marketing_staff','Marketing Staff','agency'),
('read_only','Read Only','agency');
