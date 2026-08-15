# MyUmrahGo Rebuild Progress

Master source: `MyUmrahGo_Master_Product_Roadmap (1).pdf`.

## Completed in rebuild branch

- Created isolated rebuild branch: `rebuild/master-roadmap-v1`.
- Added roadmap-to-engineering implementation blueprint.
- Added expanded MySQL schema foundation aligned to users, roles, permissions, agencies, staff, branding, CRM, passengers, documents, inventory, packages, quotations, invoices, payments, ledgers, bookings, vouchers, notifications, templates, marketing designs, support, audit logs and settings.
- Updated README to make the PDF roadmap the authoritative product direction.

## Next implementation milestones

### M1 — Application shell
- New public website shell
- New B2B shell
- New admin shell
- Responsive navigation
- Shared design system

### M2 — Authentication and authorization
- Login/logout
- Password hashing
- Sessions
- Agent application flow
- Approval flow
- Granular permission middleware
- Agency data isolation

### M3 — Inventory
- Flights
- Group blocks
- Visa products
- Makkah hotels
- Madinah hotels
- Seasonal rates
- Transport
- Ziyarat/add-ons
- Publish/unpublish controls

### M4 — Package engine
- Multi-step builder
- Autosave
- Component pricing
- Margin rules
- Final price calculation
- Package history

### M5 — Quotation / CRM / documents
- Customer records
- Passenger records
- Secure document vault
- White-label quotation templates
- PDF/PNG quotation generation
- Quotation history

### M6 — Booking center
- Universal booking request
- Service-level request queues
- Status transitions
- Admin processing
- Ticket/voucher uploads
- Agent tracking

### M7 — Finance
- Invoices
- Receipts
- Payment proof
- Verification
- Ledgers
- Adjustments/reversals
- Financial reports

### M8 — Marketing / notifications
- Marketing Studio
- Template management
- Notifications
- Advisories
- Scheduling

### M9 — QA and Hostinger production
- Security review
- Cross-agency access tests
- Backup/restore tests
- Mobile/desktop tests
- Hostinger database setup
- Domain/SSL
- Production configuration
