BEGIN;

SAVEPOINT my_savepoint;

DO $$
DECLARE
    removed_user_id INT;
    removed_group_id INT;
BEGIN
    -- Retrieve the user_id and group_id of the member to be removed
    SELECT user_id, group_id INTO removed_user_id, removed_group_id
    FROM member
    WHERE user_id = $user_id AND group_id = $group_id;

    -- Delete the member from the 'member' table
    DELETE FROM member
    WHERE user_id = removed_user_id AND group_id = removed_group_id;

    -- Insert data into the 'notification' table indicating the user left the group
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (removed_user_id, (SELECT user_id FROM groups WHERE id = removed_group_id), current_date);

    -- Insert data into the 'group_notification' table
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), removed_group_id, 'leave group');

END $$;

ROLLBACK TO my_savepoint;

DO $$
DECLARE
    removed_user_id INT;
    removed_group_id INT;
BEGIN
    -- Retrieve the user_id and group_id of the member to be removed
    SELECT user_id, group_id INTO removed_user_id, removed_group_id
    FROM member
    WHERE user_id = $user_id AND group_id = $group_id;

    -- Delete the member from the 'member' table
    DELETE FROM member
    WHERE user_id = removed_user_id AND group_id = removed_group_id;

    -- Insert data into the 'notification' table indicating the user left the group
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (removed_user_id, (SELECT user_id FROM groups WHERE id = removed_group_id), current_date);

    -- Insert data into the 'group_notification' table
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), removed_group_id, 'leave group');

END $$;

-- Commit the transaction
COMMIT;
