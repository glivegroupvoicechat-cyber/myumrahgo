-- Finance workflow migration.
CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(80) NOT NULL UNIQUE,
  quotation_id BIGINT UNSIGNED NULL,
  booking_id BIGINT UNSIGNED NULL,
  agency_id BIGINT UNSIGNED NOT NULL,
  customer_id BIGINT UNSIGNED NULL,
  subtotal_pkr DECIMAL(14,2) NOT NULL DEFAULT 0,
  tax_pkr DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_pkr DECIMAL(14,2) NOT NULL DEFAULT 0,
  status ENUM('draft','issued','partially_paid','paid','void','overdue') NOT NULL DEFAULT 'draft',
  due_date DATE NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  INDEX idx_invoices_agency_status(agency_id,status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  invoice_id BIGINT UNSIGNED NULL,
  booking_id BIGINT UNSIGNED NULL,
  amount_pkr DECIMAL(14,2) NOT NULL,
  payment_method VARCHAR(80) NOT NULL,
  reference_no VARCHAR(160) NULL,
  proof_path VARCHAR(500) NULL,
  status ENUM('pending','verified','rejected','reversed') NOT NULL DEFAULT 'pending',
  verified_by BIGINT UNSIGNED NULL,
  verified_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(verified_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_payments_agency_status(agency_id,status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ledger_entries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  agency_id BIGINT UNSIGNED NOT NULL,
  entry_type ENUM('debit','credit','adjustment') NOT NULL,
  reference_type VARCHAR(60) NULL,
  reference_id BIGINT UNSIGNED NULL,
  amount_pkr DECIMAL(14,2) NOT NULL,
  description VARCHAR(255) NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY(agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
  FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_ledger_agency_created(agency_id,created_at)
) ENGINE=InnoDB;
