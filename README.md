# MyUmrahGo

**Premium B2C + B2B Umrah travel platform.**

MyUmrahGo is being built as a mobile-first Umrah marketplace and partner platform: emotionally engaging on the consumer side, operationally powerful on the B2B side, and designed to scale into live inventory, booking, payments and support.

## Product experience

### B2C
- Premium, UGC-inspired landing experience
- Package discovery and comparison direction
- Makkah + Madinah journey storytelling
- Package detail and traveller selection
- Build-your-own-Umrah direction
- Booking/enquiry journey
- Trust, transparency and human-support messaging
- Responsive mobile-first UI

### B2B
- Partner dashboard foundation
- Agency navigation and operational workspace
- Package builder direction
- Hotel and flight inventory areas
- Booking management
- Vouchers
- Wallet/top-up
- Support

## Architecture roadmap

**Stage 1 — Experience:** static, responsive frontend and design system.

**Stage 2 — Application:** componentized frontend, shared package data model, forms, validation and client state.

**Stage 3 — Backend:** secure authentication, agency roles, users, packages, hotels, flights, bookings, payments, vouchers and audit logs.

**Stage 4 — Integrations:** hotel/flight/visa inventory providers, payment gateway, WhatsApp/SMS/email notifications and document generation.

**Stage 5 — Production:** security review, performance, observability, backups, QA, Hostinger deployment and custom domain.

## Repository

```text
/
├── index.html              # B2C homepage
├── styles.css              # Shared visual system
├── app.js                  # B2C interactions
├── pages/
│   ├── package.html        # Package details / enquiry UI
│   └── b2b.html            # Partner portal foundation
├── PROJECT_STATUS.md
├── DEPLOYMENT.md
└── README.md
```

## Production rules

- Demo prices and inventory are **not live availability**.
- No API keys, passwords, payment secrets or database credentials belong in this repository.
- All booking, pricing, availability and payment decisions must be validated server-side before production.
- B2B permissions must be enforced server-side; hiding UI controls is not security.
- Customer and agency data must be stored and processed through authenticated backend services.

## Hostinger

The current frontend is intentionally deployable as a static site. Hostinger can serve the B2C/B2B UI while the production backend is introduced separately. See `DEPLOYMENT.md` for the deployment path.
