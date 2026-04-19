# PERMACULTURE EDUCATIONAL PORTAL — PROJECT TRACKER

This document provides a high-level overview of the development progress, completed features, and upcoming tasks based on the **Module Roadmap** and **Official Project Proposal**.

---

## 🟢 COMPLETED TASKS (Foundational Infrastructure)

### 🖥️ Dashboard & UI/UX Normalization
- [x] **Global Font Scaling**: Reduced dashboard base font to `0.8rem` for a compact, professional look.
- [x] **Sidebar Logic**: Optimized active states (no background for active parents; solid green for leaf nodes).
- [x] **ACL Reactivity Fix**: Fixed issue where "Access Management" menu disappeared after login (switched helpers to reactive `$page.props`).
- [x] **Standardized DataTables**: 
    - Forced consistent `0.8rem` font size across all table elements.
    - Standardized control row layout: [Entries Group] (Left) | [Export + Search] (Right).
    - Standardized Export Button Group with semantic icons (PDF: Red, Excel: Green, etc.).
    - Implemented `text-transform: capitalize` for all table headers.

### 👥 Access Management (ACL)
- [x] **User Management**: Integrated with standardized DataTable.
- [x] **Role Management**: Integrated with standardized DataTable.
- [x] **Permission Management**: Integrated with standardized DataTable.

### 🎓 Module 3 — Course Management (CMS Foundation)
- [x] **Schema Upgrade**: Migrated for slugs, delivery modes, pricing, featured status, and full bilingual fields.
- [x] **"Bangla-First" Interface**: Standardized all forms to prioritize Bangla inputs/SEO tabs before English.
- [x] **Premium Rich Text Integration**:
    - [x] Integrated **CKEditor 5** via stable CDN.
    - [x] Built custom **Media Upload Pipeline** (Backend MediaController + Frontend Adapter).
    - [x] Activated "Everything is Rich Text" policy (Descriptions, Short Excerpts, and SEO notes).
- [x] **Courses Index**: Refactored from Card-Grid to Standard DataTable with PDF reporting.

---

## 🟡 CURRENTLY DEVELOPING (Module 3: CMS Phase 2)

### 📦 Batch & Enrollment Preparation
- [x] **Storage Foundation**: Fixed `storage:link` and reactive editor shields for media reliability.
- [ ] **Infrastructure Update**: Batch Management properties (Start/End dates, seat allocation).
- [ ] **Batch Logic**: Scheduled routines and enrollment deadlines.

---

## 🔴 UPCOMING TASKS (Roadmap Aligned with Proposal)

### 📊 Module 1 & 2: Web Portal & Membership
- [ ] **Homepage & Site Brand**: Designing the nature-oriented premium homepage.
- [ ] **Blog/Article System**: Knowledge sharing platform for Permaculture.
- [ ] **Membership Tiers**: Tiered membership levels (Basic, Pro, Life) with purchase flow.

### 📦 CMS Phase 2: Batch & Enrollment
- [ ] Implement Batch Management (Start/End dates, seat allocation).
- [ ] Build Enrollment Pipeline (Price calculation, payment redirection).
- [ ] **Module 7: Payment System**: Integration with bKash and SSLCommerz.

### 📖 CMS Phase 3: Curriculum & LMS
- [ ] **Topic Builder**: Sequential order, Rich Media content (Topics table already prepared).
- [ ] **PDF Reader**: In-browser PDF display for specific topic types (Handouts/Guides).
- [ ] **Live Session Management**: Integration with YouTube for recorded fallback.

### 🎯 Advanced Learning Features
- [ ] **Module 4: Quiz System**: MCQ engine with automated scoring.
- [ ] **Module 5: Reflection/Feedback**: Topic-wise reflections and peer feedback loops.
- [ ] **Module 6: Certificate System**: Automated PDF generation with QR verification.

### 📚 Module 8: Books Purchase System
- [ ] E-commerce module for physical and digital books.
- [ ] Shopping cart and checkout flow.

---

## 📊 PROJECT STATUS SUMMARY
| Module | Completion % | Status |
| :--- | :--- | :--- |
| **Foundational UI/UX** | 100% | ✅ Done |
| **ACL (Users/Roles)** | 100% | ✅ Done |
| **Module 3: Course Management** | 50% | 🚧 In Progress |
| **Module 1: Web Portal** | 10% | 📋 Planned |
| **Module 2: Membership** | 5% | 📋 Planned |
| **Module 4 & 5: Assessment** | 0% | 📋 Planned |
| **Module 6: Certificates** | 0% | 📋 Planned |
| **Module 7: Payments** | 0% | 📋 Planned |
| **Module 8: Books Store** | 0% | 📋 Planned |

*Last Updated: 18 April, 2026 (End of Day Session)*
