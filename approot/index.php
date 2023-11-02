<!DOCTYPE html>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<body>
  <div id="main">
    <h1>🧠 Trivia Quiz</h1>
    <div class="content">
      <form action="create_session.php" method="post">
        <label for="name">Enter your name:</label>
        <input type="text" id="name" name="name" required>
        <br>

        <label for="difficulty">Select the difficulty:</label>
        <select id="difficulty" name="difficulty">
          <option value="">Any difficulty</option>
          <option value="easy">Easy</option>
          <option value="medium">Medium</option>
          <option value="hard">Hard</option>
        </select>
        <br>

        <label for="category">Select the category:</label>
        <select id="category" name="category">
          <option value="">Any category</option>
          <?php
            # THINK ABOUT: maybe better hard-code this for better performance?

            $json = file_get_contents('https://opentdb.com/api_category.php');
            $categories = json_decode($json, true);
            foreach ($categories["trivia_categories"] as $category) {
              $id = htmlentities($category['id']);
              $name = htmlentities($category['name']);
              echo <<<END
          <option value="$id">$name</option>\n
    END;
            }
          ?>
        </select>
        <br>
        <input type="submit" value="Start">
      </form>
    </div>
    <small>
      <a href="/scoreboard.php">Score Board</a> /
      Powered by <a href="https://opentdb.com/">Open Trivia Database</a>
    </small>
  </div>
</body>
</html>
