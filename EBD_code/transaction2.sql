BEGIN;

-- Insert comment
INSERT INTO comment (space_id, author_id, username, content, date)
VALUES ($space_id, $author_id, $username, $content, (SELECT current_date));

SAVEPOINT my_savepoint;

-- Insert notification for that new comment
INSERT INTO notification (received_user, emits_user, date)
VALUES ( (SELECT user_id FROM space WHERE id = (SELECT space_id FROM comment ORDER BY id DESC LIMIT 1)), (SELECT author_id FROM comment ORDER BY id DESC LIMIT 1), (SELECT current_date));

-- Insert comment notification
INSERT INTO comment_notification (id, comment_id, notification_type)
VALUES (currval('notification_id_seq'), (SELECT id FROM comment ORDER BY id DESC LIMIT 1), 'reply_comment');

ROLLBACK TO my_savepoint;

-- Insert notification for that new comment
INSERT INTO notification (received_user, emits_user, date)
VALUES ( (SELECT user_id FROM space WHERE id = (SELECT space_id FROM comment ORDER BY id DESC LIMIT 1)), (SELECT author_id FROM comment ORDER BY id DESC LIMIT 1), (SELECT current_date));

-- Insert comment notification
INSERT INTO comment_notification (id, comment_id, notification_type)
VALUES (currval('notification_id_seq'),(SELECT id FROM comment ORDER BY id DESC LIMIT 1), 'reply_comment');

COMMIT;
