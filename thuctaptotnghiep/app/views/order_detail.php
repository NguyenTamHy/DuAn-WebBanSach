<?php
// public/order_detail.php
declare(strict_types=1);
require_once __DIR__ . '/../app/helpers.php';

if (!isset($order)) {
    echo "<main class='wrap'><h2>Đơn không tồn tại</h2></main>";
    return;
}
$addr = json_decode($order['addr_json'] ?? '{}', true) ?: [];
?>
<section class="wrap">
  <h2>Đơn hàng <?= e($order['code']) ?></h2>
  <p>Trạng thái: <strong><?= e($order['status']) ?></strong> • Ngày: <?= e($order['created_at']) ?></p>
  <h3>Thông tin giao hàng</h3>
  <p><?= nl2br(e($addr['line1'] ?? '')) ?> <br> SĐT: <?= e($addr['phone'] ?? '') ?></p>

  <h3>Chi tiết đơn</h3>
  <table class="cart-table">
    <thead><tr><th>Sách</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
    <tbody>
    <?php foreach($order_items as $it): ?>
      <tr>
        <td><?= e($it['title_snapshot']) ?></td>
        <td><?= (int)$it['qty'] ?></td>
        <td><?= money($it['unit_price']) ?></td>
        <td><?= money($it['line_total']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <p>Tạm tính: <?= money($order['subtotal']) ?></p>
  <p>Giảm: <?= money($order['discount']) ?></p>
  <p>Phí vận chuyển: <?= money($order['shipping_fee']) ?></p>
  <h3>Tổng: <?= money($order['total']) ?></h3>
</section>
