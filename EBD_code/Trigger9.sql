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


