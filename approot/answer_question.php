<!DOCTYPE html>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<?php
  include 'triviadb.php';

  if (!array_key_exists("answer_id", $_POST) || is_null($_POST["answer_id"])) {
    trigger_error("No answer provided", E_USER_ERROR);
    die(0);
  }

  $answer_id = $_POST["answer_id"];
  $answer = $triviadb->get_answer($answer_id);
  $question_id = $answer["question_id"];
  $question = $triviadb->get_question(question_id: $question_id);

  $session_id = $question["session_id"];
  $sort = $question["sort"];

  $name = $triviadb->get_name($session_id);
  $correct = $answer["answer"] == $question["correct_answer"];
  $next_question = $triviadb->get_next_question($session_id, $sort);

  $question_nr = question_nr($sort);
  $color = $correct ? "green" : "red";

  if (isset($next_question)) {
    $query = array(
      "session_id" => $session_id,
      "question" => $next_question
    );
    $next_url = "/question.php?".http_build_query($query);
  } else {
    $query = array(
      "session_id" => $session_id
    );
    $next_url = "/end.php?".http_build_query($query);
  }

  $triviadb->update_answer($question_id, $answer["answer"]);
?>
<body style="background-image: linear-gradient(to bottom right, <?php echo $color; ?>, white)">
  <div id="main">
    <h1><?php echo $name; ?>,</h1>
    <p><?php
      if ($correct) {
        echo "Congratulations, your answer is correct 🥳";
      } else {
        echo "Sorry, your answer is wrong 😞";
      }
    ?></p>

    <div class="content">
      <p>Your answer was:<br><strong><?php echo $answer["answer"]; ?></strong></p>

      <?php
        if (!$correct) {
      ?>
      <p>The correct answer was:<br><strong><?php echo $question["correct_answer"]; ?></strong></p>
      <?php
        }
      ?>

      <a class="next-question" href="<?php echo $next_url?>"><?php
        echo isset($next_question)? "Next question >>" : "Game finished";
      ?></a>
    </div>
    <small>This is your <?php echo $question_nr; ?> question /
      <?php echo $question['category']; ?> /
      <?php echo ucfirst($question['difficulty']); ?> /
      <a href="/scoreboard.php">Score Board</a>
    </small>
  </div>
</body>