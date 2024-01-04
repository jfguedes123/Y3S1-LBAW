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

