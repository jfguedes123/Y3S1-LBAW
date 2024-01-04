-- Start a transaction with BEGIN
BEGIN;

SAVEPOINT my_savepoint;

DO $$
DECLARE
    inserted_user_id1 INT;
    inserted_user_id2 INT;
BEGIN
    -- Insert data into the 'follows_request' table
    INSERT INTO follows_request (user_id1, user_id2)
    VALUES ($user_id1, $user_id2) 
    RETURNING user_id1, user_id2 INTO inserted_user_id1, inserted_user_id2;

    -- Insert data into the 'notification' table
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (
        inserted_user_id2,  -- received_user is user_id2
        inserted_user_id1,  -- emits_user is user_id1
        current_date
    );

    -- Insert data into the 'comment_notification' table
    INSERT INTO user_notification (id, notification_type)
    VALUES (currval('notification_id_seq'), 'request_follow');

END $$;

ROLLBACK TO my_savepoint;

DO $$
DECLARE
    inserted_user_id1 INT;
    inserted_user_id2 INT;
BEGIN
    -- Insert data into the 'follows_request' table
    INSERT INTO follows_request (user_id1, user_id2)
    VALUES ($user_id1, $user_id2) 
    RETURNING user_id1, user_id2 INTO inserted_user_id1, inserted_user_id2;

    -- Insert data into the 'notification' table
    INSERT INTO notification (received_user, emits_user, date)
    VALUES (
        inserted_user_id2,  -- received_user is user_id2
        inserted_user_id1,  -- emits_user is user_id1
        current_date
    );

    -- Insert data into the 'comment_notification' table
    INSERT INTO user_notification (id, notification_type)
    VALUES (currval('notification_id_seq'), 'request_follow');

END $$;
-- Commit the transaction
COMMIT;
