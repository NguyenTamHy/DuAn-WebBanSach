<?php require_once 'includes/session.php'; require_login(); ?>
<?php include 'includes/header.php'; ?>
<h2>Thông tin cá nhân</h2>
<?php if(isset($_GET['update'])): ?><p style="color:green">Cập nhật thành công</p><?php endif; ?>
<form method="post" action="controllers/ProfileController.php">
  <label>Họ tên</label><br>
  <input type="text" name="fullname" value="<?= htmlspecialchars($_SESSION['user']['fullname'] ?? '') ?>"><br>
  <label>Địa chỉ</label><br>
  <input type="text" name="address" value="<?= htmlspecialchars($_SESSION['user']['address'] ?? '') ?>"><br>
  <label>Phone</label><br>
  <input type="text" name="phone" value="<?= htmlspecialchars($_SESSION['user']['phone'] ?? '') ?>"><br>
  <button type="submit" name="update_profile">Cập nhật</button>
</form>
<?php include 'includes/footer.php'; ?>
