<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LAMP Trivia</title>
</head>
<body>
  <?php
    include 'dbconn.php';

    $session_id = $_GET["session_id"];
    $sort = $_GET["question"];

    $name = get_name($mysqli, $session_id);
    $question = get_question($mysqli, $session_id, $sort);
    $answers = get_answers($mysqli, $question["question_id"]);

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

<?php
// ↓ HELP FUNCTIONS ↓

function get_name(mysqli $mysqli, string $session_id): string {
  $stmt = $mysqli->prepare("SELECT name FROM sessions WHERE session_id=?");
  $stmt->bind_param("s", $session_id);
  $stmt->execute();

  $stmt->bind_result($name);
  $stmt->fetch();

  return $name;
}

function get_question(mysqli $mysqli, string $session_id, int $sort) {
  $stmt = $mysqli->prepare(<<<END
    SELECT
      question_id,
      question,
      category,
      difficulty
    FROM questions
    WHERE session_id=? AND sort=?
    END);
  $stmt->bind_param("ss", $session_id, $sort);
  $stmt->execute();

  $stmt->bind_result($question_id, $question, $category, $difficulty);
  $stmt->fetch();

  return array(
    "question_id" => $question_id,
    "question" => $question,
    "category" => $category,
    "difficulty" => $difficulty
  );
}

function get_answers(mysqli $mysqli, string $question_id) {
  $stmt = $mysqli->prepare("SELECT answer_id, answer FROM answers WHERE question_id=?");
  $stmt->bind_param("s", $question_id);
  $stmt->execute();

  $result = $stmt->get_result();
  $answers = array_map(
    function ($row) {
      return array(
        "answer_id" => $row[0],
        "answer" => $row[1]
      );
    },
    $result->fetch_all()
  );
  return $answers;
}
?>