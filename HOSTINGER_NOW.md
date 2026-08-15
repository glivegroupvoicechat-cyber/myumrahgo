# MyUmrahGo — Hostinger Deployment Gate

## Production target
- Domain: `myumrahgo.com`
- Runtime: Hostinger PHP + MySQL
- GitHub Pages: frontend preview only
- Production branch: `rebuild/master-roadmap-v1` until final QA, then merge deliberately

## Pre-launch checks
1. Create MySQL database/user in Hostinger.
2. Import `database/master_schema_v2.sql` and required migrations in documented order.
3. Copy `api/config.example.php` to `api/config.php` and set DB credentials and a strong application secret.
4. Confirm `GET /api/health.php` returns `ok: true` for the required tables.
5. Test admin login and agency login over HTTPS.
6. Test agency registration → pending → admin approval → login.
7. Test hotel/flight inventory reads and admin publish controls.
8. Test Package Builder → server quote → quotation → booking request.
9. Test invoice/payment/ledger records and booking status history.
10. Verify private document storage is outside public web access or protected by server rules.
11. Replace all demo/sample content before launch.
12. Disable debug output and rotate any temporary credentials.

## Scope lock
Referral functionality is excluded. The remaining A–Z scope includes public CMS, B2B agency operations, Admin, inventory, CRM/passengers, pricing, quotations, invoices, payments, bookings, documents/vouchers, notifications/support, marketing/white-label controls, reports/audit and production security.

## Important
Do not point the live domain at GitHub Pages for the full application. PHP/MySQL APIs must run on Hostinger.
