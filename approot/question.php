<!DOCTYPE html>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<body>
  <?php
    include 'triviadb.php';

    $session_id = $_GET["session_id"];
    $sort = $_GET["question"];

    $name = $triviadb->get_name($session_id);
    $question = $triviadb->get_question(session_id: $session_id, sort: $sort);
    $answers = $triviadb->get_answers($question["question_id"]);
    $question_nr = question_nr($sort);
  ?>
  <div id="main">
    <h1>
      ⁉️ <?php echo $name; ?>,
    </h1>

    <div class="content">
      <p><strong>Q:</strong> <?php echo $question['question']; ?> 🤔</p>

      <form class="flex-container" action="answer_question.php" method="post">
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
    <small>This is your <?php echo $question_nr; ?> question /
      <?php echo $question['category']; ?> /
      <?php echo ucfirst($question['difficulty']); ?>
    </small>
  </div>
</body>
</html>
