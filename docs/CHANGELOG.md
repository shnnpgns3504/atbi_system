# ATBI Intertrade System - Master Changelog

## [Version 1.8] - Form Layout Fix & Advanced Validation UI
**Date:** November 6, 2026
**Changes:**
- **Frontend (Login.vue):**
  - **Bug Fix (Layout):** Replaced the absolute-position sliding animation with a cleaner `transition` tag (slide-fade) so the left panel design and logos no longer disappear when the user clicks "Create Account".
  - **Visual Enhancements:** Improved the design with soft shadows, rounded corners (`rounded-xl`), better spacing, and a custom scrollbar for the registration form.
  - **Live Email Validation:** Added an inline message under the email field that turns red and shows a warning if the input is not a `@gmail.com` address.
  - **Live Phone Validation:** Restricted the phone number input to exactly 11 digits. Added an inline red message if the length is not 11.
  - The Register button is now automatically disabled if the email or phone number is invalid, or if the passwords do not match.

## [Version 1.5] - Input Validation & UI Layout Fix
**Date:** November 6, 2026
**Changes:**
- **Frontend (Login.vue):**
  - **Layout Fix:** Resolved the issue where the Register form was cut off or hidden. It now smoothly scrolls within the right panel without breaking the layout.
  - **Name Arrangement:** Reordered the name input fields to: First Name -> Middle Name -> Last Name -> Extension Name.
  - **Strict Letter Enforcement:** Added real-time input filtering (`@input`) to First Name, Middle Name, Last Name, Extension, and ATBI fields so they only accept letters and spaces.
  - **Strict Number Enforcement:** Added real-time input filtering to the Phone Number field so it only accepts digits.

## [Version 1.4] - Enhanced Registration & Custom Modals
**Date:** November 5, 2026
**Changes:**
- **Backend (Auth.php):** 
  - Added `username` requirement to the registration API (unique and alpha-numeric only).
  - Added `confirm_password` validation to ensure both password fields match.
  - Removed the auto-generation of usernames.
- **Frontend (Login.vue):**
  - Added a dedicated Username input field to the Register form.
  - Added a Confirm Password field with live mismatch error validation.
  - Added an interactive Password Checklist (shows green checkmarks as the user meets the rules for length, uppercase, lowercase, and numbers).
  - Added SVG Eye icons to toggle password visibility for both Password fields.
  - Replaced the default `alert()` popup with a custom styled Success Modal upon registration.
- **Frontend (ConfirmationModal.vue - NEW):**
  - Created a reusable UI component for custom confirmation dialogs to prevent browser-native popups.
- **Frontend (SuperAdminUsers.vue):**
  - Replaced all `alert()` and `confirm()` native browser popups with the new `ConfirmationModal` component for Approving, Rejecting, and Role changes.
  - Added the display of `@username` in the User Management table.

## [Version 1.3] - Email Notifications & Registration Finalization
**Date:** November 5, 2026
**Changes:**
- **Backend (Users.php):** Integrated `\Config\Services::email()` inside the `approveUser` function. When an admin approves a user, the system now automatically sends a confirmation email to the user's Gmail account.
- **Database Check:** Confirmed final `users` table schema includes `first_name`, `middle_name`, `last_name`, `extension_name`, `atbi_affiliation`, `phone_number`, and `status`.
- **Security:** Users with a `pending` or `rejected` status are now actively blocked from logging in, returning an error message to the frontend.

## [Version 1.2] - Registration, Admin Approval & UI Redesign
**Date:** October 2026
**Changes:**
- **Database:** Added `first_name`, `middle_name`, `last_name`, `extension_name`, `atbi_affiliation`, and `status` columns to the `users` table.
- **Backend (Auth.php):** Updated registration logic to require Gmail, letters-only for names, and numbers-only for phone. 
- **Backend (Users.php):** Added `approveUser` and `rejectUser` endpoints for admins.
- **Frontend (Login.vue):** Completely redesigned. Added a sliding animation to switch between Login and Register forms. Added the new registration fields with HTML5 validation.
- **Frontend (SuperAdminUsers.vue):** Added a Status column and Approve/Reject buttons for admins to manage pending users.

## [Version 1.1] - Calendar Overhaul & Archive System
**Date:** October 2026
**Changes:**
- **Database:** Added `is_archived` and `status` columns to the `calendar_events` table.
- **Backend (CalendarController):** Added logic to differentiate between permanent system holidays and admin-added events. Added Archive, Restore, and Permanent Delete endpoints.
- **Frontend (Calendar.vue):** 
  - Replaced the top "+ Add Event" button with a click-to-add feature (clicking a day on the calendar opens the modal).
  - Enforced a 1-year view limit.
  - System holidays now render permanently (red blocks).
- **Frontend (AdminCalendar.vue):** Filtered out system holidays so admins only manage their custom events. Added search and month/year filters.
- **Frontend (ArchivedCalendar.vue):** Created a brand new page for admins to restore or permanently delete archived events.

## [Version 1.0] - Super Admin to Admin Merge
**Date:** October 2026
**Changes:**
- **Database:** Removed `super_admin` enum from the `users` table. Merged all super admin privileges into the standard `admin` role.
- **Backend (Users.php):** Changed `requireAdmin()` to check for `'admin'` instead of `'super_admin'`.
- **Frontend (Router & Layout):** Removed all `super_admin` references. Admin menu now shows standard management links (Products, Calendar, Users).

## [Version 0.5] - Base System Initialization
**Date:** October 2026
**Changes:**
- Created the Vue.js frontend and CodeIgniter 4 backend.
- Set up basic Authentication (Login, Logout).
- Created the 6 ATBI structure and database tables.
- Implemented basic Product CRUD and Shopping Cart.
- Created the initial static Login page with animated gradients and Playfair Display fonts.

## [Version 1.9] - UI Enhancements & Card Layout
**Date:** November 6, 2026
**Changes:**
- **Frontend (Login.vue):**
  - Wrapped both the Login and Register forms in a sleek, modern card layout with a 2px gradient border (Emerald to Blue).
  - Added a soft drop shadow behind the card to make it pop against the background.
  - Improved the overall form layout, spacing, and text readability.
  - Ensured the custom scrollbar handles overflow gracefully when the registration form is taller than the viewport.


## [Version 2.0] - Login Success Modal
**Date:** November 6, 2026
**Changes:**
- **Frontend (Login.vue):**
  - Added a new "Welcome Back" success modal that appears immediately after a successful login.
  - The modal displays the user's first name and features a bouncing wave emoji and a progress bar animation.
  - Added a 1.5-second delay before redirecting the user to the dashboard so they can see the welcome message.