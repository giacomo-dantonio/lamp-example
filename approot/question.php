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
    $sort = $_GET["question"];

    $name = $triviadb->get_name($session_id);
    $question = $triviadb->get_question($session_id, $sort);
    $answers = $triviadb->get_answers($question["question_id"]);

    switch ($sort) {
      case 0:
        $question_nr = "1st";
        break;
      case 1:
        $question_nr = "2nd";
        break;
      case 2:
        $question_nr = "3rd";
        break;
      default:
        $nr = $sort + 1;
        $question_nr = "{$nr}th";
        break;
    }
  ?>
  <h1>
    Hello <?php echo $name; ?>.
    This is your <?php echo $question_nr; ?> question.
  </h1>

  <table>
    <tr>
      <th>Question:</th>
      <td><?php echo $question['question']; ?></td>
    </tr>
    <tr>
      <th>Category:</th>
      <td><?php echo $question['category']; ?></td>
    </tr>
    <tr>
      <th>Difficulty:</th>
      <td><?php echo $question['difficulty']; ?></td>
    </tr>
  </table>

  <br>

  <form action="answer_question.php" method="post">
    <label for="answer">Answer</label>
    <select id="answer" name="answer">
      <?php
      foreach ($answers as $answer) {
        echo <<<END
          <option value="{$answer['answer_id']}">{$answer['answer']}</option>\n
          END;
      }
      ?>
    </select>
    <br>
    <input type="submit" value="Submit">
  </form>
</body>
</html>
