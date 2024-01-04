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


