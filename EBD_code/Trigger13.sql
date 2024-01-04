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

