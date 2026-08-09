# MyUmrahGo

**A premium B2C + B2B Umrah travel platform.**

MyUmrahGo is being built as a mobile-first experience that combines premium travel storytelling, UGC-inspired discovery, transparent package presentation, and a professional B2B partner workspace.

## Current build

### B2C
- Premium landing page
- UGC-inspired visual direction
- Package discovery cards
- Package detail experience
- Build-your-own-Umrah direction
- Responsive/mobile-first UI
- Interactive save and navigation controls

### B2B
- Partner dashboard UI foundation
- Agent-focused navigation
- Package builder direction
- Hotel/flight inventory direction
- Booking, voucher, wallet and support areas

## Repository structure

```text
/
├── index.html
├── styles.css
├── app.js
├── pages/
│   ├── package.html
│   └── b2b.html
├── PROJECT_STATUS.md
└── DEPLOYMENT.md
```

## Development roadmap

1. **B2C UX** — discovery, package details and booking/enquiry flow.
2. **B2B UX** — agent login, dashboard, package builder, inventory and booking management.
3. **Backend** — authentication, database, roles, inventory and booking state.
4. **Integrations** — flights, hotels, visas, payments and notifications.
5. **Production** — security, QA, performance, Hostinger and MyUmrahGo.com.

## Important

The current package prices and inventory are demonstration data. They must not be treated as live availability until connected to the production backend/inventory sources.

Never commit API keys, passwords, payment secrets or database credentials.

See [`DEPLOYMENT.md`](DEPLOYMENT.md) for the Hostinger deployment path.
