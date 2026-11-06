# System Flowchart
<!-- AUTHENTICATION AND LOGIN -->
```mermaid
graph TD
    Start([User Visits System]) --> CheckAuth{Is User Logged In?<br/>Vue: isLoggedIn}
    
    %% LOGIN ROUTING
    CheckAuth -->|True| Dashboard[Redirect to Home Page]
    CheckAuth -->|False| LoginPage[Show Login Page]
    
    %% REGISTRATION FLOW
    LoginPage -->|Click Create Account| ShowRegister[Show Register Form<br/>Vue: isRegistering = true]
    ShowRegister -->|Submit Form| ValRegister{Validate Fields<br/>Gmail and 11 Digits?}
    
    ValRegister -->|False| RegError[Show Error Message<br/>Vue: regError]
    ValRegister -->|True| SaveUser[(Save to DB<br/>status = pending)]
    SaveUser --> RegSuccess[Show Pending Modal<br/>Vue: showSuccessModal = true]
    RegSuccess --> LoginPage
    
    %% LOGIN FLOW
    LoginPage -->|Enter Credentials| CheckCredentials{Password Matches?<br/>Auth.php: password_verify}
    
    %% Wrong Password Logic
    CheckCredentials -->|False| CheckAttempts{login_attempts >= 4?}
    CheckAttempts -->|False| IncrementAttempts[(DB: increment attempts)] --> ShowInvalid[Show Invalid Credentials<br/>HTTP 401]
    CheckAttempts -->|True| LockAccount[(DB: lockout = 30s)] --> ShowLockout[Start 30s Timer<br/>HTTP 429]
    ShowInvalid --> LoginPage
    ShowLockout --> LoginPage
    
    %% Correct Password Logic
    CheckCredentials -->|True| CheckStatus{Status == approved?}
    CheckStatus -->|False| ShowStatusError[Show Pending or Rejected Error<br/>HTTP 403]
    ShowStatusError --> LoginPage
    CheckStatus -->|True| LoginSuccess[Clear Attempts and Redirect<br/>Vue: router.push Home]
    
    %% FORGOT PASSWORD FLOW
    LoginPage -->|Click Forgot Password| ForgotModal[Open Forgot Modal<br/>Vue: showForgotModal = true]
    ForgotModal -->|Submit Email| CheckEmailExists{Email in DB?}
    
    CheckEmailExists -->|False| EmailError[Show No Account Found]
    CheckEmailExists -->|True| GenOTP[(Generate OTP<br/>Expires in 10 mins)]
    GenOTP --> SendEmail[Send SMTP Email]
    SendEmail --> EnterOTP[User Enters OTP]
    
    EnterOTP --> ValidateOTP{OTP Matches and Not Expired?}
    ValidateOTP -->|False| OTPError[Show Invalid OTP]
    ValidateOTP -->|True| ResetForm[Show New Password Form<br/>Vue: forgotStep = reset]
    
    ResetForm -->|Submit New Password| CheckLength{Password >= 8 Chars?}
    CheckLength -->|False| PwError[Show Password too short]
    CheckLength -->|True| HashOnce[(Hash Password ONCE<br/>DB: Clear OTP fields)]
    HashOnce --> ResetSuccess[Show Custom Success Modal<br/>Vue: showResetSuccess = true]
    ResetSuccess --> LoginPage