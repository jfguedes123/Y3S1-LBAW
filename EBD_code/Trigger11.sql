CREATE FUNCTION update_username_on_delete()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE comment SET username = 'Anonymous' WHERE author_id = OLD.id;
    UPDATE users SET is_public = FALSE where id = OLD.id;
    DELETE from follows where user_id1 = OLD.id or user_id2 = OLD.id;
    UPDATE space SET is_public = FALSE where user_id = OLD.id;
    DELETE FROM member where user_id = OLD.id;
    DELETE FROM follows_request where user_id1 = OLD.id or user_id2 = OLD.id;

    -- Prevent the actual delete operation
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Create a trigger on the 'generic_user' table
CREATE TRIGGER update_username_on_delete
BEFORE DELETE ON generic_user
FOR EACH ROW
EXECUTE PROCEDURE update_username_on_delete();

