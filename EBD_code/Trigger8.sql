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
