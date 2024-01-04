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
