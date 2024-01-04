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
