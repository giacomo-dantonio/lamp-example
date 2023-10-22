<?php
include 'triviadb.php';

$session_id = $triviadb->create_session($_POST["name"], $_POST["category"], $_POST["difficulty"]);
$first_question = $triviadb->create_questions($session_id, $_POST["category"], $_POST["difficulty"]);

// redirect to first question
$query = array(
  "session_id" => $session_id,
  "question" => $first_question
);
$redirect_url = "/question.php?".http_build_query($query);
echo <<<END
  <meta http-equiv="refresh" content="0; url=$redirect_url" />
  END;
?>