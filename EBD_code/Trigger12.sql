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

