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


