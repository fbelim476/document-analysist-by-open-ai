<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DocMind — Login</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:          #0a0b0f;
    --surface:     #111318;
    --surface2:    #181c24;
    --border:      rgba(255,255,255,0.07);
    --border-hover:rgba(255,255,255,0.14);
    --accent:      #00e5a0;
    --accent2:     #0077ff;
    --accent-glow: rgba(0,229,160,0.18);
    --text:        #f0f2f7;
    --muted:       #6b7385;
    --danger:      #ff4d6d;
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
    position: fixed;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 0;
    opacity: 0.5;
  }

  /* ── GLOW BLOBS ── */
  .blob {
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
  }
  .blob-1 {
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(0,229,160,0.07) 0%, transparent 65%);
    top: -250px; left: -250px;
    animation: drift 14s ease-in-out infinite;
  }
  .blob-2 {
    width: 550px; height: 550px;
    background: radial-gradient(circle, rgba(0,119,255,0.06) 0%, transparent 65%);
    bottom: -180px; right: -180px;
    animation: drift 18s ease-in-out infinite reverse;
  }
  .blob-3 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(0,229,160,0.04) 0%, transparent 70%);
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 6s ease-in-out infinite;
  }
  @keyframes drift {
    0%,100% { transform: translate(0,0); }
    33%      { transform: translate(50px, 30px); }
    66%      { transform: translate(-20px, 50px); }
  }
  @keyframes pulse {
    0%,100% { opacity: 0.4; transform: translate(-50%,-50%) scale(1); }
    50%      { opacity: 0.8; transform: translate(-50%,-50%) scale(1.15); }
  }

  /* ── GRID LINES BG ── */
  .grid-lines {
    position: fixed;
    inset: 0;
    background-image:
      linear-gradient(var(--border) 1px, transparent 1px),
      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
    z-index: 0;
    mask-image: radial-gradient(ellipse 60% 60% at 50% 50%, black 40%, transparent 100%);
    -webkit-mask-image: radial-gradient(ellipse 60% 60% at 50% 50%, black 40%, transparent 100%);
  }

  /* ── CARD ── */
  .card-wrap {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
    padding: 20px;
    animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
  }
  @keyframes cardIn {
    from { opacity: 0; transform: translateY(28px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)    scale(1); }
  }

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 40px 36px 36px;
    box-shadow:
      0 0 0 1px rgba(255,255,255,0.03),
      0 24px 64px rgba(0,0,0,0.5),
      0 0 80px rgba(0,229,160,0.03);
  }

  /* ── LOGO ── */
  .logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 32px;
  }
  .logo-mark {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    box-shadow: 0 0 24px var(--accent-glow), 0 4px 12px rgba(0,0,0,0.3);
    animation: logoPop 0.5s 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
  }
  @keyframes logoPop {
    from { transform: scale(0.5) rotate(-10deg); opacity: 0; }
    to   { transform: scale(1)   rotate(0deg);   opacity: 1; }
  }
  .logo-text {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.5px;
  }
  .logo-text span { color: var(--accent); }

  /* ── HEADING ── */
  .heading {
    text-align: center;
    margin-bottom: 28px;
    animation: fadeUp 0.5s 0.2s ease both;
  }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .heading h1 {
    font-family: 'Syne', sans-serif;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
  }
  .heading p {
    font-size: 13px;
    color: var(--muted);
  }

  /* ── FORM ── */
  .form-group {
    margin-bottom: 16px;
    animation: fadeUp 0.5s ease both;
  }
  .form-group:nth-child(1) { animation-delay: 0.25s; }
  .form-group:nth-child(2) { animation-delay: 0.32s; }

  .form-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: var(--muted);
    margin-bottom: 7px;
    letter-spacing: 0.4px;
    text-transform: uppercase;
  }

  .input-wrap {
    position: relative;
  }
  .input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    pointer-events: none;
    opacity: 0.5;
    transition: opacity 0.2s;
  }
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
  .form-input::placeholder { color: var(--muted); opacity: 0.7; }
  .form-input:focus {
    border-color: var(--accent);
    background: #1a2020;
    box-shadow: 0 0 0 3px rgba(0,229,160,0.08);
  }
  .form-input:focus + .input-icon,
  .input-wrap:focus-within .input-icon { opacity: 1; }

  /* password toggle */
  .pw-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    font-size: 14px;
    padding: 4px;
    transition: color 0.2s;
    line-height: 1;
  }
  .pw-toggle:hover { color: var(--text); }

  /* ── ERROR MSG ── */
  .error-msg {
    display: none;
    background: rgba(255,77,109,0.08);
    border: 1px solid rgba(255,77,109,0.25);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 13px;
    color: var(--danger);
    margin-bottom: 16px;
    align-items: center;
    gap: 8px;
    animation: shake 0.4s ease;
  }
  .error-msg.show { display: flex; }
  @keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60%  { transform: translateX(-5px); }
    40%,80%  { transform: translateX(5px); }
  }

  /* ── SUBMIT BTN ── */
  .btn-login {
    width: 100%;
    padding: 13px;
    margin-top: 8px;
    background: linear-gradient(135deg, var(--accent), #00b87a);
    border: none;
    border-radius: 10px;
    color: #050a07;
    font-family: 'Syne', sans-serif;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.3px;
    cursor: pointer;
    transition: all 0.22s;
    box-shadow: 0 4px 22px rgba(0,229,160,0.28);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    animation: fadeUp 0.5s 0.38s ease both;
  }
  .btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0,229,160,0.38);
  }
  .btn-login:active { transform: translateY(0); box-shadow: none; }
  .btn-login:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }

  /* loading dots */
  .dots span {
    display: inline-block;
    width: 5px; height: 5px;
    background: #050a07;
    border-radius: 50%;
    margin: 0 2px;
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
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 24px 0 20px;
    animation: fadeUp 0.5s 0.42s ease both;
  }
  .divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }
  .divider span { font-size: 11px; color: var(--muted); white-space: nowrap; }

  /* ── REGISTER LINK ── */
  .register-row {
    text-align: center;
    animation: fadeUp 0.5s 0.46s ease both;
  }
  .register-row p { font-size: 13px; color: var(--muted); }
  .register-row a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.2s;
  }
  .register-row a:hover { opacity: 0.75; text-decoration: underline; }

  /* ── FOOTER TAG ── */
  .secure-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 24px;
    font-size: 11px;
    color: var(--muted);
    opacity: 0.6;
    animation: fadeUp 0.5s 0.5s ease both;
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
      <h1>Welcome back</h1>
      <p>Sign in to continue to your workspace</p>
    </div>

    <!-- ERROR -->
    <div class="error-msg" id="errorMsg">
      <span>⚠️</span>
      <span id="errorText">Invalid email or password.</span>
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
          onkeydown="if(event.key==='Enter') document.getElementById('password').focus()"
        >
        <span class="input-icon">✉️</span>
      </div>
    </div>

    <!-- PASSWORD -->
    <div class="form-group">
      <label class="form-label" for="password">Password</label>
      <div class="input-wrap">
        <input
          class="form-input"
          id="password"
          type="password"
          placeholder="••••••••"
          autocomplete="current-password"
          onkeydown="if(event.key==='Enter') login()"
        >
        <span class="input-icon">🔑</span>
        <button class="pw-toggle" type="button" onclick="togglePw(this)" title="Show/hide password">
          👁
        </button>
      </div>
    </div>

    <!-- LOGIN BTN -->
    <button class="btn-login" id="loginBtn" onclick="login()">
      <span id="btnLabel">Sign In →</span>
    </button>

    <!-- DIVIDER -->
    <div class="divider"><span>Don't have an account?</span></div>

    <!-- REGISTER -->
    <div class="register-row">
      <p><a href="/register">Create a free account</a></p>
    </div>

    <!-- SECURE BADGE -->
    <div class="secure-badge">
      🔒 Secured with CSRF protection
    </div>

  </div>
</div>

<script>
/* ── PASSWORD TOGGLE ── */
function togglePw(btn) {
  const inp = document.getElementById('password');
  const isHidden = inp.type === 'password';
  inp.type = isHidden ? 'text' : 'password';
  btn.textContent = isHidden ? '🙈' : '👁';
}

/* ── LOGIN ── */
async function login() {
  const email    = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const btn      = document.getElementById('loginBtn');
  const errBox   = document.getElementById('errorMsg');
  const errText  = document.getElementById('errorText');

  // hide old error
  errBox.classList.remove('show');

  // basic validation
  if (!email || !password) {
    errText.textContent = 'Please enter your email and password.';
    errBox.classList.add('show');
    return;
  }

  // loading state
  btn.disabled = true;
  document.getElementById('btnLabel').innerHTML =
    '<span class="dots"><span></span><span></span><span></span></span>';

  try {
    const res = await fetch("/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ email, password })
    });

    const data = await res.json();

    if (res.ok) {
      document.getElementById('btnLabel').textContent = '✓ Success!';
      btn.style.background = 'linear-gradient(135deg,#00e5a0,#00b87a)';
      setTimeout(() => { window.location = "/dashboard"; }, 500);
    } else {
      errText.textContent = data.message || 'Invalid email or password.';
      errBox.classList.add('show');
      btn.disabled = false;
      document.getElementById('btnLabel').textContent = 'Sign In →';
    }
  } catch (e) {
    errText.textContent = 'Connection error. Please try again.';
    errBox.classList.add('show');
    btn.disabled = false;
    document.getElementById('btnLabel').textContent = 'Sign In →';
  }
}
</script>
</body>
</html>
