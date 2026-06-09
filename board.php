<?php
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>จอแสดงคิว — Carwash Queue</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@500;700;800&family=JetBrains+Mono:wght@600;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <style>
    .num-board { font-family: 'JetBrains Mono', ui-monospace, monospace; }
    .ring-glow { box-shadow: 0 0 0 1px rgba(255,255,255,.08), 0 30px 80px -20px rgba(99,102,241,.45); }
  </style>
</head>
<body class="theme-dark min-h-screen p-4 sm:p-8">
  <div class="max-w-7xl mx-auto">
    <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-8">
      <div>
        <span class="chip" style="background:rgba(99,102,241,.18); color:#a5b4fc">
          <span class="chip-dot text-emerald-400"></span> LIVE
        </span>
        <h1 class="text-4xl sm:text-6xl font-extrabold mt-2 leading-tight">
          คิวล้างรถ<span class="bg-gradient-to-r from-indigo-400 to-cyan-300 bg-clip-text text-transparent">วันนี้</span>
        </h1>
        <p class="text-slate-400 mt-1">อัปเดตอัตโนมัติทุก 3 วินาที · <span id="boardTime" class="num"></span></p>
      </div>
      <a href="<?= BASE_URL ?>/" class="btn btn-ghost text-sm">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> กลับหน้าจองคิว
      </a>
    </header>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div class="glass-dark rounded-3xl p-6 sm:p-8 text-center ring-glow relative overflow-hidden">
        <div class="absolute inset-0 opacity-30 pointer-events-none"
             style="background:radial-gradient(400px 200px at 50% 0%, #6366f1 0%, transparent 70%)"></div>
        <div class="relative">
          <div class="flex items-center justify-center gap-2 text-xs uppercase tracking-[.2em] text-indigo-300">
            <i data-lucide="droplets" class="w-4 h-4"></i> กำลังล้าง
          </div>
          <div id="boardCurrent" class="num-board text-7xl sm:text-8xl font-extrabold mt-3 bg-gradient-to-b from-white to-indigo-200 bg-clip-text text-transparent">-</div>
        </div>
      </div>
      <div class="glass-dark rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden pulse-ring"
           style="border-color:rgba(245,158,11,.3)">
        <div class="absolute inset-0 opacity-30 pointer-events-none"
             style="background:radial-gradient(400px 200px at 50% 0%, #f59e0b 0%, transparent 70%)"></div>
        <div class="relative">
          <div class="flex items-center justify-center gap-2 text-xs uppercase tracking-[.2em] text-amber-300">
            <i data-lucide="bell-ring" class="w-4 h-4"></i> เรียกคิว
          </div>
          <div id="boardCalled" class="num-board text-7xl sm:text-8xl font-extrabold mt-3 bg-gradient-to-b from-white to-amber-200 bg-clip-text text-transparent">-</div>
        </div>
      </div>
      <div class="glass-dark rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 pointer-events-none"
             style="background:radial-gradient(400px 200px at 50% 0%, #06b6d4 0%, transparent 70%)"></div>
        <div class="relative">
          <div class="flex items-center justify-center gap-2 text-xs uppercase tracking-[.2em] text-cyan-300">
            <i data-lucide="hourglass" class="w-4 h-4"></i> รอเรียก
          </div>
          <div id="boardWaitingCount" class="num-board text-7xl sm:text-8xl font-extrabold mt-3 bg-gradient-to-b from-white to-cyan-200 bg-clip-text text-transparent">0</div>
        </div>
      </div>
    </section>

    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
      <span>คิวรอเรียก</span>
      <span class="h-px flex-1 bg-gradient-to-r from-slate-700 to-transparent"></span>
    </h2>
    <div id="boardWaitingList" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-3"></div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    const API = '<?= BASE_URL ?>/api/queue-list.php';

    function fmtTime() {
      const d = new Date();
      return d.toLocaleTimeString('th-TH', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }

    function refresh() {
      $('#boardTime').text(fmtTime());
      $.getJSON(API, (data) => {
        const queues = data.queues || [];
        const current = queues.find(q => q.status === 'in_progress');
        const called  = queues.find(q => q.status === 'called');
        const waiting = queues.filter(q => q.status === 'waiting');

        $('#boardCurrent').text(current ? current.queue_number : '—');
        $('#boardCalled').text(called ? called.queue_number : '—');
        $('#boardWaitingCount').text(waiting.length);

        $('#boardWaitingList').html(
          waiting.length === 0
            ? '<div class="col-span-full text-center text-slate-500 py-12 glass-dark rounded-2xl">ไม่มีคิวรอ</div>'
            : waiting.map((q, i) => `
                <div class="glass-dark rounded-2xl p-4 text-center float-in" style="animation-delay:${i*30}ms">
                  <div class="num-board text-3xl sm:text-4xl font-extrabold bg-gradient-to-b from-white to-slate-300 bg-clip-text text-transparent">${q.queue_number}</div>
                  <div class="text-[11px] text-slate-400 mt-1 truncate">${q.service_name}</div>
                </div>`).join('')
        );
        if (window.lucide) lucide.createIcons();
      });
    }
    refresh();
    setInterval(refresh, 3000);
  </script>
  <script>if (window.lucide) lucide.createIcons();</script>
</body>
</html>
