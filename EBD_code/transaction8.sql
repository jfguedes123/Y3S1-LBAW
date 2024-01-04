BEGIN;

SAVEPOINT my_savepoint;

DO $$
DECLARE
    requested_user_id INT;
    requested_group_id INT;
BEGIN

    -- Insert the user's request into the 'group_join_request' table
    INSERT INTO group_join_request (user_id, group_id)
    VALUES ($user_id, $group_id)
		RETURNING user_id, group_id INTO requested_user_id, requested_group_id;

		INSERT INTO notification (received_user, emits_user, date)
		VALUES ((SELECT user_id FROM groups WHERE id = requested_group_id), requested_user_id, current_date);

		 -- Insert data into the 'group_notification' table
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), requested_group_id, 'request_join');

END $$;

ROLLBACK TO my_savepoint;

DO $$
DECLARE
    requested_user_id INT;
    requested_group_id INT;
BEGIN

    -- Insert the user's request into the 'group_join_request' table
    INSERT INTO group_join_request (user_id, group_id)
    VALUES ($user_id, $group_id)
    RETURNING user_id, group_id INTO requested_user_id, requested_group_id;

    INSERT INTO notification (received_user, emits_user, date)
    VALUES ((SELECT user_id FROM groups WHERE id = requested_group_id), requested_user_id, current_date);

    -- Insert data into the 'group_notification' table
    INSERT INTO group_notification (id, group_id, notification_type)
    VALUES (currval('notification_id_seq'), requested_group_id, 'request_join');

END $$;

-- Commit the transaction
COMMIT;
