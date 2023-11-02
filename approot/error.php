<!DOCTYPE html>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<body>
  <div id="main">
    <h1>Oops 🤭</h1>

    <div class="content">
      <p>An error occurred:</p>
      <h2><?php echo $_GET["error"]; ?></h2>
    </div>
    <small>
      <a href="/scoreboard.php">Score Board</a>
    </small>
  </div>
</body>