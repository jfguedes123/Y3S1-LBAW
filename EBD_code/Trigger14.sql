CREATE FUNCTION delete_group() RETURNS TRIGGER AS 

$BODY$ 

BEGIN 

DELETE FROM space where OLD.id = space.group_id;

DELETE FROM member where OLD.id = member.group_id;

DELETE FROM group_join_request WHERE OLD.id = group_join_request.group_id;

DELETE FROM group_notification WHERE OLD.id = group_notification.group_id;

RETURN OLD;

END 

$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER delete_group

BEFORE DELETE on groups

FOR EACH ROW

EXECUTE PROCEDURE delete_group();
