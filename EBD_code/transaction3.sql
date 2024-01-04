-- Start a transaction with BEGIN
BEGIN;
SAVEPOINT my_savepoint;

DO $$
DECLARE
    inserted_user_id INT;
    inserted_group_id INT;
    inserted_is_favorite BOOLEAN;
BEGIN
    -- Insert the data into the table and capture the values into variables
    INSERT INTO member(user_id, group_id, is_favorite)
    VALUES ($user_id, $group_id, $is_favourite)
    RETURNING user_id, group_id, is_favorite INTO inserted_user_id, inserted_group_id, inserted_is_favorite;

    -- Insert data into the 'notification' table with the captured values
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (inserted_user_id, (SELECT user_id FROM groups WHERE id = inserted_group_id), current_date);

    -- Insert data into the 'group_notification' table
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), inserted_group_id, 'joined group');

END $$;

ROLLBACK TO my_savepoint;

DO $$
DECLARE
    inserted_user_id INT;
    inserted_group_id INT;
    inserted_is_favorite BOOLEAN;
BEGIN
    -- Insert the data into the table and capture the values into variables
    INSERT INTO member(user_id, group_id, is_favorite)
    VALUES ($user_id, $group_id, $is_favorite)
    RETURNING user_id, group_id, is_favorite INTO inserted_user_id, inserted_group_id, inserted_is_favorite;

    -- Insert data into the 'notification' table with the captured values
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (inserted_user_id, (SELECT user_id FROM groups WHERE id = inserted_group_id), current_date);

    -- Insert data into the 'group_notification' table
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), inserted_group_id, 'joined group');

END $$;

-- Commit the transaction
COMMIT;
