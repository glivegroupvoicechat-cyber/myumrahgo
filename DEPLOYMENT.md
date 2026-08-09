# MyUmrahGo Deployment

## Current stage
MyUmrahGo is currently a static frontend prototype. The repository contains the B2C experience and B2B UI foundation. Live inventory, authentication, payments and booking APIs are not connected yet.

## Hostinger path

1. Keep GitHub as the source of truth.
2. Deploy the contents of the repository to the Hostinger website root (`public_html`) for the static frontend.
3. Confirm that `index.html` is the default entry point.
4. Keep the relative paths intact (`styles.css`, `app.js`, and `pages/*`).
5. Point `myumrahgo.com` DNS to the Hostinger hosting target.
6. Enable Hostinger SSL after DNS is active.
7. Test desktop and mobile routes, especially `/pages/package.html` and `/pages/b2b.html`.

## Before production

- Replace demo package data with a secure backend/database.
- Add real authentication and role-based access.
- Connect hotel, flight and visa inventory sources.
- Implement booking state management and audit logs.
- Add a payment provider only after credentials are supplied.
- Add server-side validation and rate limiting.
- Never commit API keys, passwords, payment secrets or database credentials.

## Important

Do not put secrets into HTML, CSS, JavaScript, GitHub Issues, or public configuration files. Production secrets belong in the hosting/backend environment configuration.
