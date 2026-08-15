-- CRM/passenger support migration for the roadmap rebuild.
-- Safe to run after database/master_schema_v2.sql.
ALTER TABLE customers ADD INDEX IF NOT EXISTS idx_customers_email(email);
ALTER TABLE passengers ADD INDEX IF NOT EXISTS idx_passengers_customer(customer_id);
