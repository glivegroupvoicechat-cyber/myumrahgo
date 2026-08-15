-- Quotation persistence for the roadmap-driven package/quote workflow.
CREATE TABLE IF NOT EXISTS quotations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_no VARCHAR(40) NOT NULL UNIQUE,
  agency_id BIGINT UNSIGNED NULL,
  title VARCHAR(190) NOT NULL,
  customer_name VARCHAR(190) NOT NULL,
  valid_until DATE NULL,
  total_pkr DECIMAL(14,2) NOT NULL,
  status ENUM('draft','issued','expired','cancelled','converted') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS quotation_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_id BIGINT UNSIGNED NOT NULL,
  item_name VARCHAR(190) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'PKR',
  unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
  quantity DECIMAL(12,2) NOT NULL DEFAULT 1,
  amount_pkr DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_quotations_agency_status ON quotations(agency_id,status,created_at);
