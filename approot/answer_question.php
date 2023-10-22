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

    $answer_id = $_POST["answer_id"];
    $answer = $triviadb->get_answer($answer_id);
    $question_id = $answer["question_id"];
    $question = $triviadb->get_question(question_id: $question_id);

    $session_id = $question["session_id"];
    $sort = $question["sort"];

    $name = $triviadb->get_name($session_id);
    $correct = $answer["answer"] == $question["correct_answer"];
    $next_question = $triviadb->get_next_question($session_id, $sort);

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

  <h1><?php echo $name; ?>,
  <?php
    if ($correct) {
      echo "Congratulations, your answer is correct 🥳";
    } else {
      echo "Sorry, your answer is wrong 😞";
    }
  ?>
  </h1>

  <p>Your answer was: <?php echo $answer["answer"]; ?></p>

  <?php
    if (!$correct) {
  ?>
  <p>The correct answer was: <?php echo $question["correct_answer"]; ?></p>
  <?php
    }
  ?>

  <a href="<?php echo $next_url?>"><?php
    echo isset($next_question)? "Next question >>" : "Game finished";
  ?></a>
</body>