-- Switch to the 'testdb' database
USE testdb;

CREATE TABLE IF NOT EXISTS testdb.sessions (
  session_id varchar(255),
  name varchar(255),
  PRIMARY KEY (session_id)
);

CREATE TABLE IF NOT EXISTS testdb.questions (
  question_id varchar(255),
  session_id varchar(255),
  question varchar(255),
  sort int,
  category varchar(255),
  type varchar(255),
  difficulty varchar(255),
  correct_answer varchar(255),
  player_answer varchar(255),
  FOREIGN KEY (session_id) REFERENCES testdb.sessions(session_id),
  PRIMARY KEY (question_id)
);

CREATE TABLE IF NOT EXISTS testdb.answers (
  answer_id varchar(255),
  question_id varchar(255),
  session_id varchar(255),
  answer varchar(255),
  FOREIGN KEY (question_id) REFERENCES testdb.questions(question_id),
  FOREIGN KEY (session_id) REFERENCES testdb.sessions(session_id),
  PRIMARY KEY (answer_id)
);
