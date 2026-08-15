-- MyUmrahGo production gate helpers.
-- Run after master schema/migrations on a fresh production database.
-- No passwords or secrets are stored here.

CREATE TABLE IF NOT EXISTS system_settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  value_type ENUM('text','number','boolean','json') NOT NULL DEFAULT 'text',
  is_public TINYINT(1) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO system_settings(setting_key,setting_value,value_type,is_public) VALUES
('site.name','MyUmrahGo','text',1),
('site.currency.primary','PKR','text',1),
('site.currency.secondary','SAR','text',1),
('site.support.whatsapp','','text',1),
('site.support.phone','','text',1),
('site.support.email','','text',1),
('site.homepage.enabled','1','boolean',1),
('site.maintenance_mode','0','boolean',0)
ON DUPLICATE KEY UPDATE setting_key=VALUES(setting_key);
