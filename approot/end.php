<!DOCTYPE html>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<?php
  function interpolate_color($corA, $corB, $lerp) {
    $redA = $corA & 0xFF0000;
    $greenA = $corA & 0x00FF00;
    $blueA = $corA & 0x0000FF;
    $redB = $corB & 0xFF0000;
    $greenB = $corB & 0x00FF00;
    $blueB = $corB & 0x0000FF;

    $redC = $redA + (($redB - $redA) * $lerp) & 0xFF0000;         // Only Red
    $greenC = $greenA + (($greenB - $greenA) * $lerp) & 0x00FF00; // Only Green
    $blueC = $blueA + (($blueB - $blueA) * $lerp) & 0x0000FF;     // Only Blue

    $result = dechex($redC | $greenC | $blueC);
    return "#".str_pad($result, 6, "0", STR_PAD_LEFT);
  }

  include 'triviadb.php';

  $session_id = $_GET["session_id"];
  $name = $triviadb->get_name($session_id);
  $session = $triviadb->get_session($session_id);

  function callback($acc, $question) { 
    return ($question["player_answer"] == $question["correct_answer"]) ? $acc + 1 : $acc;
  }

  $factor =  array_reduce($session, "callback", 0) / sizeof($session);
  $score = $factor * 100;

  $color = interpolate_color(0xff0000, 0x00ff00, $factor);
?>

<body style="background-image: linear-gradient(to bottom right, <?php echo $color; ?>, white)">
  <div id="main" class="large">
    <h1><?php echo $name; ?>,</h1>
    <center>
      <h2>Congratulations, you made it! 🥳</h2>
      <p>You answered <strong><?php echo $score; ?>%</strong> of the questions correctly.</p>
    </center>

    <div class="content">
      <table class="recap">
        <tr>
          <th>Question</th>
          <th>Your answer</th>
          <th>Correct answer</th>
          <th></th>
        </tr>
        <?php
          foreach ($session as $question) {
            $correct = $question["player_answer"] == $question["correct_answer"];
            $emoji = $correct ? '👌' : '😔';

            echo <<<END
              <tr>
                <td>{$question["question"]}</td>
                <td>{$question["player_answer"]}</td>
                <td>{$question["correct_answer"]}</td>
                <td>{$emoji}</td>
              </tr>
          END;
          }
        ?>
      </table>
    </div>
    <small><?php echo $question['category']; ?> /
      <?php echo ucfirst($question['difficulty']); ?>
    </small>
  </div>
</body>