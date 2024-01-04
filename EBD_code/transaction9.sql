BEGIN;

SAVEPOINT my_savepoint;

DO $$
DECLARE
    requested_user_id INT;
    requested_group_id INT;
BEGIN
    -- Retrieve the user_id and group_id of the user requesting to join a group
    SELECT user_id, group_id INTO requested_user_id, requested_group_id
    FROM group_join_request
    WHERE user_id = $user_id AND group_id = $group_id;

    -- Insert new member
    INSERT INTO member (user_id, group_id, is_favorite)
    VALUES (requested_user_id, requested_group_id, $is_favorite);

    -- Remove group join request
    DELETE FROM group_join_request
    WHERE user_id = requested_user_id
    AND group_id = requested_group_id;

    -- Insert data into the 'notification' table
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (
        requested_user_id,
        (SELECT user_id FROM groups WHERE id = requested_group_id),
        current_date
    );

    -- Insert data into the 'group_notification' table for the acceptance
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), requested_group_id, 'accepted_join');

END $$;

ROLLBACK TO my_savepoint;

DO $$
DECLARE
    requested_user_id INT;
    requested_group_id INT;
BEGIN
    -- Retrieve the user_id and group_id of the user requesting to join a group
    SELECT user_id, group_id INTO requested_user_id, requested_group_id
    FROM group_join_request
    WHERE user_id = $user_id AND group_id = $group_id;

    -- Insert new member
    INSERT INTO member (user_id, group_id, is_favorite)
    VALUES (requested_user_id, requested_group_id, $is_favorite);

    -- Remove group join request
    DELETE FROM group_join_request
    WHERE user_id = requested_user_id
    AND group_id = requested_group_id;

    -- Insert data into the 'notification' table
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (
        requested_user_id,
        (SELECT user_id FROM groups WHERE id = requested_group_id),
        current_date
    );

    -- Insert data into the 'group_notification' table for the acceptance
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), requested_group_id, 'accepted_join');

END $$;

-- Commit the transaction
COMMIT;
