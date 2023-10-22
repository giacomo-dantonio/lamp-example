-- Switch to the 'testdb' database
USE testdb;

-- FIXME: Replace this with something else. This is just to test.
CREATE TABLE IF NOT EXISTS Persons (
    PersonID int,
    LastName varchar(255),
    FirstName varchar(255),
    Address varchar(255),
    City varchar(255)
);
