<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'จองคิวล้างรถ';

$services = db()->query("SELECT * FROM services WHERE is_active=1 ORDER BY id")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<div class="grid lg:grid-cols-5 gap-6 items-start">
  <!-- Hero / pitch -->
  <section class="lg:col-span-2 float-in">
    <span class="chip"><span class="chip-dot text-indigo-500"></span>เปิดให้บริการวันนี้</span>
    <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mt-3">
      จองคิวล้างรถ<br>
      <span class="bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text text-transparent">
        ไม่ต้องรอนาน
      </span>
    </h1>
    <p class="text-slate-600 mt-4 text-lg">
      จองออนไลน์ใน 30 วินาที รับเลขคิว ติดตามสถานะแบบเรียลไทม์
      ไม่ต้องเสียเวลายืนรอที่ร้าน
    </p>

    <div class="grid grid-cols-3 gap-3 mt-6">
      <div class="glass glass-card !p-4 text-center">
        <i data-lucide="zap" class="w-6 h-6 mx-auto text-indigo-600"></i>
        <div class="text-xs text-slate-600 mt-2 font-medium">จองเร็ว</div>
      </div>
      <div class="glass glass-card !p-4 text-center">
        <i data-lucide="bell-ring" class="w-6 h-6 mx-auto text-cyan-600"></i>
        <div class="text-xs text-slate-600 mt-2 font-medium">เรียลไทม์</div>
      </div>
      <div class="glass glass-card !p-4 text-center">
        <i data-lucide="check-circle-2" class="w-6 h-6 mx-auto text-emerald-600"></i>
        <div class="text-xs text-slate-600 mt-2 font-medium">ใช้งานง่าย</div>
      </div>
    </div>

    <a href="<?= BASE_URL ?>/board.php" class="btn btn-ghost mt-6 w-full sm:w-auto">
      <i data-lucide="monitor" class="w-4 h-4"></i> ดูคิวปัจจุบัน
    </a>
  </section>

  <!-- Booking form -->
  <section class="lg:col-span-3 glass glass-card float-in" style="animation-delay:.1s">
    <div class="flex items-center gap-3 mb-5">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white"
           style="background:linear-gradient(135deg,#6366f1,#06b6d4)">
        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold">จองคิวล้างรถ</h2>
        <p class="text-xs text-slate-500">กรอกข้อมูลเพื่อรับเลขคิว</p>
      </div>
    </div>

    <form id="bookingForm" class="space-y-4">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

      <div>
        <label class="label">ชื่อ-นามสกุล <span class="text-rose-500">*</span></label>
        <input name="customer_name" required class="field" placeholder="ชื่อลูกค้า">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="label">เบอร์โทร</label>
          <input name="phone" class="field" placeholder="08x-xxx-xxxx" inputmode="tel">
        </div>
        <div>
          <label class="label">ทะเบียนรถ</label>
          <input name="plate" class="field" placeholder="กก-1234">
        </div>
      </div>

      <div>
        <label class="label">ประเภทรถ</label>
        <select name="vehicle_type" class="field">
          <option value="sedan">รถเก๋ง</option>
          <option value="suv">SUV</option>
          <option value="pickup">กระบะ</option>
          <option value="van">รถตู้</option>
          <option value="motorcycle">มอเตอร์ไซค์</option>
        </select>
      </div>

      <div>
        <label class="label">บริการ <span class="text-rose-500">*</span></label>
        <select name="service_id" required class="field">
          <?php foreach ($services as $s): ?>
            <option value="<?= e((string)$s['id']) ?>">
              <?= e($s['name']) ?> — <?= number_format($s['price'], 0) ?> ฿ (<?= (int)$s['duration_minutes'] ?> นาที)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="label">หมายเหตุ</label>
        <textarea name="note" rows="2" class="field" placeholder="แจ้งรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
      </div>

      <button class="btn btn-primary w-full text-base">
        จองคิวเลย <i data-lucide="arrow-right" class="w-5 h-5"></i>
      </button>
    </form>

    <div id="result" class="mt-6 hidden"></div>
  </section>
</div>

<script>window.BASE_URL_PHP = '<?= BASE_URL ?>';</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
