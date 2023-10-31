<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles.css">
  <title>LAMP Trivia</title>
</head>
<body>
  <?php
    include 'triviadb.php';

    $session_id = $_GET["session_id"];
    $sort = $_GET["question"];

    $name = $triviadb->get_name($session_id);
    $question = $triviadb->get_question(session_id: $session_id, sort: $sort);
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
  <div id="main">
    <h1>
      ⁉️ <?php echo $name; ?>,
    </h1>

    <div class="content">
      <p><strong>Q:</strong> <?php echo $question['question']; ?> 🤔</p>

      <form action="answer_question.php" method="post">
        <div class="answers">
          <?php
          foreach ($answers as $answer) {
            echo <<<END
              <label>
                <input type="radio" name="answer_id" value="{$answer['answer_id']}">
                {$answer['answer']}
              </label>
    END;
          }
          ?>
        </div>
        <input type="submit" value="Submit">
      </form>
    </div>
    <small>This is your <?php echo $question_nr; ?> question. /
    <?php echo $question['category']; ?> /
    <?php echo $question['difficulty']; ?>
  </small>
  </div>
</body>
</html>
