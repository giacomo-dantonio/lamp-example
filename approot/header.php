  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧠</text></svg>">
  <link rel="stylesheet" href="styles.css">
  <title>LAMP Trivia Quiz</title>

<?php
//error handler function
function redirect_to_error_page($errno, $errstr) {
  $err = "[$errno] $errstr";
  $query = array("error" => $err);

  $redirect_url = "/error.php?".http_build_query($query);
  echo <<<END
    <meta http-equiv="refresh" content="0; url=$redirect_url" />
    END;
}

//set error handler
set_error_handler("redirect_to_error_page");
?>
