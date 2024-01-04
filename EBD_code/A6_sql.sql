-----------------------------------------
--
-- Use this code to drop and create a schema.
-- In this case, the DROP TABLE statements can be removed.
--
﻿﻿﻿-- DROP SCHEMA medialibrary CASCADE;
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
DROP TRIGGER IF EXISTS update_username_on_delete ON generic_user CASCADE;
DROP TRIGGER IF EXISTS delete_space ON space CASCADE;
DROP TRIGGER IF EXISTS delete_comment ON comment CASCADE;
DROP TRIGGER IF EXISTS delete_group ON groups CASCADE;


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
DROP FUNCTION IF EXISTS group_search_update() CASCADE;
DROP FUNCTION IF EXISTS space_search_update() CASCADE;
DROP FUNCTION IF EXISTS comment_search_update() CASCADE;
DROP FUNCTION IF EXISTS update_username_on_delete() CASCADE;
DROP FUNCTION IF EXISTS delete_space() CASCADE;
DROP FUNCTION IF EXISTS delete_comment() CASCADE;
DROP FUNCTION IF EXISTS delete_group() CASCADE;

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
DROP TABLE IF EXISTS generic_user CASCADE;
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

-- Create the 'generic_user' table
CREATE TABLE generic_user (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);

-- Create the 'user' table
CREATE TABLE users (
    id INTEGER PRIMARY KEY REFERENCES generic_user(id) ON UPDATE CASCADE,
    is_public BOOLEAN DEFAULT false NOT NULL
);
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
  id INTEGER PRIMARY KEY REFERENCES generic_user(id) ON UPDATE CASCADE
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
content TEXT NOT NULL,
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

AFTER INSERT OR UPDATE ON groups

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

IF EXISTS(SELECT * FROM users,space WHERE NEW.space_id = space.id AND space.user_id = users.id AND NOT users.is_public AND space.group_id IS NULL) AND NOT EXISTS (SELECT * FROM space,follows WHERE NEW.space_id = space.id AND NEW.user_id = follows.user_id1 AND follows.user_id2 = space.user_id) THEN 

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
        AND NOT users.is_public
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
    UPDATE comment SET username = 'Anonymous' WHERE author_id = OLD.id;
    UPDATE users SET is_public = FALSE where id = OLD.id;
    DELETE from follows where user_id1 = OLD.id or user_id2 = OLD.id;
    UPDATE space SET is_public = FALSE where user_id = OLD.id;
    DELETE FROM member where user_id = OLD.id;
    DELETE FROM follows_request where user_id1 = OLD.id or user_id2 = OLD.id;

    -- Prevent the actual delete operation
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Create a trigger on the 'generic_user' table
CREATE TRIGGER update_username_on_delete
BEFORE DELETE ON generic_user
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
        AND NOT is_public
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

-- Create the trigger
CREATE TRIGGER verify_comment_restrictions
BEFORE INSERT OR UPDATE ON likes_on_comments
FOR EACH ROW
EXECUTE PROCEDURE verify_comment_restrictions();
