<?php
const N_QUESTIONS = 10;

define("QUESTION_KEYS", array(
  "question_id",
  "session_id",
  "question",
  "sort",
  "category",
  "type",
  "difficulty",
  "correct_answer",
  "player_answer"
));

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

  public function get_question(string $question_id = null, string $session_id = null, int $sort = null) {
    $select = <<<END
      SELECT
        question_id,
        session_id,
        question,
        sort,
        category,
        type,
        difficulty,
        correct_answer
      FROM questions
      END;
    if (isset($question_id)) {
      $stmt = $this->mysqli->prepare(<<<END
        $select
        WHERE question_id=?
        END
      );
      $stmt->bind_param("s", $question_id);
    } else {
      $stmt = $this->mysqli->prepare(<<<END
        $select
        WHERE session_id=? AND sort=?
        END
      );
      $stmt->bind_param("ss", $session_id, $sort);
    }
    $stmt->execute();

    $stmt->bind_result(
      $question_id,
      $session_id,
      $question,
      $sort,
      $category,
      $type,
      $difficulty,
      $correct_answer);
    $stmt->fetch();

    return array(
      "question_id" => $question_id,
      "session_id" => $session_id,
      "question" => $question,
      "sort" => $sort,
      "category" => $category,
      "type" => $type,
      "difficulty" => $difficulty,
      "correct_answer" => $correct_answer,
    );
  }

  public function get_answers(string $question_id) {
    $stmt = $this->mysqli->prepare("SELECT question_id, answer_id, answer FROM answers WHERE question_id=?");
    $stmt->bind_param("s", $question_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $answers = array_map(
      function ($row) {
        return array(
          "question_id" => $row[0],
          "answer_id" => $row[1],
          "answer" => $row[2]
        );
      },
      $result->fetch_all()
    );

    shuffle($answers);
    return $answers;
  }

  public function get_answer(string $answer_id) {
    $stmt = $this->mysqli->prepare(<<<END
      SELECT
        question_id,
        session_id,
        answer
      FROM answers
      WHERE answer_id=?
      END
    );
    $stmt->bind_param("s", $answer_id);
    $stmt->execute();

    $stmt->bind_result($question_id, $session_id, $answer);
    $stmt->fetch();

    return array(
      "question_id" => $question_id,
      "session_id" => $session_id,
      "answer" => $answer,
    );
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
    $url = $this->make_opentdb_url(N_QUESTIONS, $category, $difficulty);
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

  public function get_next_question(string $session_id, int $sort) {
    $stmt = $this->mysqli->prepare(
      "SELECT sort FROM questions WHERE session_id=? AND sort>?"
    );
    $stmt->bind_param("si", $session_id, $sort);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = $result->fetch_all();

    if (isset($rows) && sizeof($rows) > 0) {
      $data = array_map(function($row) { return $row[0]; }, $rows);
      return min($data);
    }
  }

  public function update_answer(string $question_id, string $player_answer) {
    $stmt = $this->mysqli->prepare(<<<END
      UPDATE questions
      SET player_answer=?
      WHERE question_id=?
      END
    );
    $stmt->bind_param("ss", $player_answer, $question_id);
    $stmt->execute();
  }

  public function get_session(string $session_id) {
    $attrs = implode(", ", QUESTION_KEYS);
    $stmt = $this->mysqli->prepare(
      <<<END
      SELECT $attrs
      FROM questions
      WHERE session_id=?
      END
    );
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = $result->fetch_all();

    if (isset($rows) && sizeof($rows) > 0) {
      $data = array_map(function($row) {
        return array_combine(QUESTION_KEYS, $row);
      }, $rows);
      return $data;
    }
  }

  private function make_opentdb_url(int $amount, string $category, string $difficulty) {
    $param = array("amount" => $amount);
    if (isset($category) && $category != "") {
      $param["category"] = $category;
    }
    if (isset($difficulty) && $difficulty != "") {
      $param["difficulty"] = $difficulty;
    }

    $query = http_build_query($param);
    $url = "https://opentdb.com/api.php?" . $query;

    return $url;
  }
}

$triviadb = new triviadb;
?>
