<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="../assets/css/style.css">
  <title>Register</title>
</head>
<body>

<div class="box">
  <h2>Đăng ký</h2>

  <form action="../actions/register_action.php" method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
  </form>

  <br>
  <a href="login.php">Đã có tài khoản? Login</a>
</div>

</body>
</html>