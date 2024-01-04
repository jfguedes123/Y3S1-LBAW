-----------------------------------------
-- Populate the database
-----------------------------------------
-- Sample data for generic_user table

INSERT INTO generic_user(username,name,email,password) VALUES('user1','user1','user1@example.com','user1'),('user2','user2','user2@example.com','user2'),('admin','admin','admin@example.com','admin');

INSERT INTO users(id,is_public) VALUES(1,false),(2,true); 

INSERT INTO admin(id) VALUES(3);

INSERT INTO groups(user_id,name,is_public,description) VALUES(1,'EBD',false,'Just a creation for the EDB Component');

INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('We will discuss our opinions','11-09-2021',true,1,1);

INSERT INTO space(content,date,is_public,user_id) VALUES('Just a test','11-09-2021',false,2);

INSERT INTO comment(space_id,author_id,username,content,date) VALUES(1,1,'wdyw','what do you want','11-09-2022');
INSERT INTO comment(space_id,author_id,username,content,date) VALUES(2,2,'wdym','what do you mean','11-09-2022');

INSERT INTO likes_on_comments(user_id,comment_id) VALUES(1,2);

INSERT INTO follows_request(user_id1,user_id2) VALUES(1,2); 

INSERT INTO follows(user_id1,user_id2) VALUES(2,1); 

INSERT INTO group_join_request(user_id,group_id) VALUES(2,1); 

INSERT INTO likes_on_spaces(user_id,space_id) VALUES(1,2); 

INSERT INTO message(received_id,emits_id,content,date,is_viewed) VALUES(1,2,'message','11-09-2021',false);


