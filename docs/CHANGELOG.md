# ATBI Intertrade System - Changelog

## [Version 1.1] - Registration & Approval Flow
**Date:** October - November 2026
**Changes:**
- Added full Registration Form with First Name, Middle Name, Last Name, and Extension Name.
- Added Email restrictions (must be a Gmail account).
- Added ATBI Affiliation (text-only) and Phone Number (numbers only) fields.
- Added `status` column (pending, approved, rejected) to the users table.
- Added Admin Approval mechanism: Admins can now Approve or Reject pending registrations.
- New users cannot log in until the Admin approves their account.
- Login page now features an animated toggle between Login and Register forms.
- Added CSS animation to slide the form and brand logos when toggling between Login/Register.