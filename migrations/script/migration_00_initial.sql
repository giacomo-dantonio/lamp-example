-- Switch to the 'trivadb' database
USE trivadb;

CREATE TABLE IF NOT EXISTS trivadb.sessions (
  session_id varchar(255),
  name varchar(255),
  category varchar(255),
  difficulty varchar(255),
  PRIMARY KEY (session_id)
);

CREATE TABLE IF NOT EXISTS trivadb.questions (
  question_id varchar(255),
  session_id varchar(255),
  question varchar(255),
  sort int,
  category varchar(255),
  type varchar(255),
  difficulty varchar(255),
  correct_answer varchar(255),
  player_answer varchar(255),
  CONSTRAINT UC_sort UNIQUE (session_id, sort),
  FOREIGN KEY (session_id) REFERENCES trivadb.sessions(session_id),
  PRIMARY KEY (question_id)
);

CREATE TABLE IF NOT EXISTS trivadb.answers (
  answer_id varchar(255),
  question_id varchar(255),
  session_id varchar(255),
  answer varchar(255),
  FOREIGN KEY (question_id) REFERENCES trivadb.questions(question_id),
  FOREIGN KEY (session_id) REFERENCES trivadb.sessions(session_id),
  PRIMARY KEY (answer_id)
);

CREATE OR REPLACE VIEW sessions_scored AS
WITH scores (session_id, score) AS (
  SELECT session_id, SUM(CASE WHEN player_answer=correct_answer THEN 1 ELSE 0 END) / COUNT(*) * 100 score
  FROM questions
  GROUP BY session_id
)
SELECT se.*, sc.score
FROM sessions se LEFT JOIN scores sc ON se.session_id = sc.session_id;
