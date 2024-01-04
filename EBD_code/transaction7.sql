BEGIN;

SAVEPOINT my_savepoint;

DO $$
DECLARE
    inserted_user_id INT;
    inserted_space_id INT;
BEGIN
    -- Insert the data into the 'likes_on_space' table and capture the values into variables
    INSERT INTO likes_on_spaces(user_id, space_id)
    VALUES ($user_id, $space_id)
    RETURNING user_id, space_id INTO inserted_user_id, inserted_space_id;

		 -- Insert data into the 'notification' table with the captured values
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (
        (SELECT user_id FROM space WHERE id = inserted_space_id),
        inserted_user_id,
        current_date
    );

    -- Insert data into the 'notification' table with the captured values
    INSERT INTO space_notification (id, space_id, notification_type)
    VALUES (currval('notification_id_seq'), inserted_space_id, 'liked_space');

END $$;

ROLLBACK TO my_savepoint;

DO $$
DECLARE
    inserted_user_id INT;
    inserted_space_id INT;
BEGIN
    -- Insert the data into the 'likes_on_space' table and capture the values into variables
    INSERT INTO likes_on_spaces(user_id, space_id)
    VALUES ($user_id, $space_id)
    RETURNING user_id, space_id INTO inserted_user_id, inserted_space_id;

		 -- Insert data into the 'notification' table with the captured values
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (
        (SELECT user_id FROM space WHERE id = inserted_space_id),
        inserted_user_id,
        current_date
    );

    -- Insert data into the 'notification' table with the captured values
    INSERT INTO space_notification (id, space_id, notification_type)
    VALUES (currval('notification_id_seq'), inserted_space_id, 'liked_space');

END $$;

-- Commit the transaction
COMMIT;
