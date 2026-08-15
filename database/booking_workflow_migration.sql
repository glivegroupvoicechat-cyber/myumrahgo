-- Booking workflow support migration.
CREATE TABLE IF NOT EXISTS booking_status_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(60) NOT NULL,
  note TEXT NULL,
  changed_by BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY(changed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_booking_history_booking(booking_id,created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vouchers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  voucher_no VARCHAR(80) NOT NULL UNIQUE,
  voucher_type VARCHAR(60) NOT NULL DEFAULT 'umrah',
  storage_path VARCHAR(500) NULL,
  status ENUM('draft','issued','cancelled') NOT NULL DEFAULT 'draft',
  issued_by BIGINT UNSIGNED NULL,
  issued_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY(issued_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_vouchers_booking(booking_id)
) ENGINE=InnoDB;
