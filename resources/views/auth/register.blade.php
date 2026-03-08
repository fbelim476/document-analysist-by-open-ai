<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DocMind — Register</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:           #0a0b0f;
    --surface:      #111318;
    --surface2:     #181c24;
    --border:       rgba(255,255,255,0.07);
    --accent:       #00e5a0;
    --accent2:      #0077ff;
    --accent-glow:  rgba(0,229,160,0.18);
    --text:         #f0f2f7;
    --muted:        #6b7385;
    --danger:       #ff4d6d;
    --success:      #00e5a0;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  /* ── NOISE ── */
  body::before {
    content: '';
    position: fixed; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none; z-index: 0; opacity: 0.5;
  }

  /* ── BLOBS ── */
  .blob { position: fixed; border-radius: 50%; pointer-events: none; z-index: 0; }
  .blob-1 {
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(0,119,255,0.07) 0%, transparent 65%);
    top: -250px; right: -200px;
    animation: drift 16s ease-in-out infinite;
  }
  .blob-2 {
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(0,229,160,0.06) 0%, transparent 65%);
    bottom: -200px; left: -180px;
    animation: drift 12s ease-in-out infinite reverse;
  }
  .blob-3 {
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(0,229,160,0.05) 0%, transparent 70%);
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    animation: pulse 7s ease-in-out infinite;
  }
  @keyframes drift {
    0%,100% { transform: translate(0,0); }
    33%      { transform: translate(-40px, 40px); }
    66%      { transform: translate(30px, -30px); }
  }
  @keyframes pulse {
    0%,100% { opacity: 0.3; transform: translate(-50%,-50%) scale(1); }
    50%      { opacity: 0.7; transform: translate(-50%,-50%) scale(1.2); }
  }

  /* ── GRID ── */
  .grid-lines {
    position: fixed; inset: 0;
    background-image:
      linear-gradient(var(--border) 1px, transparent 1px),
      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none; z-index: 0;
    mask-image: radial-gradient(ellipse 65% 65% at 50% 50%, black 40%, transparent 100%);
    -webkit-mask-image: radial-gradient(ellipse 65% 65% at 50% 50%, black 40%, transparent 100%);
  }

  /* ── CARD ── */
  .card-wrap {
    position: relative; z-index: 1;
    width: 100%; max-width: 440px;
    padding: 20px;
    animation: cardIn 0.6s cubic-bezier(0.22,1,0.36,1) forwards;
  }
  @keyframes cardIn {
    from { opacity: 0; transform: translateY(30px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)    scale(1); }
  }

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 22px;
    padding: 40px 36px 36px;
    box-shadow:
      0 0 0 1px rgba(255,255,255,0.03),
      0 28px 70px rgba(0,0,0,0.55),
      0 0 80px rgba(0,119,255,0.03);
  }

  /* ── LOGO ── */
  .logo {
    display: flex; align-items: center; justify-content: center;
    gap: 10px; margin-bottom: 28px;
  }
  .logo-mark {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    box-shadow: 0 0 24px var(--accent-glow), 0 4px 14px rgba(0,0,0,0.3);
    animation: logoPop 0.5s 0.25s cubic-bezier(0.34,1.56,0.64,1) both;
  }
  @keyframes logoPop {
    from { transform: scale(0.4) rotate(-15deg); opacity: 0; }
    to   { transform: scale(1)   rotate(0deg);   opacity: 1; }
  }
  .logo-text {
    font-family: 'Syne', sans-serif;
    font-size: 24px; font-weight: 800; letter-spacing: -0.5px;
  }
  .logo-text span { color: var(--accent); }

  /* ── HEADING ── */
  .heading {
    text-align: center; margin-bottom: 26px;
    animation: fadeUp 0.5s 0.15s ease both;
  }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .heading h1 {
    font-family: 'Syne', sans-serif;
    font-size: 21px; font-weight: 700;
    margin-bottom: 6px; letter-spacing: -0.3px;
  }
  .heading p { font-size: 13px; color: var(--muted); }

  /* ── STEP INDICATOR ── */
  .steps {
    display: flex; align-items: center; justify-content: center;
    gap: 0; margin-bottom: 28px;
    animation: fadeUp 0.5s 0.2s ease both;
  }
  .step-dot {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; font-family: 'Syne', sans-serif;
    border: 1.5px solid var(--border);
    color: var(--muted);
    background: var(--surface2);
    transition: all 0.3s;
    position: relative; z-index: 1;
  }
  .step-dot.active {
    border-color: var(--accent);
    color: var(--accent);
    background: rgba(0,229,160,0.08);
    box-shadow: 0 0 12px rgba(0,229,160,0.2);
  }
  .step-dot.done {
    border-color: var(--accent);
    background: var(--accent);
    color: #050a07;
  }
  .step-line {
    width: 40px; height: 1.5px;
    background: var(--border);
    transition: background 0.4s;
  }
  .step-line.done { background: var(--accent); }

  /* ── FORM GROUP ── */
  .form-group {
    margin-bottom: 15px;
    animation: fadeUp 0.45s ease both;
  }
  .form-group:nth-child(1) { animation-delay: 0.28s; }
  .form-group:nth-child(2) { animation-delay: 0.34s; }
  .form-group:nth-child(3) { animation-delay: 0.40s; }

  .form-label {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 11.5px; font-weight: 500; color: var(--muted);
    margin-bottom: 7px; letter-spacing: 0.4px; text-transform: uppercase;
  }
  .label-hint { font-size: 10.5px; color: var(--muted); opacity: 0.7; text-transform: none; letter-spacing: 0; }

  .input-wrap { position: relative; }
  .input-icon {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    font-size: 15px; pointer-events: none; opacity: 0.45; transition: opacity 0.2s;
  }
  .input-wrap:focus-within .input-icon { opacity: 1; }

  .form-input {
    width: 100%;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 14px 12px 40px;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  }
  .form-input::placeholder { color: var(--muted); opacity: 0.6; }
  .form-input:focus {
    border-color: var(--accent);
    background: #141f1a;
    box-shadow: 0 0 0 3px rgba(0,229,160,0.07);
  }
  .form-input.valid   { border-color: rgba(0,229,160,0.5); }
  .form-input.invalid { border-color: rgba(255,77,109,0.5); }

  /* validation tick */
  .valid-icon {
    position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
    font-size: 13px; opacity: 0; transition: opacity 0.2s;
    pointer-events: none;
  }
  .form-input.valid   ~ .valid-icon { opacity: 1; }

  /* pw toggle */
  .pw-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--muted); font-size: 14px; padding: 4px;
    transition: color 0.2s; line-height: 1;
  }
  .pw-toggle:hover { color: var(--text); }

  /* pw strength bar */
  .pw-strength {
    margin-top: 8px; display: none;
  }
  .pw-strength.show { display: block; }
  .strength-bar-bg {
    height: 3px; background: var(--surface2);
    border-radius: 10px; overflow: hidden; margin-bottom: 5px;
  }
  .strength-bar {
    height: 100%; border-radius: 10px;
    transition: width 0.4s ease, background 0.4s;
    width: 0%;
  }
  .strength-label { font-size: 11px; color: var(--muted); }

  /* ── ERROR ── */
  .error-msg {
    display: none; margin-bottom: 14px;
    background: rgba(255,77,109,0.08);
    border: 1px solid rgba(255,77,109,0.22);
    border-radius: 9px; padding: 10px 14px;
    font-size: 13px; color: var(--danger);
    align-items: center; gap: 8px;
    animation: shake 0.4s ease;
  }
  .error-msg.show { display: flex; }
  @keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60%  { transform: translateX(-5px); }
    40%,80%  { transform: translateX(5px); }
  }

  /* ── TERMS ── */
  .terms-row {
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 18px; margin-top: 4px;
    animation: fadeUp 0.45s 0.44s ease both;
  }
  .terms-check {
    width: 16px; height: 16px; margin-top: 1px; flex-shrink: 0;
    accent-color: var(--accent); cursor: pointer;
  }
  .terms-text { font-size: 12px; color: var(--muted); line-height: 1.55; }
  .terms-text a { color: var(--accent); text-decoration: none; }
  .terms-text a:hover { text-decoration: underline; }

  /* ── SUBMIT BTN ── */
  .btn-register {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, var(--accent2), #0055cc);
    border: none; border-radius: 10px;
    color: #fff;
    font-family: 'Syne', sans-serif;
    font-size: 14px; font-weight: 700; letter-spacing: 0.3px;
    cursor: pointer;
    transition: all 0.22s;
    box-shadow: 0 4px 22px rgba(0,119,255,0.28);
    display: flex; align-items: center; justify-content: center; gap: 8px;
    animation: fadeUp 0.45s 0.48s ease both;
  }
  .btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0,119,255,0.38);
  }
  .btn-register:active  { transform: translateY(0); box-shadow: none; }
  .btn-register:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

  /* dots */
  .dots span {
    display: inline-block; width: 5px; height: 5px;
    background: #fff; border-radius: 50%; margin: 0 2px;
    animation: dot 1s infinite;
  }
  .dots span:nth-child(2) { animation-delay: 0.15s; }
  .dots span:nth-child(3) { animation-delay: 0.30s; }
  @keyframes dot {
    0%,80%,100% { transform: scale(0.6); opacity: 0.4; }
    40%         { transform: scale(1.1); opacity: 1; }
  }

  /* ── DIVIDER ── */
  .divider {
    display: flex; align-items: center; gap: 12px;
    margin: 22px 0 18px;
    animation: fadeUp 0.45s 0.5s ease both;
  }
  .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
  .divider span { font-size: 11px; color: var(--muted); white-space: nowrap; }

  /* ── LOGIN LINK ── */
  .login-row {
    text-align: center;
    animation: fadeUp 0.45s 0.52s ease both;
  }
  .login-row p { font-size: 13px; color: var(--muted); }
  .login-row a { color: var(--accent); text-decoration: none; font-weight: 500; transition: opacity 0.2s; }
  .login-row a:hover { opacity: 0.75; text-decoration: underline; }

  /* ── SECURE BADGE ── */
  .secure-badge {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    margin-top: 22px; font-size: 11px; color: var(--muted); opacity: 0.55;
    animation: fadeUp 0.45s 0.54s ease both;
  }
</style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
<div class="grid-lines"></div>

<div class="card-wrap">
  <div class="card">

    <!-- LOGO -->
    <div class="logo">
      <div class="logo-mark">🧠</div>
      <div class="logo-text">Doc<span>Mind</span></div>
    </div>

    <!-- HEADING -->
    <div class="heading">
      <h1>Create your account</h1>
      <p>Start analyzing documents with AI in seconds</p>
    </div>

    <!-- STEP INDICATOR -->
    <div class="steps" id="stepIndicator">
      <div class="step-dot active" id="step1">1</div>
      <div class="step-line" id="line1"></div>
      <div class="step-dot" id="step2">2</div>
      <div class="step-line" id="line2"></div>
      <div class="step-dot" id="step3">3</div>
    </div>

    <!-- ERROR -->
    <div class="error-msg" id="errorMsg">
      <span>⚠️</span>
      <span id="errorText">Something went wrong.</span>
    </div>

    <!-- NAME -->
    <div class="form-group">
      <label class="form-label" for="name">
        Full Name
      </label>
      <div class="input-wrap">
        <input
          class="form-input"
          id="name"
          type="text"
          placeholder="John Doe"
          autocomplete="name"
          oninput="validateName(this)"
          onkeydown="if(event.key==='Enter') document.getElementById('email').focus()"
        >
        <span class="input-icon">👤</span>
        <span class="valid-icon">✅</span>
      </div>
    </div>

    <!-- EMAIL -->
    <div class="form-group">
      <label class="form-label" for="email">Email Address</label>
      <div class="input-wrap">
        <input
          class="form-input"
          id="email"
          type="email"
          placeholder="you@example.com"
          autocomplete="email"
          oninput="validateEmail(this)"
          onkeydown="if(event.key==='Enter') document.getElementById('password').focus()"
        >
        <span class="input-icon">✉️</span>
        <span class="valid-icon">✅</span>
      </div>
    </div>

    <!-- PASSWORD -->
    <div class="form-group">
      <label class="form-label" for="password">
        Password
        <span class="label-hint">min. 8 characters</span>
      </label>
      <div class="input-wrap">
        <input
          class="form-input"
          id="password"
          type="password"
          placeholder="Create a strong password"
          autocomplete="new-password"
          oninput="validatePassword(this)"
          onkeydown="if(event.key==='Enter') doRegister()"
        >
        <span class="input-icon">🔑</span>
        <button class="pw-toggle" type="button" onclick="togglePw(this)" title="Show/hide">👁</button>
      </div>
      <!-- STRENGTH BAR -->
      <div class="pw-strength" id="pwStrength">
        <div class="strength-bar-bg">
          <div class="strength-bar" id="strengthBar"></div>
        </div>
        <div class="strength-label" id="strengthLabel">Weak</div>
      </div>
    </div>

    <!-- TERMS -->
    <div class="terms-row">
      <input type="checkbox" class="terms-check" id="termsCheck" onchange="checkForm()">
      <label class="terms-text" for="termsCheck">
        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
      </label>
    </div>

    <!-- REGISTER BTN -->
    <button class="btn-register" id="registerBtn" onclick="doRegister()" disabled>
      <span id="btnLabel">Create Account →</span>
    </button>

    <!-- DIVIDER -->
    <div class="divider"><span>Already have an account?</span></div>

    <!-- LOGIN LINK -->
    <div class="login-row">
      <p><a href="/">Sign in instead</a></p>
    </div>

    <!-- BADGE -->
    <div class="secure-badge">🔒 Secured with CSRF protection</div>

  </div>
</div>

<script>
let nameOk = false, emailOk = false, pwOk = false;

/* ── STEP INDICATOR ── */
function updateSteps() {
  const steps = [nameOk || emailOk || pwOk, emailOk || pwOk, pwOk];
  const active = nameOk ? (emailOk ? (pwOk ? 3 : 2) : 1) : 0;

  ['step1','step2','step3'].forEach((id,i) => {
    const el = document.getElementById(id);
    el.className = 'step-dot';
    if (i < active)      el.classList.add('done'),   el.textContent = '✓';
    else if (i === active) el.classList.add('active'), el.textContent = i+1;
    else                  el.textContent = i+1;
  });
  ['line1','line2'].forEach((id,i) => {
    document.getElementById(id).className = 'step-line' + (i < active ? ' done' : '');
  });
}

/* ── VALIDATIONS ── */
function validateName(el) {
  nameOk = el.value.trim().length >= 2;
  el.className = 'form-input ' + (nameOk ? 'valid' : '');
  updateSteps(); checkForm();
}

function validateEmail(el) {
  emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim());
  el.className = 'form-input ' + (emailOk ? 'valid' : el.value ? 'invalid' : '');
  updateSteps(); checkForm();
}

function validatePassword(el) {
  const val = el.value;
  const strength = getStrength(val);
  pwOk = val.length >= 8;

  el.className = 'form-input ' + (pwOk ? 'valid' : val ? 'invalid' : '');

  const bar   = document.getElementById('strengthBar');
  const label = document.getElementById('strengthLabel');
  const wrap  = document.getElementById('pwStrength');

  if (val.length > 0) {
    wrap.classList.add('show');
    const configs = [
      { pct:'20%', bg:'#ff4d6d', text:'Weak' },
      { pct:'50%', bg:'#ffb340', text:'Fair' },
      { pct:'80%', bg:'#00b87a', text:'Good' },
      { pct:'100%',bg:'#00e5a0', text:'Strong 💪' },
    ];
    const cfg = configs[Math.min(strength, 3)];
    bar.style.width = cfg.pct;
    bar.style.background = cfg.bg;
    label.textContent = cfg.text;
    label.style.color = cfg.bg;
  } else {
    wrap.classList.remove('show');
  }
  updateSteps(); checkForm();
}

function getStrength(pw) {
  let s = 0;
  if (pw.length >= 8)  s++;
  if (/[A-Z]/.test(pw))   s++;
  if (/[0-9]/.test(pw))   s++;
  if (/[^A-Za-z0-9]/.test(pw)) s++;
  return s - 1;
}

function checkForm() {
  const terms = document.getElementById('termsCheck').checked;
  document.getElementById('registerBtn').disabled = !(nameOk && emailOk && pwOk && terms);
}

/* ── PW TOGGLE ── */
function togglePw(btn) {
  const inp = document.getElementById('password');
  const hidden = inp.type === 'password';
  inp.type = hidden ? 'text' : 'password';
  btn.textContent = hidden ? '🙈' : '👁';
}

/* ── REGISTER ── */
async function doRegister() {
  const name     = document.getElementById('name').value.trim();
  const email    = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const btn      = document.getElementById('registerBtn');
  const errBox   = document.getElementById('errorMsg');
  const errText  = document.getElementById('errorText');

  errBox.classList.remove('show');
  btn.disabled = true;
  document.getElementById('btnLabel').innerHTML =
    '<span class="dots"><span></span><span></span><span></span></span>';

  try {
    const res = await fetch("/api/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ name, email, password })
    });

    const data = await res.json();

    if (res.ok) {
      document.getElementById('btnLabel').textContent = '✓ Account Created!';
      btn.style.background = 'linear-gradient(135deg,#00e5a0,#00b87a)';
      btn.style.color = '#050a07';
      setTimeout(() => { window.location = "/"; }, 700);
    } else {
      errText.textContent = data.message || 'Registration failed. Please try again.';
      errBox.classList.add('show');
      btn.disabled = false;
      document.getElementById('btnLabel').textContent = 'Create Account →';
    }
  } catch(e) {
    errText.textContent = 'Connection error. Please try again.';
    errBox.classList.add('show');
    btn.disabled = false;
    document.getElementById('btnLabel').textContent = 'Create Account →';
  }
}
</script>
</body>
</html>
