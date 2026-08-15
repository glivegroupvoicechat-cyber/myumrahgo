# MyUmrahGo Final A→Z Build Checklist

This checklist is the execution contract for the roadmap rebuild. A module is not considered complete until its UI, API, database persistence, permissions and validation are connected.

## Public experience
- [ ] Homepage CMS-backed hero
- [ ] Package discovery and package detail
- [ ] Hotel and flight discovery
- [ ] B2B partner entry
- [ ] Agency registration
- [ ] About/company information
- [ ] Services
- [ ] FAQ
- [ ] Advisories/notifications
- [ ] Promotional banners
- [ ] Vlog / Journey Journal
- [ ] Contact and WhatsApp support
- [ ] Legal pages
- [ ] Responsive/mobile UX

## Identity and access
- [ ] Customer authentication
- [ ] Agency owner authentication
- [ ] Admin authentication
- [ ] Session security
- [ ] Password reset/change
- [ ] RBAC
- [ ] Agency data isolation
- [ ] Staff invitations and roles
- [ ] Session revocation
- [ ] Audit events

## Admin control panel
- [ ] Command dashboard
- [ ] Agency approval/rejection/suspension
- [ ] Agency profiles and limits
- [ ] Staff management
- [ ] Roles and permissions
- [ ] Hotel inventory
- [ ] Flight inventory
- [ ] Visa products
- [ ] Transport services
- [ ] Ziyarat/add-ons
- [ ] Package engine
- [ ] Booking center
- [ ] Visa processing
- [ ] Flight processing
- [ ] Hotel processing
- [ ] Transport processing
- [ ] Finance and ledgers
- [ ] Documents and vouchers
- [ ] Marketing Studio
- [ ] Notifications
- [ ] Reports/exports
- [ ] CMS
- [ ] Audit logs
- [ ] System settings
- [ ] Backup/restore controls

## Agency portal
- [ ] Dashboard
- [ ] Agency profile
- [ ] Branding
- [ ] Staff
- [ ] CRM
- [ ] Passengers
- [ ] Package builder
- [ ] Inventory selection
- [ ] Quote calculator
- [ ] Saved drafts
- [ ] Quotations
- [ ] Invoices
- [ ] Payment proof
- [ ] Wallet/top-up
- [ ] Ledger
- [ ] Booking requests
- [ ] Booking status history
- [ ] Visa requests
- [ ] Flight requests
- [ ] Hotel requests
- [ ] Transport requests
- [ ] Documents
- [ ] Vouchers
- [ ] Notifications
- [ ] Support tickets
- [ ] Marketing Studio
- [ ] Reports

## Booking and finance
- [ ] Customer record creation
- [ ] Passenger record creation
- [ ] Server-side price calculation
- [ ] Margin protection
- [ ] Quotation versioning
- [ ] Invoice generation
- [ ] Payment receipt
- [ ] Payment verification
- [ ] Ledger entries
- [ ] Reconciliation
- [ ] Booking creation
- [ ] Booking operational queues
- [ ] Cancellation rules
- [ ] Status history
- [ ] Voucher generation

## Security and production
- [ ] Prepared statements
- [ ] Server-side validation
- [ ] Authorization on every protected action
- [ ] CSRF/session controls where applicable
- [ ] Private document storage
- [ ] Secure document download
- [ ] Net-rate protection
- [ ] Audit logging
- [ ] Error handling without secret leakage
- [ ] HTTPS
- [ ] Database backup
- [ ] Restore test
- [ ] Hostinger configuration
- [ ] Production smoke test
- [ ] Mobile smoke test

## Explicit scope exclusion
- Referral program is excluded.

## Launch rule
Only after the checklist is materially complete should PR #5 be considered for merge and the production domain be pointed at the new build.
