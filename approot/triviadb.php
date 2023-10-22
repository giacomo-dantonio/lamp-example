<?php
class triviadb
{
  private $mysqli;

  function __construct() {
    $this->mysqli = new mysqli("devmysql", "testuser", "testpw", "trivadb");
  }

  public function get_name(string $session_id): string {

    $stmt = $this->mysqli->prepare("SELECT name FROM sessions WHERE session_id=?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    $stmt->bind_result($name);
    $stmt->fetch();
  
    return $name;
  }
  
  public function get_question(string $session_id, int $sort) {
    $stmt = $this->mysqli->prepare(<<<END
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
  
  public function get_answers(string $question_id) {
    $stmt = $this->mysqli->prepare("SELECT answer_id, answer FROM answers WHERE question_id=?");
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

  public function create_session(string $name, string $category, string $difficulty): string {
    $session_id = uniqid();
  
    $stmt = $this->mysqli->prepare(<<<END
      INSERT INTO trivadb.sessions (session_id, name, category, difficulty)
      VALUES (?, ?, ?, ?)
      END);
    $stmt->bind_param("ssss", $session_id, $name, $category, $difficulty);
    $stmt->execute();
  
    return $session_id;
  }

  public function create_questions(string $session_id, string $category, string $difficulty) {
    // fetch questions from Open Trivia API
    $url = $this->make_opentdb_url(10, $category, $difficulty);
    $response = file_get_contents($url);
    $json = json_decode($response, true);
  
    if ($json["response_code"] != 0) {
      // FIXME: handle error
    }
  
    // Prepare insert statements
    $stmt = $this->mysqli->prepare(<<<END
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
  
    $ans_stmt = $this->mysqli->prepare(<<<END
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

  private function make_opentdb_url(int $amount, string $category, string $difficulty) {
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
}

$triviadb = new triviadb;
?>
