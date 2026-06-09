<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'สถานะคิวของฉัน';
include __DIR__ . '/includes/header.php';
$number = trim($_GET['number'] ?? '');
?>

<div class="max-w-lg mx-auto float-in">
  <div class="text-center mb-6">
    <span class="chip"><span class="chip-dot text-cyan-500"></span>ตรวจสอบสถานะ</span>
    <h1 class="text-3xl sm:text-4xl font-extrabold mt-3">
      <span class="bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text text-transparent">สถานะคิวของฉัน</span>
    </h1>
    <p class="text-slate-600 mt-2 text-sm">กรอกเลขคิวเพื่อดูสถานะแบบเรียลไทม์</p>
  </div>

  <div class="glass glass-card">
    <form id="checkForm" class="flex gap-2 mb-5">
      <input id="qNumber" name="number" value="<?= e($number) ?>" required
             class="field flex-1 text-lg font-semibold tracking-wider uppercase"
             placeholder="เช่น A001">
      <button class="btn btn-primary px-5">
        <i data-lucide="search" class="w-4 h-4"></i> ค้นหา
      </button>
    </form>

    <div id="qResult" class="hidden">
      <div class="text-center">
        <div class="text-xs text-slate-500 uppercase tracking-wider">เลขคิวของคุณ</div>
        <div id="qNum" class="num text-7xl font-extrabold my-3 bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text text-transparent"></div>
        <div id="qStatus" class="chip"></div>
      </div>
      <div class="grid grid-cols-2 gap-3 mt-6 text-sm">
        <div class="glass !p-4 rounded-xl">
          <div class="text-slate-500 text-xs">บริการ</div>
          <div id="qService" class="font-semibold mt-1"></div>
        </div>
        <div class="glass !p-4 rounded-xl">
          <div class="text-slate-500 text-xs">คิวก่อนหน้า</div>
          <div class="font-semibold mt-1"><span id="qAhead" class="num">0</span> คิว</div>
        </div>
        <div class="col-span-2 glass !p-5 rounded-xl text-center"
             style="background:linear-gradient(135deg, rgba(99,102,241,.10), rgba(6,182,212,.10))">
          <div class="text-slate-500 text-xs uppercase tracking-wider">เวลารอโดยประมาณ</div>
          <div class="mt-1"><span id="qEta" class="font-extrabold text-4xl num">0</span>
            <span class="text-slate-600 ml-1">นาที</span></div>
        </div>
      </div>
    </div>

    <p id="qError" class="text-rose-600 mt-3 hidden text-sm"></p>
  </div>
</div>

<script>
  const QSTATUS_API = '<?= BASE_URL ?>/api/queue-status.php';
  let timer;

  function check() {
    const num = $('#qNumber').val().trim();
    if (!num) return;
    $.getJSON(QSTATUS_API, { number: num })
      .done((d) => {
        $('#qError').hide();
        $('#qResult').removeClass('hidden');
        $('#qNum').text(d.queue_number);
        $('#qStatus').text(d.status_label).attr('class', 'chip status-' + d.status);
        $('#qService').text(d.service_name);
        $('#qAhead').text(d.ahead);
        $('#qEta').text(d.estimated_minutes);
      })
      .fail((x) => {
        $('#qResult').addClass('hidden');
        $('#qError').text(x.responseJSON?.error || 'เกิดข้อผิดพลาด').show();
      });
  }

  $('#checkForm').on('submit', (e) => { e.preventDefault(); check(); });
  if ($('#qNumber').val()) {
    check();
    timer = setInterval(check, 5000);
  }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
