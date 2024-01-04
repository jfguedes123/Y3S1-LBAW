--
-- Use a specific schema and set it as default - SportHub.
--
DROP SCHEMA IF EXISTS lbaw2372 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw2372;
set search_path to lbaw2372;

-----------------------------------------
--
-- Use this code to drop and create a schema.
-- In this case, the DROP TABLE statements can be removed.
--
-- DROP SCHEMA medialibrary CASCADE;
-- CREATE SCHEMA medialibrary;
-- SET search_path TO medialibrary;
-----------------------------------------

-----------------------------------------
-- Drop old schema
-----------------------------------------
-- Drop triggers with CASCADE

DROP TRIGGER IF EXISTS verify_comment_restrictions ON likes_on_comments CASCADE;
DROP TRIGGER IF EXISTS verify_comment_availability ON comment CASCADE;
DROP TRIGGER IF EXISTS verify_request_self_follow ON follows_request CASCADE;
DROP TRIGGER IF EXISTS verify_follow_request ON follows_request CASCADE;
DROP TRIGGER IF EXISTS verify_space_comments_availability ON likes_on_spaces CASCADE;
DROP TRIGGER IF EXISTS verify_group_join_request ON group_join_request CASCADE;
DROP TRIGGER IF EXISTS verify_group_owner ON groups CASCADE;
DROP TRIGGER IF EXISTS verify_self_follow ON follows CASCADE;
DROP TRIGGER IF EXISTS verify_space_likes ON likes_on_spaces CASCADE;
DROP TRIGGER IF EXISTS verify_comment_like ON likes_on_comments CASCADE;
DROP TRIGGER IF EXISTS verify_space_group ON space CASCADE;
DROP TRIGGER IF EXISTS group_search_update ON groups CASCADE;
DROP TRIGGER IF EXISTS space_search_update ON space CASCADE;
DROP TRIGGER IF EXISTS comment_search_update ON comment CASCADE;
DROP TRIGGER IF EXISTS update_username_on_delete ON users CASCADE;
DROP TRIGGER IF EXISTS delete_space ON space CASCADE;
DROP TRIGGER IF EXISTS delete_comment ON comment CASCADE;
DROP TRIGGER IF EXISTS delete_group ON groups CASCADE;
DROP TRIGGER IF EXISTS follow_request_notification ON follows_request CASCADE;
DROP TRIGGER IF EXISTS follows_notification ON follows CASCADE;
DROP TRIGGER IF EXISTS accept_follow_request_notification ON follows_request CASCADE;
DROP TRIGGER IF EXISTS leave_group_notification ON member CASCADE;
DROP TRIGGER IF EXISTS remove_member_notification ON member CASCADE;
DROP TRIGGER IF EXISTS join_request_notification ON group_join_request CASCADE;
DROP TRIGGER IF EXISTS accept_join_group_notification ON group_join_request CASCADE;
DROP TRIGGER IF EXISTS liked_space_notification ON likes_on_spaces CASCADE;
DROP TRIGGER IF EXISTS liked_comment_notification ON likes_on_comments CASCADE;
DROP TRIGGER IF EXISTS comment_space_notification ON comment CASCADE;



-- Drop functions with CASCADE
DROP FUNCTION IF EXISTS verify_comment_restrictions() CASCADE;
DROP FUNCTION IF EXISTS verify_comment_availability() CASCADE;
DROP FUNCTION IF EXISTS verify_request_self_follow() CASCADE;
DROP FUNCTION IF EXISTS verify_follow_request() CASCADE;
DROP FUNCTION IF EXISTS verify_space_comments_availability() CASCADE;
DROP FUNCTION IF EXISTS verify_group_join_request() CASCADE;
DROP FUNCTION IF EXISTS verify_group_owner() CASCADE;
DROP FUNCTION IF EXISTS verify_self_follow() CASCADE;
DROP FUNCTION IF EXISTS verify_space_likes() CASCADE;
DROP FUNCTION IF EXISTS verify_comment_like() CASCADE;
DROP FUNCTION IF EXISTS verify_space_group() CASCADE;
DROP FUNCTION IF EXISTS user_search_update CASCADE;
DROP FUNCTION IF EXISTS group_search_update() CASCADE;
DROP FUNCTION IF EXISTS space_search_update() CASCADE;
DROP FUNCTION IF EXISTS comment_search_update() CASCADE;
DROP FUNCTION IF EXISTS update_username_on_delete() CASCADE;
DROP FUNCTION IF EXISTS delete_space() CASCADE;
DROP FUNCTION IF EXISTS delete_comment() CASCADE;
DROP FUNCTION IF EXISTS delete_group() CASCADE;
DROP FUNCTION IF EXISTS follow_request_notification() CASCADE;
DROP FUNCTION IF EXISTS follows_notification() CASCADE;
DROP FUNCTION IF EXISTS accept_follow_request_notification() CASCADE;
DROP FUNCTION IF EXISTS leave_group_notification() CASCADE;
DROP FUNCTION IF EXISTS remove_member_notification() CASCADE;
DROP FUNCTION IF EXISTS join_request_notification() CASCADE;
DROP FUNCTION IF EXISTS accept_join_group_notification() CASCADE;
DROP FUNCTION IF EXISTS liked_space_notification() CASCADE;
DROP FUNCTION IF EXISTS liked_comment_notification() CASCADE;
DROP FUNCTION IF EXISTS comment_space_notification() CASCADE;



-- Drop indexes with CASCADE
DROP INDEX IF EXISTS search_group CASCADE;
DROP INDEX IF EXISTS search_space CASCADE;
DROP INDEX IF EXISTS search_comment CASCADE;
DROP INDEX IF EXISTS received_user_notification CASCADE;
DROP INDEX IF EXISTS emits_user_notification CASCADE;
DROP INDEX IF EXISTS user_id_space CASCADE;
DROP INDEX IF EXISTS user_id_comment CASCADE;

-- Drop tables with CASCADE

DROP TABLE IF EXISTS group_join_request CASCADE;
DROP TABLE IF EXISTS space_notification CASCADE;
DROP TABLE IF EXISTS user_notification CASCADE;
DROP TABLE IF EXISTS group_notification CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS follows_request CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS configuration CASCADE;
DROP TABLE IF EXISTS member CASCADE;
DROP TABLE IF EXISTS space CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS likes_on_spaces CASCADE;
DROP TABLE IF EXISTS likes_on_comments CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS blocked CASCADE;

--Drop Types--
DROP TYPE IF EXISTS user_notification_enum;
DROP TYPE IF EXISTS group_notification_enum;
DROP TYPE IF EXISTS space_notification_enum;
DROP TYPE IF EXISTS comment_notification_enum;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE user_notification_enum AS ENUM('request_follow', 'started_following','accepted_follow');
CREATE TYPE group_notification_enum AS ENUM('invite', 'remove', 'group_promotion', 'leave group', 'joined group', 'accepted_join', 'request_join');
CREATE TYPE space_notification_enum AS ENUM('liked_space','space_tagging');
CREATE TYPE comment_notification_enum AS ENUM('liked_comment','comment_space', 'reply_comment', 'comment_tagging');

-----------------------------------------
-- Tables
-----------------------------------------

-- Note that a plural 'users' name was adopted because user is a reserved word in PostgreSQL.

-- Create the 'user' table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    is_public BOOLEAN DEFAULT false NOT NULL);
-- Create the 'group' table
CREATE TABLE groups (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON UPDATE CASCADE,
    name TEXT NOT NULL,
    is_public BOOLEAN NOT NULL DEFAULT false,
    description TEXT NOT NULL
);

-- Create the 'space' table
CREATE TABLE space (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    date DATE NOT NULL CHECK (date <= current_date),
    is_public BOOLEAN NOT NULL,
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    group_id INT REFERENCES groups(id)
);



-- Create the 'member' table
CREATE TABLE member (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    group_id INT REFERENCES groups(id) ON UPDATE CASCADE,
    is_favorite BOOLEAN DEFAULT false NOT NULL,
    PRIMARY KEY(user_id,group_id)
);

-- Create the 'configuration' table
CREATE TABLE configuration (
    user_id INT REFERENCES users(id),
    notification_type TEXT NOT NULL,
    active BOOLEAN DEFAULT true NOT NULL
);

-- Create the 'notification' table
CREATE TABLE notification (
    id SERIAL PRIMARY KEY,
    received_user INT NOT NULL REFERENCES users(id)  ON UPDATE CASCADE,
    emits_user INT NOT NULL REFERENCES users(id)  ON UPDATE CASCADE,
    viewed BOOLEAN DEFAULT false NOT NULL,
    date DATE NOT NULL CHECK (date <= current_date)
);


-- Create the 'comment' table
CREATE TABLE comment (
    id SERIAL PRIMARY KEY,
    space_id INT REFERENCES space(id) ON UPDATE CASCADE,
    author_id INT REFERENCES users(id) ON UPDATE CASCADE,
    username TEXT NOT NULL,
    content TEXT,
    date DATE NOT NULL CHECK (date <= current_date)
);

-- Create the 'follows_request' table
CREATE TABLE follows_request (
    user_id1 INT REFERENCES users(id) ON UPDATE CASCADE,
    user_id2 INT REFERENCES users(id) ON UPDATE CASCADE,
   PRIMARY KEY(user_id1,user_id2)
);

-- Create the 'follows' table
CREATE TABLE follows (
    user_id1 INT REFERENCES users(id) ON UPDATE CASCADE,
    user_id2 INT REFERENCES users(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id1,user_id2)
);

-- Create the 'admin' table
CREATE TABLE admin (
   id INTEGER PRIMARY KEY REFERENCES users(id) ON UPDATE CASCADE
);

-- Create the 'comment_notification' table
CREATE TABLE comment_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    comment_id INT NOT NULL REFERENCES comment(id) ON UPDATE CASCADE,
    notification_type comment_notification_enum NOT NULL
);

-- Create the 'group_notification' table
CREATE TABLE group_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    group_id INT NOT NULL REFERENCES groups(id) ON UPDATE CASCADE,
    notification_type group_notification_enum NOT NULL
);

-- Create the 'user_notification' table
CREATE TABLE user_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON UPDATE CASCADE,
    notification_type user_notification_enum NOT NULL
);

-- Create the 'space_notification' table
CREATE TABLE space_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    space_id INT NOT NULL REFERENCES space(id) ON UPDATE CASCADE,
    notification_type space_notification_enum NOT NULL
);

-- Create the 'group_join_request' table
CREATE TABLE group_join_request (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    group_id INT REFERENCES groups(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id,group_id)
);

-- Create the 'likes_on_spaces' table
CREATE TABLE likes_on_spaces (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    space_id INT REFERENCES space(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id,space_id)
);

-- Create the 'likes_on_comments' table
CREATE TABLE likes_on_comments (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    comment_id INT REFERENCES comment(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id,comment_id)
);

-- Create the 'blocked' table
CREATE TABLE blocked (
    user_id INT REFERENCES users(id)
);

-- Create the 'message' table 
CREATE TABLE message ( 
id SERIAL PRIMARY KEY,
received_id INTEGER REFERENCES users(id) ON UPDATE CASCADE,
emits_id INTEGER REFERENCES users(id) ON UPDATE CASCADE,
content TEXT,
date DATE NOT NULL CHECK (date <= current_date),
is_viewed BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE INDEX received_user_notification ON notification USING btree(received_user);
CLUSTER notification USING received_user_notification;

CREATE INDEX emits_user_notification ON notification USING btree (emits_user);
CLUSTER notification USING emits_user_notification;

CREATE INDEX user_id_space ON space USING hash (user_id);

CREATE INDEX user_id_comment ON comment USING hash (author_id);

-- Add column to comment to store computed ts_vectors.

ALTER TABLE users
ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION user_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('simple', NEW.name), 'A') ||
         setweight(to_tsvector('simple', NEW.username), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.name <> OLD.name OR NEW.username <> OLD.username) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('simple', NEW.name), 'A') ||
             setweight(to_tsvector('simple', NEW.username), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

ALTER TABLE comment
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION comment_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('english', NEW.content), 'A')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.content <> OLD.content) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('english', NEW.content), 'A')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER user_search_update
BEFORE INSERT OR UPDATE ON users
FOR EACH ROW
EXECUTE PROCEDURE user_search_update();

CREATE INDEX search_user ON users USING GIN (tsvectors);


-- Create a trigger before insert or update on comment.
CREATE TRIGGER comment_search_update
 BEFORE INSERT OR UPDATE ON comment
 FOR EACH ROW
 EXECUTE PROCEDURE comment_search_update();

-- Finally, create a GIN index for ts_vectors.
CREATE INDEX search_comment ON comment USING GIN (tsvectors); 


-- Add column to space to store computed ts_vectors.
ALTER TABLE space
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION space_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = to_tsvector('simple', NEW.content);
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.content <> OLD.content) THEN
           NEW.tsvectors = to_tsvector('simple', NEW.content);
      END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on space.
CREATE TRIGGER space_search_update
 BEFORE INSERT OR UPDATE ON space
 FOR EACH ROW
 EXECUTE PROCEDURE space_search_update();


-- Finally, create a GIN index for ts_vectors.
CREATE INDEX search_space ON space USING GIN (tsvectors); 

-- Add column to groups to store computed ts_vectors.
ALTER TABLE groups
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION group_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('english', NEW.name), 'A') ||
         setweight(to_tsvector('english', NEW.description), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('english', NEW.name), 'A') ||
             setweight(to_tsvector('english', NEW.description), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on work.
CREATE TRIGGER group_search_update
 BEFORE INSERT OR UPDATE ON groups
 FOR EACH ROW
 EXECUTE PROCEDURE group_search_update();


-- Finally, create a GIN index for ts_vectors.
CREATE INDEX search_group ON groups USING GIN (tsvectors);

-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

--Trigger 01 -- 
CREATE FUNCTION verify_space_group() RETURNS TRIGGER AS 

$BODY$

BEGIN 

IF NOT EXISTS (SELECT * FROM member where new.user_id = user_id AND NEW.group_id = group_id)

AND NEW.group_id IS NOT NULL THEN 

RAISE EXCEPTION 'An user can only post on a group where he belongs'; 

END IF; 

RETURN NEW ;

END 

$BODY$ 

LANGUAGE plpgsql; 

CREATE TRIGGER verify_space_group

BEFORE INSERT OR UPDATE ON space 

FOR EACH ROW 

EXECUTE PROCEDURE verify_space_group();

--Trigger02--
CREATE FUNCTION verify_comment_like() RETURNS TRIGGER AS 

$BODY$ 

BEGIN 

IF EXISTS (SELECT * FROM likes_on_comments WHERE NEW.user_id =user_id AND NEW.comment_id = comment_id) THEN

RAISE EXCEPTION 'An user can only like a comment one time';

END IF; 

RETURN NEW; 

END

$BODY$ 

LANGUAGE plpgsql; 

CREATE TRIGGER verify_comment_like

BEFORE INSERT OR UPDATE ON likes_on_comments

FOR EACH ROW

EXECUTE PROCEDURE verify_comment_like();

--Trigger03--

CREATE FUNCTION verify_space_likes() RETURNS TRIGGER AS

$BODY$ 

BEGIN 

IF EXISTS(SELECT * FROM likes_on_spaces WHERE NEW.user_id = user_id and NEW.space_id = space_id) THEN 

RAISE EXCEPTION 'An user can only like a Space one time';

END IF;

RETURN NEW; 

END 

$BODY$ 
 
LANGUAGE plpgsql; 

CREATE TRIGGER verify_space_likes

BEFORE INSERT OR UPDATE ON likes_on_spaces

FOR EACH ROW

EXECUTE PROCEDURE verify_space_likes();

--Trigger04--
CREATE FUNCTION verify_self_follow() RETURNS TRIGGER AS

$BODY$  

BEGIN

IF NEW.user_id1 = NEW.user_id2 THEN

RAISE EXCEPTION 'An User cannot be his follower';

END IF;

RETURN NEW;

END 

$BODY$ 

LANGUAGE plpgsql; 

CREATE TRIGGER verify_self_follow

BEFORE INSERT OR UPDATE ON follows

FOR EACH ROW

EXECUTE PROCEDURE verify_self_follow();

--Trigger05--

CREATE FUNCTION verify_group_owner() RETURNS TRIGGER AS

$BODY$

BEGIN 

RAISE NOTICE 'NEW.id = %', NEW.id;

INSERT INTO member(user_id,group_id,is_favorite)

VALUES(NEW.user_id,NEW.id,True);

RETURN NEW;

END 

$BODY$

LANGUAGE plpgsql; 

CREATE TRIGGER verify_group_owner

AFTER INSERT ON groups

FOR EACH ROW

EXECUTE PROCEDURE verify_group_owner();



--Trigger06--
CREATE FUNCTION verify_group_join_request() RETURNS TRIGGER AS

$BODY$ 

BEGIN

IF EXISTS 

(SELECT * FROM member WHERE NEW.user_id = user_id AND NEW.group_id=group_id) 

THEN RAISE EXCEPTION 'An User cannot request to join a group where he already belongs';

END IF; 

RETURN NEW; 

END 

$BODY$ 

LANGUAGE plpgsql; 

CREATE TRIGGER verify_group_join_request

BEFORE INSERT ON group_join_request 

FOR EACH ROW 

EXECUTE PROCEDURE verify_group_join_request();

--Trigger07--
CREATE FUNCTION verify_space_comments_availability() RETURNS TRIGGER AS 
$BODY$

BEGIN 

IF EXISTS(SELECT * FROM likes_on_spaces WHERE NEW.user_id = user_id AND NEW.space_id= space_id) THEN 

RAISE EXCEPTION ' An User can only like a Space one time';

END IF;

IF EXISTS(SELECT * FROM space WHERE NEW.space_id = space.id AND space.group_id IS NOT NULL) AND NOT EXISTS (SELECT * FROM space,member WHERE NEW.space_id = space.id AND space.group_id = member.group_id AND NEW.user_id = member.user_id) THEN 

RAISE EXCEPTION 'An User can only like spaces from groups which they belong to';

END IF;

IF EXISTS(SELECT * FROM users,space WHERE NEW.space_id = space.id AND space.user_id = users.id AND users.is_public AND space.group_id IS NULL) AND NOT EXISTS (SELECT * FROM space,follows WHERE NEW.space_id = space.id AND NEW.user_id = follows.user_id1 AND follows.user_id2 = space.user_id) THEN 

RAISE EXCEPTION ' An User can only like comments or spaces from public users or from users that they follow';

END IF;

RETURN NEW;

END 

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_space_comments_availability

BEFORE INSERT OR UPDATE ON likes_on_spaces

FOR EACH ROW

EXECUTE PROCEDURE verify_space_comments_availability();

--Trigger08--

CREATE FUNCTION verify_follow_request() RETURNS TRIGGER AS

$BODY$  

BEGIN 

IF EXISTS 

(SELECT * FROM follows WHERE NEW.user_id1 = user_id1 AND NEW.user_id2 = user_id2) 

THEN RAISE EXCEPTION 'An User cannot ask to follow someone that he already follows';

END IF; 

RETURN NEW;

END 

$BODY$ 

LANGUAGE plpgsql; 

CREATE TRIGGER verify_follow_request 

BEFORE INSERT ON follows_request

FOR EACH ROW

EXECUTE PROCEDURE verify_follow_request();

-- Trigger09 --
CREATE FUNCTION verify_request_self_follow() RETURNS TRIGGER AS 

$BODY$ 

BEGIN 

IF NEW.user_id1 = NEW.user_id2 THEN 

RAISE EXCEPTION 'An user cannot request to follows themselbes';

END IF;

RETURN NEW;

END 

$BODY$ 

LANGUAGE plpgsql;

CREATE TRIGGER verify_request_self_follow

BEFORE INSERT OR UPDATE ON follows_request

FOR EACH ROW

EXECUTE PROCEDURE verify_request_self_follow();
--Trigger10--
CREATE FUNCTION verify_comment_availability() RETURNS TRIGGER AS
$BODY$
BEGIN
    -- Check if the NEW.author_id is the same as the space owner's user_id
    IF NEW.author_id = (SELECT user_id FROM space WHERE NEW.space_id = space.id) THEN
        RETURN NEW;  -- The author is the same as the space owner, no further checks needed
    END IF;

    -- Check if the user can comment on groups they belong to
    IF EXISTS (
        SELECT *
        FROM space
        WHERE NEW.space_id = space.id
        AND space.group_id IS NOT NULL
    ) AND NOT EXISTS (
        SELECT *
        FROM space, member
        WHERE NEW.space_id = space.id
        AND space.group_id = member.group_id
        AND NEW.author_id = member.user_id
    ) THEN
        RAISE EXCEPTION 'An user can only comment on groups where he belongs to';
    END IF;

    -- Check if the user can comment on spaces owned by public users or users they follow
    IF EXISTS (
        SELECT *
        FROM users, space
        WHERE NEW.space_id = space.id
        AND space.user_id = users.id
        AND users.is_public
        AND space.group_id IS NULL
    ) AND NOT EXISTS (
        SELECT *
        FROM space, follows
        WHERE NEW.space_id = space.id
        AND NEW.author_id = follows.user_id1
        AND follows.user_id2 = space.user_id
    ) THEN
        RAISE EXCEPTION 'An user can only comment spaces from: public users or users they follow';
    END IF;

    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_comment_availability
BEFORE INSERT OR UPDATE ON comment
FOR EACH ROW
EXECUTE PROCEDURE verify_comment_availability();




--Trigger11--
-- Create a function to update the username before deleting

CREATE FUNCTION update_username_on_delete()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE comment SET author_id = 1 WHERE author_id = OLD.id;
    UPDATE space set user_id = 1 WHERE user_id = OLD.id;
    UPDATE groups set user_id = 1 WHERE user_id = OLD.id;
    DELETE FROM notification where emits_user = OLD.id or received_user = OLD.id;
    DELETE FROM user_notification where user_id = OLD.id;
    DELETE from follows where user_id1 = OLD.id or user_id2 = OLD.id;
    DELETE FROM message where emits_id = OLD.id or received_id = OLD.id;
    DELETE FROM member where user_id = OLD.id;
    DELETE FROM follows_request where user_id1 = OLD.id or user_id2 = OLD.id;

    -- Prevent the actual delete operation
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

-- Create a trigger on the 'user' table
CREATE TRIGGER update_username_on_delete
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION update_username_on_delete();

--Trigger12--

CREATE FUNCTION delete_space() RETURNS TRIGGER AS 

$BODY$

BEGIN 

DELETE FROM likes_on_spaces WHERE OLD.id = likes_on_spaces.space_id;

DELETE FROM space_notification WHERE OLD.id = space_notification.space_id;

DELETE FROM comment WHERE OLD.id = comment.space_id;

RETURN OLD;

END 

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER delete_space

BEFORE DELETE ON space

FOR EACH ROW

EXECUTE PROCEDURE delete_space();

--Trigger13--

CREATE FUNCTION delete_comment() RETURNS TRIGGER AS 

$BODY$ 

BEGIN 

DELETE from likes_on_comments WHERE OLD.id = likes_on_comments.comment_id;

DELETE FROM comment_notification WHERE OLD.id = comment_notification.comment_id;

RETURN OLD;

END 

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER delete_comment

BEFORE DELETE ON comment

FOR EACH ROW

EXECUTE PROCEDURE delete_comment();

--Trigger14--
CREATE FUNCTION delete_group() RETURNS TRIGGER AS 

$BODY$ 

BEGIN 

DELETE FROM space where OLD.id = space.group_id;

DELETE FROM member where OLD.id = member.group_id;

DELETE FROM group_join_request WHERE OLD.id = group_join_request.group_id;

DELETE FROM group_notification WHERE OLD.id = group_notification.group_id;

RETURN OLD;

END 

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER delete_group

BEFORE DELETE on groups

FOR EACH ROW

EXECUTE PROCEDURE delete_group();

--Trigger 15 --
CREATE FUNCTION verify_comment_restrictions() RETURNS TRIGGER AS 
$BODY$

BEGIN 

-- Check if the user has already liked the comment
IF EXISTS(
    SELECT 1
    FROM likes_on_comments
    WHERE user_id = NEW.user_id AND comment_id = NEW.comment_id
) THEN 
    RAISE EXCEPTION 'A user can only like a comment once';
END IF;

-- Check if the comment is in a group
IF EXISTS(
    SELECT 1
    FROM comment
    JOIN space ON comment.space_id = space.id
    WHERE comment.id = NEW.comment_id AND space.group_id IS NOT NULL
) THEN
    -- If the comment is in a group, check if the user is a member of that group
    IF NOT EXISTS(
        SELECT 1
        FROM member
        WHERE user_id = NEW.user_id
            AND group_id = (
                SELECT space.group_id
                FROM space
                WHERE space.id = (
                    SELECT comment.space_id
                    FROM comment
                    WHERE comment.id = NEW.comment_id
                )
            )
    ) THEN
        RAISE EXCEPTION 'A user can only like comments in spaces they belong to';
    END IF;
ELSE
    -- If the comment is not in a group, check the author's profile privacy
    IF EXISTS(
        SELECT 1
        FROM users
        WHERE id = (
            SELECT author_id
            FROM comment
            WHERE id = NEW.comment_id
        )
        AND is_public
    ) AND NOT EXISTS(
        SELECT 1
        FROM follows
        WHERE user_id1 = NEW.user_id
            AND user_id2 = (
                SELECT author_id
                FROM comment
                WHERE id = NEW.comment_id
            )
    ) THEN
        RAISE EXCEPTION 'A user can only like comments from public users or from users they follow';
    END IF;
END IF;

RETURN NEW;

END 

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER verify_comment_restrictions
BEFORE INSERT OR UPDATE ON likes_on_comments
FOR EACH ROW
EXECUTE PROCEDURE verify_comment_restrictions();

-- Trigger16
CREATE FUNCTION follow_request_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 
    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES(NEW.user_id2, NEW.user_id1, false, CURRENT_DATE)
    RETURNING id INTO new_id;

    INSERT INTO user_notification(id, user_id, notification_type) 
    VALUES(new_id, NEW.user_id2, 'request_follow');

    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER follow_request_notification
AFTER INSERT ON follows_request
FOR EACH ROW
EXECUTE PROCEDURE follow_request_notification();


--Triger17--

CREATE FUNCTION follows_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 
    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES(NEW.user_id2, NEW.user_id1, false, CURRENT_DATE)
    RETURNING id INTO new_id;

    INSERT INTO user_notification(id, user_id, notification_type) 
    VALUES(new_id, NEW.user_id2, 'started_following');

    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER follows_notification
AFTER INSERT ON follows
FOR EACH ROW
EXECUTE PROCEDURE follows_notification();



--Trigger21--

CREATE FUNCTION join_request_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 
    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES((SELECT user_id FROM groups WHERE id = NEW.group_id), NEW.user_id, false, CURRENT_TIMESTAMP)
    RETURNING id INTO new_id;

    INSERT INTO group_notification(id, group_id, notification_type) 
    VALUES(new_id, NEW.group_id, 'request_join');
    
    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER join_request_notification
AFTER INSERT ON group_join_request
FOR EACH ROW
EXECUTE PROCEDURE join_request_notification();


--Trigger23--
CREATE FUNCTION liked_space_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 

    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES((SELECT user_id FROM space WHERE id = NEW.space_id),NEW.user_id, false, CURRENT_TIMESTAMP)
    RETURNING id INTO new_id;
    INSERT INTO space_notification(id, space_id, notification_type) 
    VALUES(new_id, NEW.space_id, 'liked_space');
    
    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER liked_space_notification
AFTER INSERT ON likes_on_spaces
FOR EACH ROW
EXECUTE PROCEDURE liked_space_notification();

--Trigger24--
CREATE FUNCTION liked_comment_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 

    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES((SELECT author_id FROM comment WHERE id = NEW.comment_id),NEW.user_id,false, CURRENT_TIMESTAMP)
    RETURNING id INTO new_id;
    INSERT INTO comment_notification(id, comment_id, notification_type) 
    VALUES(new_id, NEW.comment_id, 'liked_comment');
    
    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER liked_comment_notification
AFTER INSERT ON likes_on_comments
FOR EACH ROW
EXECUTE PROCEDURE liked_comment_notification();

--Trigger25--
CREATE OR REPLACE FUNCTION comment_space_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 
    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES((SELECT user_id FROM space WHERE id = NEW.space_id), NEW.author_id, false, CURRENT_TIMESTAMP)
    RETURNING id INTO new_id;

    INSERT INTO comment_notification(id, comment_id, notification_type) 
    VALUES(new_id, NEW.id, 'comment_space');
    
    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER comment_space_notification
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE PROCEDURE comment_space_notification();

--Trigger26--

CREATE FUNCTION joined_group_notification() RETURNS TRIGGER AS 
$BODY$ 
DECLARE
    new_id INTEGER;
BEGIN 
    INSERT INTO notification(received_user, emits_user, viewed, date) 
    VALUES((SELECT user_id from groups where id = NEW.group_id), NEW.user_id, false, CURRENT_DATE)
    RETURNING id INTO new_id;

    INSERT INTO group_notification(id, group_id, notification_type) 
    VALUES(new_id, NEW.group_id, 'joined group');

    RETURN NEW;
END 
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER joined_group_notification
AFTER INSERT ON member
FOR EACH ROW
EXECUTE PROCEDURE joined_group_notification();






INSERT INTO users(username,name,email,password,is_public) VALUES ('Anonymous','Anonymous','anonymous@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',true);
INSERT INTO users (username, name, email, password, is_public) VALUES ('luisvrelvas','luis','luisrelvas@netcabo.pt','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);
INSERT INTO users (username,name,email,password,is_public) VALUES('eduardomachado','eduardo','eduardo@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);
INSERT INTO users (username,name,email,password,is_public) VALUES('joaoguedes','joao','joao@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);
INSERT INTO users(username,name,email,password,is_public) VALUES('carlosoliveira','carlos','carlos@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);

INSERT INTO users(username,name,email,password,is_public) VALUES('joaopereira','joao','joaopereira@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);
INSERT INTO users(username,name,email,password,is_public) VALUES('mariajoao','maria','maria@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',true);
INSERT INTO users(username,name,email,password,is_public) VALUES('luisamaria','luisa','luisa@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',true);
INSERT INTO users(username,name,email,password,is_public) VALUES('franciscaluisa','francisca','francisca@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);
INSERT INTO users(username,name,email,password,is_public) VALUES('leonorponte','leonor','leonor@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',true);
INSERT INTO users(username,name,email,password,is_public) VALUES('bernardoalmeida','bernardo','bernardo@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',false);
INSERT INTO users(username,name,email,password,is_public) VALUES('miguelalmeida','miguel','miguel@gmail.com ','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',true);
INSERT INTO users(username,name,email,password,is_public) VALUES('joaquimnunes','joaquim','joaquim@gmail.com','$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG',true);
INSERT INTO users(username, name, email, password, is_public) VALUES('anamartins', 'ana', 'ana.martins@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('pedrosilva', 'pedro', 'pedro.silva@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('ritamoreira', 'rita', 'rita.moreira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('tiagosantos', 'tiago', 'tiago.santos@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('catarinaoliveira', 'catarina', 'catarina.oliveira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('josesousa', 'jose', 'jose.sousa@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('carlamachado', 'carla', 'carla.machado@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('ricardosilveira', 'ricardo', 'ricardo.silveira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('andreapereira', 'andrea', 'andrea.pereira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('carlosgomes', 'carlos', 'carlos.gomes@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('patriciacosta', 'patricia', 'patricia.costa@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('manuelrodrigues', 'manuel', 'manuel.rodrigues@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('susanaoliveira', 'susana', 'susana.oliveira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('fernandoalves', 'fernando', 'fernando.alves@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('danielasantos', 'daniela', 'daniela.santos@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('antoniocarvalho', 'antonio', 'antonio.carvalho@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('margaridasilva', 'margarida', 'margarida.silva@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('josepereira', 'jose', 'josepereira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('inesmartins', 'ines', 'inesmartins@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('joseoliveira', 'jose', 'joseoliveira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('mariasilva', 'maria', 'mariasilva@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('josemartins', 'jose', 'josemartins@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('inespereira', 'ines', 'inespereira@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('josealves', 'jose', 'josealves@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);
INSERT INTO users(username, name, email, password, is_public) VALUES('inesrodrigues', 'ines', 'inesrodrigues@gmai.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', false);
INSERT INTO users(username, name, email, password, is_public) VALUES('josecarvalho', 'jose', 'josecarvalho@gmail.com', '$2y$10$KRrZJveUEfwMazAkESHrcO350h3FlaFF4LiN1dTyGJgpkQKBfaVlG', true);

INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Look at Ronaldo goal',current_date,false,5,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Look at Pessi',current_date,false,5,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Did you see that Neymar is bald',current_date,false,5,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Taremi is taking the Penalty !',current_date,false,5,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Ronaldo is the best',current_date,false,6,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Ronaldo should have won the Ballon dOr',current_date,false,6,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Ronaldo is beautiful',current_date,false,6,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Benfica is going to spend millions in this season to finish in second',current_date,false,7,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Where is Roger-Ball?',current_date,false,7,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Ruben Amorim next season is on Manchester United',current_date,false,8,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Daniel Ramos got sacked! AHAHHAHAHHA AROUCAAAAA',current_date,false,8,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Who will win the Bwin league this season ?',current_date,false,9,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Will Hallaand Score more goals than Mbappe this season ?',current_date,false,9,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Will Ronaldo win the Asian Champions League this season ?',current_date,false,10,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Who will be the Top Scorer of Premier League this season ?',current_date,false,10,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Who will win the Champions League this season ?',current_date,false,10,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Who will win the Europa League this season ?',current_date,false,11,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Who will win the Euro 2024 ?',current_date,false,11,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Did you see Sergio Conceição Flash-Interview ? This guy is amazing ! PORTOOOOOO',current_date,false,11,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Did you see the Benfica losing at Reboleira?',current_date,false,12,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('What a goal from Gyokeres in Luz',current_date,false,12,null);
INSERT INTO space(content,date,is_public,user_id,group_id) VALUES('Did you see the Sporting winning against Porto ?',current_date,false,12,null);



INSERT INTO comment(space_id,author_id,username,content,date) VALUES(5,6,'mariajoao','Yes he is. Damn that power',current_date);
INSERT INTO comment(space_id,author_id,username,content,date) VALUES(2,6,'mariajoao','What are you saying bro ?',current_date);
INSERT INTO comment(space_id,author_id,username,content,date) VALUES(2,5,'joaopereira','Another Penalty Goal, this guy is horrible',current_date);
INSERT INTO comment(space_id,author_id,username,content,date) VALUES(1,5,'joaopereira','Amazing goal',current_date);

INSERT INTO admin(id) VALUES(1);
INSERT INTO admin(id) VALUES(2);
INSERT INTO admin(id) VALUES(3);
INSERT INTO admin(id) VALUES(4);



    