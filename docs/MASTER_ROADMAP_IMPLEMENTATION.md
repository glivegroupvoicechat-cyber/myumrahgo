# MyUmrahGo Master Roadmap — Implementation Blueprint

This document translates `MyUmrahGo_Master_Product_Roadmap (1).pdf` into an implementation baseline for the rebuild branch.

## Product objective

MyUmrahGo is a B2B-first digital operating platform for Umrah and travel agencies. The core workflow is:

**Build → Quote → Invoice → Collect → Book → Process → Issue → Track → Market → Report**

## System layers

1. Public website
2. B2B agent/agency portal
3. Admin / operations control panel
4. Application backend
5. MySQL database
6. Private file storage
7. Scheduler/jobs
8. Audit/security layer

## Foundation modules

### Public website
- Premium landing page and hero experience
- Product information and public content
- B2B signup/login
- Support/contact
- CMS-controlled content

### B2B portal
- Dashboard
- Agency profile and branding
- Package builder
- Package history
- Flight / visa / hotel / transport / ziyarat inventory
- Customer CRM
- Quotations
- Invoices
- Payments and proof upload
- Customer/agency ledgers
- Secure documents
- Vouchers
- Marketing Studio
- Notifications
- Reports
- Support tickets
- Staff management
- Security/settings

### Admin control panel
- Dashboard/KPIs
- B2B agent applications, approvals and account controls
- Staff/role management
- Flights and group blocks
- Visa products
- Makkah and Madinah hotels/rates/availability
- Transport
- Ziyarat/add-ons
- Package engine and pricing rules
- Booking center and processing queues
- Visa/flight/hotel/transport requests
- Quotations, invoices, payments and ledgers
- Customers, documents, vouchers
- Notifications and templates
- Reports/export
- CMS
- Settings
- Roles & permissions
- Audit logs
- Backup/restore monitoring

## Package builder

The builder follows this exact sequence:

1. Select Flight
2. Select Visa
3. Select Makkah Hotel
4. Select Madinah Hotel
5. Select Transport
6. Select Ziyarat/add-ons
7. Review passenger count and component totals
8. Apply margin / permitted pricing rule
9. Calculate final price
10. Generate white-label quotation
11. Start booking from saved package

Drafts must auto-save at major steps.

## Booking lifecycle

- Submitted
- Under Review
- Processing
- Confirmed / Unavailable / Rejected / Cancelled / Completed

Every transition must be timestamped and audited.

## Financial model

Required objects:

- Quotation
- Invoice
- Receipt
- Customer ledger
- Agency ledger
- Payment proof
- Refund/adjustment

Financial corrections must use adjustment/reversal entries rather than silently rewriting history.

## CRM and documents

Customer records connect to passengers, bookings, quotations, invoices and documents. Passport and other sensitive files must remain in private storage and be accessed through server-side authorization.

## White-label / Marketing

Agency branding should cover:

- Agency identity
- Contact information
- Logo and colors
- Quotation
- Invoice
- Voucher
- Marketing creative
- Bank details
- Terms

Marketing Studio supports admin-published templates, live preview, generated design history, PNG/PDF output, and protected template elements.

## Security baseline

- Strong password authentication and sessions
- Role-based authorization plus agency-level data isolation
- Private document storage and authorized download routes
- HTTPS
- Audit logs for sensitive actions
- Automated backup and tested restore procedure
- Protection of supplier/net rates
- Separate admin security controls
- Session timeout/revocation
- Configurable retention
- Privacy/consent controls appropriate to operating jurisdictions

## Core database model

`users`, `roles`, `permissions`, `user_roles`, `agencies`, `agency_staff`, `agency_branding`, `customers`, `passengers`, `documents`, `airlines`, `flights`, `flight_blocks`, `visa_products`, `hotels`, `hotel_rates`, `transport_services`, `ziyarat_services`, `packages`, `package_items`, `quotations`, `invoices`, `invoice_items`, `payments`, `ledger_entries`, `booking_requests`, `booking_items`, `vouchers`, `notifications`, `templates`, `marketing_designs`, `support_tickets`, `audit_logs`, `settings`.

## Build order

### Phase 0 — Foundation
Architecture, database, permissions, security model, UI map.

### Phase 1 — Core platform
Public site, signup/login, B2B portal, admin portal, agencies, branding.

### Phase 2 — Inventory
Flights, visa, Makkah hotels, Madinah hotels, transport, ziyarat.

### Phase 3 — Package engine
Builder, pricing, margin, autosave, history.

### Phase 4 — Documents + CRM
Quotation PDF/PNG, templates, customers, secure files.

### Phase 5 — Booking
Universal booking center, processing queues, statuses and issued documents.

### Phase 6 — Finance
Invoices, receipts, ledgers, payment proofs, financial reporting.

### Phase 7 — Marketing
Marketing Studio and template management.

### Phase 8 — Automation
Notifications, reminders, expiry alerts, scheduled reports.

### Phase 9 — Growth
APIs, suppliers, online payments, mobile apps, marketplace.

## MVP acceptance

A first live operational release is complete when an agent can:

1. Register
2. Be approved by admin
3. Log in securely
4. Browse permitted inventory
5. Build/save a package
6. Add margin
7. Generate a branded quotation
8. Retrieve the quotation later
9. Start booking
10. See the request appear in Admin

Admin must be able to process the request, update status and attach final documents. Security and agency isolation must be enforced server-side.
