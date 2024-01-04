BEGIN; 

INSERT INTO generic_user(username,name,email,password) 
VALUES($username, $name, $email, $password);

SAVEPOINT my_savepoint;

INSERT INTO users(id,is_public) 
VALUES((SELECT id FROM generic_user ORDER BY id DESC LIMIT 1), $is_public);

ROLLBACK TO my_savepoint;

INSERT INTO users(id,is_public) 
VALUES((SELECT id FROM generic_user ORDER BY id DESC LIMIT 1), $is_public);

COMMIT; 
