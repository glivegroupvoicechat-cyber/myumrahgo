# MyUmrahGo.com

**B2B Umrah + Travel Operating Platform**

MyUmrahGo is being rebuilt from the Master Product Roadmap into a modular production platform for travel agencies.

## Product mission

One secure workspace for an agency to:

**Build → Quote → Invoice → Collect → Book → Process → Issue → Track → Market → Report**

## Rebuild scope

### Public website
- Premium modern landing experience
- Hero section and visual storytelling
- Product/packages/content
- B2B signup and login
- Support and public CMS content
- Responsive UI

### B2B agent portal
- Dashboard
- Agency profile and white-label branding
- Package builder with autosave
- Inventory browsers
- CRM and passenger records
- Quotations / invoices / payments / ledgers
- Booking center and status tracking
- Secure documents and vouchers
- Marketing Studio
- Notifications, support and reports
- Staff/role management

### Admin control panel
- KPIs and operational queues
- Agent onboarding and approvals
- Staff, roles and granular permissions
- Flight / visa / hotel / transport / ziyarat inventory
- Package/pricing rules
- Booking processing
- Payments, ledgers and financial controls
- Documents and vouchers
- Notifications and templates
- Reports/export
- CMS
- Audit logs
- System settings
- Backup/restore monitoring

## Architecture

```text
Public UI / B2B UI / Admin UI
            |
       PHP Application API
            |
          MySQL
       /           \
Private Files      Audit Logs
       |
Scheduler / Jobs / Notifications
```

The production database is authoritative. Sensitive customer documents stay in private storage and are accessed only through authorized server-side routes.

## Development branch

The current implementation work is on:

`rebuild/master-roadmap-v1`

The original `main` branch remains preserved as the previous implementation.

## Current rebuild assets

- `docs/MASTER_ROADMAP_IMPLEMENTATION.md` — roadmap-to-engineering translation
- `database/master_schema_v2.sql` — roadmap-aligned database foundation

## Build sequence

1. Foundation + authentication + permissions
2. Admin and agency shells
3. Inventory
4. Package engine
5. Quotations / CRM / secure documents
6. Booking workflows
7. Finance
8. Marketing Studio
9. Notifications / automation
10. Growth integrations

## Security baseline

- Strong password/session controls
- Role-based authorization
- Agency-level data isolation
- Private document storage
- Server-side validation
- Audit logging
- Backup/restore process
- Supplier/net-rate protection
- Admin access protection

See `docs/MASTER_ROADMAP_IMPLEMENTATION.md` for the complete implementation baseline.
