CREATE FUNCTION verify_comment_restrictions() RETURNS TRIGGER AS 
$BODY$

BEGIN 

-- Check if the user has already liked the comment
IF EXISTS(
    SELECT 1
    FROM likes_on_comments
    WHERE user_id = NEW.user_id AND comment_id = NEW.comment_id
) THEN 
    RAISE EXCEPTION 'A user can only like a comment once';
END IF;

-- Check if the comment is in a group
IF EXISTS(
    SELECT 1
    FROM comment
    JOIN space ON comment.space_id = space.id
    WHERE comment.id = NEW.comment_id AND space.group_id IS NOT NULL
) THEN
    -- If the comment is in a group, check if the user is a member of that group
    IF NOT EXISTS(
        SELECT 1
        FROM member
        WHERE user_id = NEW.user_id
            AND group_id = (
                SELECT space.group_id
                FROM space
                WHERE space.id = (
                    SELECT comment.space_id
                    FROM comment
                    WHERE comment.id = NEW.comment_id
                )
            )
    ) THEN
        RAISE EXCEPTION 'A user can only like comments in spaces they belong to';
    END IF;
ELSE
    -- If the comment is not in a group, check the author's profile privacy
    IF EXISTS(
        SELECT 1
        FROM users
        WHERE id = (
            SELECT author_id
            FROM comment
            WHERE id = NEW.comment_id
        )
        AND NOT is_public
    ) AND NOT EXISTS(
        SELECT 1
        FROM follows
        WHERE user_id1 = NEW.user_id
            AND user_id2 = (
                SELECT author_id
                FROM comment
                WHERE id = NEW.comment_id
            )
    ) THEN
        RAISE EXCEPTION 'A user can only like comments from public users or from users they follow';
    END IF;
END IF;

RETURN NEW;

END 

$BODY$

LANGUAGE plpgsql;

-- Create the trigger
CREATE TRIGGER verify_comment_restrictions
BEFORE INSERT OR UPDATE ON likes_on_comments
FOR EACH ROW
EXECUTE PROCEDURE verify_comment_restrictions();


