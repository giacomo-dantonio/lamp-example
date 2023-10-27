<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LAMP Trivia</title>
</head>
<body>
  <?php
    include 'triviadb.php';

    $session_id = $_GET["session_id"];
    $name = $triviadb->get_name($session_id);
    $session = $triviadb->get_session($session_id);

    function callback($acc, $question) { 
      return ($question["player_answer"] == $question["correct_answer"]) ? $acc + 1 : $acc;
    }

    $score = array_reduce($session, "callback", 0) * 100 / sizeof($session);
  ?>
  <h1><?php echo $name; ?>: Congratulations, you made it!</h1>

  <h2>You answered <?php echo $score; ?>% of the questions correctly.</h2>

  <table>
    <tr>
      <th>Question</th>
      <th>Your answer</th>
      <th>Correct answer</th>
    </tr>
    <?php
      foreach ($session as $question) {
        echo <<<END
          <tr>
            <td>{$question["question"]}</td>
            <td>{$question["player_answer"]}</td>
            <td>{$question["correct_answer"]}</td>
          </tr>
      END;
      }
    ?>
  </table>
</body>