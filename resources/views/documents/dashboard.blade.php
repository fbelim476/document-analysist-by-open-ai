<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DocMind — AI Document Analyzer</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0a0b0f;
    --surface: #111318;
    --surface2: #181c24;
    --border: rgba(255,255,255,0.07);
    --accent: #00e5a0;
    --accent2: #0077ff;
    --accent-glow: rgba(0,229,160,0.15);
    --text: #f0f2f7;
    --muted: #6b7385;
    --danger: #ff4d6d;
    --warning: #ffb340;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    overflow-x: hidden;
  }

  /* ─── NOISE OVERLAY ─── */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 0;
    opacity: 0.4;
  }

  /* ─── GLOW BLOB ─── */
  .glow-blob {
    position: fixed;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(0,229,160,0.06) 0%, transparent 70%);
    top: -200px; left: -200px;
    pointer-events: none;
    z-index: 0;
    animation: blobFloat 12s ease-in-out infinite;
  }
  .glow-blob2 {
    position: fixed;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(0,119,255,0.05) 0%, transparent 70%);
    bottom: -150px; right: -150px;
    pointer-events: none;
    z-index: 0;
    animation: blobFloat 16s ease-in-out infinite reverse;
  }
  @keyframes blobFloat {
    0%,100% { transform: translate(0,0); }
    50% { transform: translate(60px, 40px); }
  }

  /* ─── LAYOUT ─── */
  .shell {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px 60px;
  }

  /* ─── TOPBAR ─── */
  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 0 20px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 40px;
  }
  .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Syne', sans-serif;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.5px;
  }
  .logo-icon {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    box-shadow: 0 0 20px var(--accent-glow);
  }
  .logo span { color: var(--accent); }

  .btn-logout {
    display: flex; align-items: center; gap: 7px;
    padding: 8px 18px;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--muted);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-logout:hover {
    border-color: var(--danger);
    color: var(--danger);
    background: rgba(255,77,109,0.06);
  }
  .btn-logout svg { width: 14px; height: 14px; }

  /* ─── GRID ─── */
  .grid {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 24px;
    align-items: start;
  }

  /* ─── PANEL ─── */
  .panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
  }
  .panel-header {
    padding: 18px 22px 16px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
  }
  .panel-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
  }
  .panel-icon.green { background: rgba(0,229,160,0.12); }
  .panel-icon.blue  { background: rgba(0,119,255,0.12); }
  .panel-icon.gold  { background: rgba(255,179,64,0.12); }
  .panel-title {
    font-family: 'Syne', sans-serif;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.3px;
  }
  .panel-body { padding: 22px; }

  /* ─── UPLOAD ZONE ─── */
  .upload-zone {
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 32px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s;
    position: relative;
  }
  .upload-zone:hover, .upload-zone.dragover {
    border-color: var(--accent);
    background: var(--accent-glow);
  }
  .upload-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%;
  }
  .upload-icon { font-size: 36px; margin-bottom: 10px; }
  .upload-label {
    font-size: 13px;
    color: var(--muted);
    line-height: 1.6;
  }
  .upload-label strong { color: var(--accent); }
  .file-name-display {
    margin-top: 10px;
    font-size: 12px;
    color: var(--accent);
    font-weight: 500;
    min-height: 18px;
  }

  .btn-upload {
    width: 100%;
    margin-top: 16px;
    padding: 12px;
    background: linear-gradient(135deg, var(--accent), #00b87a);
    border: none;
    border-radius: 10px;
    color: #0a0b0f;
    font-family: 'Syne', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 20px rgba(0,229,160,0.25);
  }
  .btn-upload:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 28px rgba(0,229,160,0.35);
  }
  .btn-upload:active { transform: translateY(0); }
  .btn-upload:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

  /* ─── DOCUMENT LIST ─── */
  .doc-list { display: flex; flex-direction: column; gap: 8px; }

  .doc-card {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: border-color 0.2s;
    animation: fadeSlideIn 0.3s ease forwards;
    opacity: 0;
  }
  .doc-card:hover { border-color: rgba(255,255,255,0.15); }

  @keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .doc-file-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    background: rgba(0,119,255,0.1);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
  }
  .doc-info { flex: 1; min-width: 0; }
  .doc-name {
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .doc-meta { font-size: 11px; color: var(--muted); margin-top: 2px; }

  .doc-actions { display: flex; gap: 6px; flex-shrink: 0; }

  .btn-sm {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    border: 1px solid var(--border);
    background: transparent;
    color: var(--muted);
    transition: all 0.18s;
    display: flex; align-items: center; gap: 4px;
  }
  .btn-sm.analyze {
    color: var(--accent);
    border-color: rgba(0,229,160,0.25);
  }
  .btn-sm.analyze:hover {
    background: rgba(0,229,160,0.1);
    border-color: var(--accent);
    color: var(--accent);
  }
  .btn-sm.download:hover {
    background: rgba(0,119,255,0.1);
    border-color: var(--accent2);
    color: var(--accent2);
  }
  .btn-sm.view-analysis {
    color: var(--warning);
    border-color: rgba(255,179,64,0.25);
  }
  .btn-sm.view-analysis:hover {
    background: rgba(255,179,64,0.1);
    border-color: var(--warning);
  }

  /* ─── ANALYZED BADGE ─── */
  .badge {
    display: inline-block;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 2px 7px;
    border-radius: 20px;
    background: rgba(0,229,160,0.1);
    color: var(--accent);
    border: 1px solid rgba(0,229,160,0.2);
  }

  /* ─── RIGHT PANEL: ANALYSIS ─── */
  .analysis-panel { position: sticky; top: 24px; }

  .analysis-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 30px;
    text-align: center;
    gap: 14px;
  }
  .analysis-empty-icon { font-size: 48px; opacity: 0.3; }
  .analysis-empty p { color: var(--muted); font-size: 13px; line-height: 1.7; }

  .analysis-result {
    display: none;
    padding: 24px;
  }
  .analysis-result.active { display: block; }

  .analysis-filename {
    font-family: 'Syne', sans-serif;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 4px;
  }
  .analysis-subtitle {
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 6px;
  }
  .dot { width: 5px; height: 5px; border-radius: 50%; background: var(--accent); }

  .analysis-body {
    font-size: 13.5px;
    line-height: 1.85;
    color: #c8cdd8;
    white-space: pre-wrap;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    max-height: 520px;
    overflow-y: auto;
  }
  .analysis-body::-webkit-scrollbar { width: 4px; }
  .analysis-body::-webkit-scrollbar-track { background: transparent; }
  .analysis-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

  /* ─── LOADING ─── */
  .loading-state {
    display: none;
    padding: 60px 30px;
    text-align: center;
  }
  .loading-state.active { display: block; }

  .spinner {
    width: 40px; height: 40px;
    border: 2px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 16px;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  .loading-text { color: var(--muted); font-size: 13px; }
  .loading-text span {
    color: var(--accent);
    font-weight: 500;
  }

  /* ─── EMPTY STATES ─── */
  .empty-list {
    text-align: center;
    padding: 30px 20px;
    color: var(--muted);
    font-size: 12px;
  }
  .empty-list span { display: block; font-size: 24px; margin-bottom: 8px; opacity: 0.5; }

  /* ─── TOAST ─── */
  #toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 20px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    z-index: 9999;
    transform: translateY(80px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    max-width: 320px;
  }
  #toast.show { transform: translateY(0); opacity: 1; }
  #toast.success { border-color: rgba(0,229,160,0.3); }
  #toast.error   { border-color: rgba(255,77,109,0.3); }
  .toast-icon { font-size: 18px; }

  /* ─── SECTION LABEL ─── */
  .section-count {
    margin-left: auto;
    font-size: 11px;
    color: var(--muted);
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2px 10px;
    font-weight: 500;
  }

  /* ─── TAB SWITCHER ─── */
  .tab-switcher {
    display: flex;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 16px;
    gap: 4px;
  }
  .tab-btn {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 7px;
    background: transparent;
    color: var(--muted);
    font-family: 'Syne', sans-serif;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    letter-spacing: 0.3px;
  }
  .tab-btn.active {
    background: var(--surface);
    color: var(--text);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }
  .tab-btn.active.pdf-tab  { color: var(--accent2); }
  .tab-btn.active.text-tab { color: var(--accent);  }

  .tab-pane { display: none; }
  .tab-pane.active { display: block; }

  /* ─── TEXT INPUT ─── */
  .text-input-wrap {
    position: relative;
  }
  .text-title-input {
    width: 100%;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px 14px;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    margin-bottom: 10px;
    outline: none;
    transition: border-color 0.2s;
  }
  .text-title-input:focus { border-color: var(--accent); }
  .text-title-input::placeholder { color: var(--muted); }

  .text-area-input {
    width: 100%;
    min-height: 140px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 14px;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    line-height: 1.7;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s;
  }
  .text-area-input:focus { border-color: var(--accent); }
  .text-area-input::placeholder { color: var(--muted); }
  .char-count {
    text-align: right;
    font-size: 11px;
    color: var(--muted);
    margin-top: 6px;
  }
  .char-count.warn { color: var(--warning); }

  .btn-upload-text {
    width: 100%;
    margin-top: 12px;
    padding: 12px;
    background: linear-gradient(135deg, var(--accent2), #0055cc);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-family: 'Syne', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 20px rgba(0,119,255,0.25);
  }
  .btn-upload-text:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 28px rgba(0,119,255,0.35);
  }
  .btn-upload-text:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

  /* ─── SCROLLBAR ─── */
  ::-webkit-scrollbar { width: 5px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

  /* ─── RESPONSIVE ─── */
  @media (max-width: 860px) {
    .grid { grid-template-columns: 1fr; }
    .analysis-panel { position: static; }
  }
</style>
</head>
<body>

<div class="glow-blob"></div>
<div class="glow-blob2"></div>

<div class="shell">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="logo">
      <div class="logo-icon">🧠</div>
      Doc<span>Mind</span>
    </div>
    <button class="btn-logout" onclick="logout()">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
      </svg>
      Logout
    </button>
  </div>

  <!-- MAIN GRID -->
  <div class="grid">

    <!-- LEFT COLUMN -->
    <div style="display:flex; flex-direction:column; gap:20px;">

      <!-- UPLOAD PANEL -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-icon green">⬆</div>
          <div class="panel-title">Add Document</div>
        </div>
        <div class="panel-body">

          <!-- TAB SWITCHER -->
          <div class="tab-switcher">
            <button class="tab-btn pdf-tab active" onclick="switchTab('pdf')">
              📄 Upload PDF / File
            </button>
            <button class="tab-btn text-tab" onclick="switchTab('text')">
              ✏️ Type / Paste Text
            </button>
          </div>

          <!-- TAB: PDF UPLOAD -->
          <div class="tab-pane active" id="tab-pdf">
            <div class="upload-zone" id="dropZone">
              <input type="file" id="fileInput" name="file" onchange="onFileChange(this)">
              <div class="upload-icon">📄</div>
              <div class="upload-label">
                <strong>Click to browse</strong> or drag &amp; drop<br>
                PDF, DOCX, TXT supported
              </div>
              <div class="file-name-display" id="fileNameDisplay"></div>
            </div>
            <button class="btn-upload" id="uploadBtn" onclick="uploadDocument()" disabled>
              ⬆ &nbsp;Upload Document
            </button>
          </div>

          <!-- TAB: TEXT INPUT -->
          <div class="tab-pane" id="tab-text">
            <input
              type="text"
              class="text-title-input"
              id="textTitle"
              placeholder="Document title (e.g. My Notes, Report Summary…)"
              oninput="checkTextReady()"
            >
            <textarea
              class="text-area-input"
              id="textContent"
              placeholder="Type or paste your text here…&#10;&#10;AI will analyze this content just like a PDF document."
              oninput="onTextInput(this)"
            ></textarea>
            <div class="char-count" id="charCount">0 characters</div>
            <button class="btn-upload-text" id="uploadTextBtn" onclick="uploadText()" disabled>
              💾 &nbsp;Save &amp; Submit Text
            </button>
          </div>

        </div>
      </div>

      <!-- DOCUMENTS PANEL -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-icon blue">📂</div>
          <div class="panel-title">My Documents</div>
          <div class="section-count" id="docCount">0</div>
        </div>
        <div class="panel-body" style="padding:14px;">
          <div class="doc-list" id="documents">
            <div class="empty-list"><span>📭</span>No documents yet</div>
          </div>
        </div>
      </div>

      <!-- ANALYZED PANEL -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-icon gold">✨</div>
          <div class="panel-title">Analyzed Documents</div>
          <div class="section-count" id="analyzedCount">0</div>
        </div>
        <div class="panel-body" style="padding:14px;">
          <div class="doc-list" id="analyzedDocuments">
            <div class="empty-list"><span>🔍</span>No analyses yet</div>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: ANALYSIS -->
    <div class="panel analysis-panel" style="min-height:500px;">
      <div class="panel-header">
        <div class="panel-icon green">🤖</div>
        <div class="panel-title">AI Analysis</div>
      </div>

      <!-- Loading -->
      <div class="loading-state" id="loadingState">
        <div class="spinner"></div>
        <div class="loading-text">Analyzing document with <span>AI</span>…<br>This may take a moment.</div>
      </div>

      <!-- Empty -->
      <div class="analysis-empty" id="analysisEmpty">
        <div class="analysis-empty-icon">🔍</div>
        <p>Select a document and click <strong style="color:var(--accent)">Analyze</strong><br>to see AI-powered insights here.</p>
      </div>

      <!-- Result -->
      <div class="analysis-result" id="analysisResult">
        <div class="analysis-filename" id="resultFilename">—</div>
        <div class="analysis-subtitle">
          <div class="dot"></div>
          <span id="resultMeta">AI Analysis Complete</span>
        </div>
        <div class="analysis-body" id="resultBody"></div>
      </div>

    </div>

  </div>
</div>

<!-- TOAST -->
<div id="toast">
  <span class="toast-icon" id="toastIcon">✅</span>
  <span id="toastMsg">Message</span>
</div>

<script>
const API = "/api";

/* ─── TAB SWITCH ─── */
function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  document.querySelector('.' + tab + '-tab').classList.add('active');
}

/* ─── TEXT HELPERS ─── */
function onTextInput(el) {
  const len = el.value.length;
  const cc = document.getElementById('charCount');
  cc.textContent = len.toLocaleString() + ' characters';
  cc.className = 'char-count' + (len > 4000 ? ' warn' : '');
  checkTextReady();
}
function checkTextReady() {
  const title = document.getElementById('textTitle').value.trim();
  const content = document.getElementById('textContent').value.trim();
  document.getElementById('uploadTextBtn').disabled = !(title && content);
}

/* ─── UPLOAD TEXT ─── */
async function uploadText() {
  const title = document.getElementById('textTitle').value.trim();
  const content = document.getElementById('textContent').value.trim();
  if (!title || !content) return;
  const btn = document.getElementById('uploadTextBtn');
  btn.disabled = true;
  btn.innerHTML = '⏳ &nbsp;Saving…';
  try {
    const blob = new Blob([content], { type: 'text/plain' });
    const file = new File([blob], title.endsWith('.txt') ? title : title + '.txt', { type: 'text/plain' });
    const formData = new FormData();
    formData.append('file', file);
    const res = await fetch(API + "/documents", {
      method: "POST",
      headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: formData
    });
    const data = await res.json();
    showToast(data.message || 'Text saved as document!', 'success');
    document.getElementById('textTitle').value = '';
    document.getElementById('textContent').value = '';
    document.getElementById('charCount').textContent = '0 characters';
    btn.disabled = true;
    loadDocuments();
  } catch(e) {
    showToast('Failed to save. Try again.', 'error');
    btn.disabled = false;
  }
  btn.innerHTML = '💾 &nbsp;Save &amp; Submit Text';
}


/* ─── TOAST ─── */
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  const icon = { success: '✅', error: '❌', info: 'ℹ️' };
  document.getElementById('toastIcon').textContent = icon[type] || '✅';
  document.getElementById('toastMsg').textContent = msg;
  t.className = `show ${type}`;
  setTimeout(() => t.className = '', 3000);
}

/* ─── FILE PICK ─── */
function onFileChange(input) {
  const display = document.getElementById('fileNameDisplay');
  const btn = document.getElementById('uploadBtn');
  if (input.files.length) {
    display.textContent = '📎 ' + input.files[0].name;
    btn.disabled = false;
  } else {
    display.textContent = '';
    btn.disabled = true;
  }
}

/* Drag & drop */
const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
dz.addEventListener('drop', e => {
  e.preventDefault();
  dz.classList.remove('dragover');
  const fi = document.getElementById('fileInput');
  fi.files = e.dataTransfer.files;
  onFileChange(fi);
});

/* ─── UPLOAD ─── */
async function uploadDocument() {
  const fi = document.getElementById('fileInput');
  if (!fi.files.length) return;
  const btn = document.getElementById('uploadBtn');
  btn.disabled = true;
  btn.textContent = 'Uploading…';
  try {
    const formData = new FormData();
    formData.append('file', fi.files[0]);
    const res = await fetch(API + "/documents", {
      method: "POST",
      headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: formData
    });
    const data = await res.json();
    showToast(data.message || 'Document uploaded!', 'success');
    fi.value = '';
    document.getElementById('fileNameDisplay').textContent = '';
    loadDocuments();
  } catch (e) {
    showToast('Upload failed. Try again.', 'error');
  }
  btn.disabled = false;
  btn.innerHTML = '⬆ &nbsp;Upload Document';
}

/* ─── LOAD DOCUMENTS ─── */
async function loadDocuments() {
  try {
    const res = await fetch(API + "/documents");
    const data = await res.json();
    const docs = data.documents || [];
    const container = document.getElementById('documents');
    document.getElementById('docCount').textContent = docs.length;

    if (!docs.length) {
      container.innerHTML = '<div class="empty-list"><span>📭</span>No documents yet</div>';
      return;
    }

    container.innerHTML = docs.map((doc, i) => `
      <div class="doc-card" style="animation-delay:${i * 0.05}s">
        <div class="doc-file-icon">📄</div>
        <div class="doc-info">
          <div class="doc-name">${escHtml(doc.original_name)}</div>
          <div class="doc-meta">ID #${doc.id}</div>
        </div>
        <div class="doc-actions">
          <button class="btn-sm analyze" onclick="analyze(${doc.id}, '${escHtml(doc.original_name)}')">
            🤖 Analyze
          </button>
          <button class="btn-sm download" onclick="download(${doc.id})">
            ⬇
          </button>
        </div>
      </div>
    `).join('');
  } catch(e) {}
}

/* ─── ANALYZE ─── */
async function analyze(id, name) {
  document.getElementById('analysisEmpty').style.display = 'none';
  document.getElementById('analysisResult').className = 'analysis-result';
  document.getElementById('loadingState').className = 'loading-state active';

  try {
    const res = await fetch(API + "/documents/" + id + "/analyze", {
      method: "POST",
      headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
    });
    const data = await res.json();

    document.getElementById('loadingState').className = 'loading-state';
    document.getElementById('resultFilename').textContent = name || 'Document';
    document.getElementById('resultMeta').textContent = 'AI Analysis Complete · ' + new Date().toLocaleTimeString();
    document.getElementById('resultBody').textContent = data.result || 'No result returned.';
    document.getElementById('analysisResult').className = 'analysis-result active';
    showToast('Analysis complete!', 'success');
    loadAnalyzedDocuments();
  } catch(e) {
    document.getElementById('loadingState').className = 'loading-state';
    document.getElementById('analysisEmpty').style.display = 'flex';
    showToast('Analysis failed. Try again.', 'error');
  }
}

/* ─── LOAD ANALYZED ─── */
async function loadAnalyzedDocuments() {
  try {
    const res = await fetch(API + "/analyzed-documents");
    const data = await res.json();
    const docs = data.analyzed_documents || [];
    const container = document.getElementById('analyzedDocuments');
    document.getElementById('analyzedCount').textContent = docs.length;

    if (!docs.length) {
      container.innerHTML = '<div class="empty-list"><span>🔍</span>No analyses yet</div>';
      return;
    }

    container.innerHTML = docs.map((doc, i) => `
      <div class="doc-card" style="animation-delay:${i * 0.05}s">
        <div class="doc-file-icon" style="background:rgba(255,179,64,0.1);">✨</div>
        <div class="doc-info">
          <div class="doc-name">${escHtml(doc.original_name)}</div>
          <div class="doc-meta"><span class="badge">Analyzed</span></div>
        </div>
        <div class="doc-actions">
          <button class="btn-sm view-analysis" onclick="showAnalysis(${doc.id})">
            👁 View
          </button>
        </div>
      </div>
    `).join('');
  } catch(e) {}
}

/* ─── SHOW SAVED ANALYSIS ─── */
async function showAnalysis(id) {
  try {
    const res = await fetch(API + "/analyzed-documents");
    const data = await res.json();
    const doc = data.analyzed_documents.find(d => d.id == id);
    if (!doc) return;

    document.getElementById('analysisEmpty').style.display = 'none';
    document.getElementById('loadingState').className = 'loading-state';
    document.getElementById('resultFilename').textContent = doc.original_name;
    document.getElementById('resultMeta').textContent = 'Saved Analysis';
    document.getElementById('resultBody').textContent = doc.analysis;
    document.getElementById('analysisResult').className = 'analysis-result active';
  } catch(e) {}
}

/* ─── DOWNLOAD ─── */
function download(id) {
  window.open(API + "/documents/" + id + "/download");
}

/* ─── LOGOUT ─── */
async function logout() {
  try {
    await fetch(API + "/logout", {
      method: "POST",
      headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
    });
  } finally {
    window.location = "/";
  }
}

/* ─── UTILS ─── */
function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str || ''));
  return d.innerHTML;
}

/* ─── INIT ─── */
loadDocuments();
loadAnalyzedDocuments();
</script>
</body>
</html>
