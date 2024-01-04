
CREATE FUNCTION verify_comment_availability() RETURNS TRIGGER AS
$BODY$
BEGIN
    -- Check if the NEW.author_id is the same as the space owner's user_id
    IF NEW.author_id = (SELECT user_id FROM space WHERE NEW.space_id = space.id) THEN
        RETURN NEW;  -- The author is the same as the space owner, no further checks needed
    END IF;

    -- Check if the user can comment on groups they belong to
    IF EXISTS (
        SELECT *
        FROM space
        WHERE NEW.space_id = space.id
        AND space.group_id IS NOT NULL
    ) AND NOT EXISTS (
        SELECT *
        FROM space, member
        WHERE NEW.space_id = space.id
        AND space.group_id = member.group_id
        AND NEW.author_id = member.user_id
    ) THEN
        RAISE EXCEPTION 'An user can only comment on groups where he belongs to';
    END IF;

    -- Check if the user can comment on spaces owned by public users or users they follow
    IF EXISTS (
        SELECT *
        FROM users, space
        WHERE NEW.space_id = space.id
        AND space.user_id = users.id
        AND NOT users.is_public
        AND space.group_id IS NULL
    ) AND NOT EXISTS (
        SELECT *
        FROM space, follows
        WHERE NEW.space_id = space.id
        AND NEW.author_id = follows.user_id1
        AND follows.user_id2 = space.user_id
    ) THEN
        RAISE EXCEPTION 'An user can only comment spaces from: public users or users they follow';
    END IF;

    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_comment_availability
BEFORE INSERT OR UPDATE ON comment
FOR EACH ROW
EXECUTE PROCEDURE verify_comment_availability();

