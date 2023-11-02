<!DOCTYPE html>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<body>
  <?php
    include 'triviadb.php';

    $sessions = $triviadb->get_sessions(50);
  ?>
  <div id="main" class="large">
    <h1>🧠 Trivia Quiz - Score Board</h1>

    <div class="content">
    <table class="recap">
        <tr>
          <th>Player</th>
          <th>Difficulty</th>
          <th>Score</th>
        </tr>
        <?php
          foreach ($sessions as $session) {
            $difficulty = ucfirst($session["difficulty"]);
            $score = number_format($session["score"]);

            echo <<<END
              <tr>
                <td>{$session["name"]}</td>
                <td>{$difficulty}</td>
                <td>{$score}</td>
              </tr>
          END;
          }
        ?>
      </table>
    </div>
  </div>
</body>
</html>
