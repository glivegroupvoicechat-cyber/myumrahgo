# MyUmrahGo Hostinger Production Setup

## Do not commit secrets

1. Copy `api/config.example.php` to `api/config.php` on the Hostinger server only.
2. Replace all placeholder database values with the MySQL database created in Hostinger.
3. Keep `api/config.php` outside version control and never paste credentials into GitHub.
4. Enable HTTPS for `myumrahgo.com` before accepting real credentials or documents.

## Database sequence

Import:

- `database/master_schema_v2.sql`
- `database/auth_migration.sql` if the base schema needs RBAC additions
- `database/quotation_migration.sql`

Review table conflicts before importing into an existing database. For a fresh installation, use the current master schema as the baseline and then apply the required migrations.

## First admin

Create the first Super Admin through a controlled server-side provisioning step using a strong password hash. Do not hard-code an admin password in the repository.

## Private documents

Passport scans, payment proofs and other sensitive customer files must not be served as public static assets. Store them outside the public web root where possible and expose them only through authenticated PHP download routes.

## Launch gate

Do not switch the domain to production until authentication, agency isolation, inventory, quotation, booking, financial and document workflows have passed end-to-end testing.
