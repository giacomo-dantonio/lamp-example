<?php
include 'dbconn.php';

$session_id = create_session($mysqli, $_POST["name"], $_POST["category"], $_POST["difficulty"]);
$first_question = create_questions($mysqli, $session_id, $_POST["category"], $_POST["difficulty"]);

// redirect to first question
$query = array(
  "session_id" => $session_id,
  "question" => $first_question
);
$redirect_url = "/question.php?".http_build_query($query);
echo <<<END
  <meta http-equiv="refresh" content="0; url=$redirect_url" />
  END;


// ↓ HELP FUNCTIONS ↓

function create_session(mysqli $mysqli, string $name, string $category, string $difficulty): string {
  $session_id = uniqid();

  $stmt = $mysqli->prepare(<<<END
    INSERT INTO trivadb.sessions (session_id, name, category, difficulty)
    VALUES (?, ?, ?, ?)
    END);
  $stmt->bind_param("ssss", $session_id, $name, $category, $difficulty);
  $stmt->execute();

  return $session_id;
}

function make_url(int $amount, string $category, string $difficulty) {
  $param = array("amount" => 10);
  if (isset($category) && $category != "") {
    $param["category"] = $category;
  }
  if (isset($difficulty) && $difficulty != "") {
    $difficulty = $difficulty;
  }

  $query = http_build_query($param);
  $url = "https://opentdb.com/api.php?" . $query;

  return $url;
}

function create_questions(mysqli $mysqli, string $session_id, string $category, string $difficulty) {
  // fetch questions from Open Trivia API
  $url = make_url(10, $category, $difficulty);
  $response = file_get_contents($url);
  $json = json_decode($response, true);

  if ($json["response_code"] != 0) {
    // FIXME: handle error
  }

  // Prepare insert statements
  $stmt = $mysqli->prepare(<<<END
    INSERT INTO trivadb.questions (
      question_id,
      session_id,
      question,
      sort,
      category,
      type,
      difficulty,
      correct_answer)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    END);
  $stmt->bind_param(
    "sssissss",
    $question_id,
    $session_id,
    $text,
    $sort,
    $question_category,
    $type,
    $question_difficulty,
    $correct_answer
  );

  $ans_stmt = $mysqli->prepare(<<<END
    INSERT INTO trivadb.answers (
      answer_id,
      question_id,
      session_id,
      answer
    )
    VALUES (?, ?, ?, ?)
    END
  );
  $ans_stmt->bind_param(
    "ssss",
    $answer_id,
    $question_id,
    $session_id,
    $ans_text
  );

  // Insert questions to DB
  $sort = 0;
  foreach ($json["results"] as $question) {
    $question_id = uniqid();
    $text = $question["question"];
    $question_category = $question["category"];
    $type = $question["type"];
    $question_difficulty = $question["difficulty"];
    $correct_answer = $question["correct_answer"];

    $stmt->execute();

    // Insert answers to DB
    $answer_id = uniqid();
    $ans_text = $question["correct_answer"];
    $ans_stmt->execute();

    foreach ($question["incorrect_answers"] as $ans_text) {
      $answer_id = uniqid();
      $ans_stmt->execute();
    }

    $sort++;
  }

  return 0;
}
?>