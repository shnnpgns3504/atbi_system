# ATBI Intertrade System - Master Changelog

This document tracks all major updates, categorized by system feature and file, for easy reference.

---

## 🔐 1. Authentication, Login & Registration
**Files Involved:** `Login.vue`, `auth.ts`, `Auth.php`, `ForgotPassword.php`, `UserModel.php`

### [Version 3.1] - Login Lockout & Password Hash Fix (Latest)
- **Database (`users` table):** Added `login_attempts` (to count failures) and `login_lockout_until` (to store the 30s timeout).
- **Backend (`UserModel.php`):** 
  - **Critical Bug Fix:** Removed the `beforeUpdate` hook. Previously, the model was hashing the password on update, AND the controller was hashing it during a reset, causing a "double-hash" which made the new password fail during login.
  - Added `login_attempts` and `login_lockout_until` to `$allowedFields`.
- **Backend (`Auth.php`):** 
  - Implemented strict login logic: If `password_verify()` fails, it increments `login_attempts`. 
  - If the user hits 4 attempts, it sets `login_lockout_until` to +30 seconds and returns an HTTP `429 Too Many Requests` error.
  - If login succeeds, it resets `login_attempts` to 0.
- **Backend (`ForgotPassword.php`):** Removed the OTP attempt limit (since lockout moved to login). Ensured `resetPassword()` clears the OTP fields and saves the new hash exactly once.
- **Frontend (`Login.vue`):** 
  - Added `loginLockoutTimer` state and `startLoginLockout()` function.
  - If the backend returns a `429` error, the frontend catches it, disables the "Sign In" button, and displays a 30-second countdown text.
  - Replaced the native browser `alert()` after a successful password reset with a custom styled Vue modal (`showResetSuccess`).

### [Version 2.9] - OTP Forgot Password Flow
- **Database:** Added `reset_otp` (6-digit code) and `reset_otp_expires` (10-minute limit) to the `users` table.
- **Backend (`ForgotPassword.php`):** 
  - `requestOtp()` generates a random 6-digit code, saves it with a 10-minute expiry, and uses `\Config\Services::email()` to send a real HTML email via SMTP (Gmail).
  - `verifyOtp()` checks if the code matches and isn't expired.
  - `resetPassword()` updates the password and clears the OTP columns.
- **Frontend (`Login.vue`):** Built a 3-step modal (`forgotStep` = 'email', 'otp', 'reset').

### [Version 2.8] - ATBI Dropdown & Registration Validation
- **Frontend (`Login.vue`):** 
  - Changed the "ATBI Affiliation" text input into a `<select>` dropdown containing BSU, CLSU, ISU, DMMMSU, MMSU, and PSAU.
  - Enforced HTML5 input restrictions: Names (letters only), Phone (numbers only, 11 digits), Email (must be `@gmail.com`).
  - Added Live Password Rules checkmarks (green text for 8+ chars, uppercase, lowercase, number).
  - Added SVG Eye icons to toggle password visibility.
  - Added red asterisks (`*`) to all required fields.
- **Backend (`Auth.php`):** Enforced backend validation for the new fields (e.g., `min_length[10]`, `numeric`).

### [Version 2.0] - Registration Status Workflow
- **Database:** Added `status` enum (`pending`, `approved`, `rejected`) to `users`.
- **Backend (`Auth.php`):** New users are saved with `status = 'pending'`. Login is blocked if `status` is not `'approved'`.
- **Backend (`Users.php`):** Integrated SMTP email sending inside `approveUser()`. When an admin approves a user, an automatic confirmation email is sent.

### [Version 1.0] - Super Admin Role Standardization
- **Database:** Removed `super_admin` from the `role` enum. Merged all privileges into the standard `admin` role.
- **Frontend (`Layout.vue`):** Removed all `super_admin` checks. Admin sidebar menu now shows for `role === 'admin'`.

---

## 👑 2. Admin Management & Modals
**Files Involved:** `SuperAdminUsers.vue`, `Users.php`, `ConfirmationModal.vue`

### [Version 1.8] - Custom Confirmation Modals
- **Frontend (`ConfirmationModal.vue`):** Created a new reusable custom Vue component to replace native browser `alert()` and `confirm()` popups. It features custom icons, colors, and animations.
- **Frontend (`SuperAdminUsers.vue`):** Replaced all native "Approve/Reject" confirmation popups with the new custom modal.

### [Version 1.2] - User Approval UI
- **Frontend (`SuperAdminUsers.vue`):** 
  - Added a "Status" column with color-coded badges (`pending` = yellow, `approved` = green, `rejected` = red).
  - Added "Approve" and "Reject" action buttons for pending users.
  - Added `@username` display so admins can see the unique username chosen by the user.

---

## 📅 3. Calendar Management
**Files Involved:** `Calendar.vue`, `AdminCalendar.vue`, `ArchivedCalendar.vue`, `CalendarController.php`

### [Version 1.1] - Calendar Overhaul & Archive System
- **Database:** Added `is_archived` (soft delete) and `status` (upcoming, finished, cancelled) to the `calendar_events` table.
- **Backend (`CalendarController.php`):** 
  - Added logic to separate permanent system holidays (`is_holiday = 1`) from custom admin events.
  - Added endpoints for `archive`, `restore`, and `deletePermanent`.
- **Frontend (`Calendar.vue`):** 
  - Removed the "+ Add Event" button. Replaced it with a click-to-add feature (`@cell-click`). Clicking a date on the calendar opens the Add Event modal.
  - Enforced a 1-year view limit (`min-date`, `max-date`).
  - System holidays render permanently in red.
- **Frontend (`AdminCalendar.vue`):** Filtered out system holidays so admins only manage their custom events. Added search and month/year filters.
- **Frontend (`ArchivedCalendar.vue`):** Created a brand new page (`/admin/calendar/archive`) for admins to view, restore, or permanently delete archived events.

---

## 🎨 4. Global Layout & Navigation
**Files Involved:** `Layout.vue`, `Home.vue`, `About.vue`, `Router/index.ts`

### [Version 2.7] - Layout Perfection & ATBI Routing Fix
- **Frontend (`Layout.vue`):** Restructured the layout so the Header and Navbar never overlap the Sidebar. The Sidebar extends from the very top, while the Header/Navbar align with the right content area (matching the Footer).
- **Frontend (`Router/index.ts`):** Changed ATBI routing from `:slug` to `:id` to guarantee pages load correctly based on the database ID.
- **Frontend (`AtbiDetail.vue`):** Added a `watch` on `route.params.id` so clicking between ATBIs automatically reloads the page data without needing to go back to the Home page.
- **Backend (`Atbis.php`):** Updated `show($identifier)` to check if the identifier is numeric (`atbi_id`) or text (`slug`).

### [Version 2.1] - Global Header, Navbar & Footer
- **Frontend (`Layout.vue`):** 
  - Added a fixed Top Header with system title and user profile.
  - Added a Secondary Navigation Bar with a green-to-blue gradient.
  - Added a Footer with circular ATBI logos (3 left, 3 right) and the main logo in the center.
- **Frontend (`Home.vue`):** Removed the grid of ATBI cards. The home page now only displays a large "Welcome" message.
- **Frontend (`About.vue`):** Created a brand new "About Us" page with sample data.

---

## 🗄️ 5. Database Schema Updates (SQL Summary)
For future reference, here is every column added to the system:

**Table: `users`**
- `first_name`, `middle_name`, `last_name`, `extension_name`
- `atbi_affiliation`, `phone_number`
- `status` (pending, approved, rejected)
- `reset_otp`, `reset_otp_expires`, `otp_attempts`
- `login_attempts`, `login_lockout_until`

**Table: `calendar_events`**
- `is_archived`, `status` (upcoming, finished, cancelled)