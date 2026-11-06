# System Flowchart Explanation

This document explains the logic behind the Authentication, Registration, and Forgot Password flowcharts. It serves as a guide for developers to understand how the Frontend (Vue.js) and Backend (CodeIgniter 4) interact at each step.

---

## 1. Initial Routing (Check Auth)
**Flowchart Box:** `Is User Logged In?`
- **Logic:** When the Vue app loads, the router checks the Pinia `authStore` to see if a valid token/session exists.
- **True (Yes):** The user is immediately redirected to the `/home` dashboard.
- **False (No):** The user is kept on the `/login` page.

---

## 2. Registration Flow
**Flowchart Box:** `Validate Fields` -> `Save to DB` -> `Show Pending Modal`
- **Frontend Validation:** The Vue `Login.vue` component checks if the email ends with `@gmail.com` and if the phone number is exactly 11 digits before even sending the request.
- **Backend Validation:** `Auth.php -> register()` checks if the username/email already exists.
- **Database Save:** The user is saved with `status = 'pending'`. They cannot log in yet.
- **Success Modal:** The Vue `showSuccessModal` becomes true, showing the "Registration Submitted" popup.

---

## 3. Login Flow (The Lockout Logic)
**Flowchart Box:** `Password Matches?` -> `Check Attempts`
- **Verification:** The backend uses `password_verify()` to compare the typed password against the hashed password in the database.
- **Wrong Password:** 
  - The backend checks `login_attempts` in the database.
  - If `< 4`: It increments the attempts and returns a `401 Unauthorized`.
  - If `>= 4`: It sets `login_lockout_until = +30 seconds` and returns a **`429 Too Many Requests`**.
- **Frontend Catch:** The Vue `handleLogin` function has a `catch` block. If it sees a 429 error, it triggers `startLoginLockout(30)` which disables the button and starts the visual countdown timer.

---

## 4. Login Flow (Status Check)
**Flowchart Box:** `Status == approved?`
- **Logic:** If the password is correct, the backend checks the `status` column.
- **False (Pending/Rejected):** The backend returns a `403 Forbidden` error. The frontend displays "Wait for admin approval".
- **True (Approved):** The backend generates a random token, returns it, and the frontend redirects the user to the Dashboard.

---

## 5. Forgot Password Flow (OTP)
**Flowchart Box:** `Generate OTP` -> `Validate OTP` -> `Hash Password ONCE`
- **Request:** The user submits their email. The backend generates a 6-digit code and saves it with a 10-minute expiration.
- **Email:** CodeIgniter's `Services::email()` uses SMTP (Gmail App Password) to send the code to the user.
- **Verify:** The user enters the OTP. The backend checks if it matches and if `time() < reset_otp_expires`.
- **Reset:** Once verified, the user types a new password. 
- **The Double-Hash Bug Fix:** The backend controller manually calls `password_hash()` exactly once. (Previously, the model was also hashing it, causing the login to fail).
- **Cleanup:** The backend clears the `reset_otp` and `otp_attempts` columns so the user is no longer in a "reset" state.

---

## Key Database Columns Used

| Column | Purpose |
| :--- | :--- |
| `status` | Tracks registration state (`pending`, `approved`, `rejected`). |
| `login_attempts` | Counts consecutive failed logins. |
| `login_lockout_until` | Stores the timestamp for the 30-second login lockout. |
| `reset_otp` | Stores the 6-digit code for password reset. |
| `reset_otp_expires` | Timestamp to enforce the 10-minute OTP expiration. |
| `password` | Must contain a Bcrypt hash (e.g., `$2y$10$...`). |