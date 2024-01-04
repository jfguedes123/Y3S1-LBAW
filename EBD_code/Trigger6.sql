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
