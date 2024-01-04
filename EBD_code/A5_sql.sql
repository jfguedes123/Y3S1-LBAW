
DROP TABLE IF EXISTS group_join_request CASCADE;
DROP TABLE IF EXISTS space_notification CASCADE;
DROP TABLE IF EXISTS user_notification CASCADE;
DROP TABLE IF EXISTS group_notification CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS follows_request CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS configuration CASCADE;
DROP TABLE IF EXISTS member CASCADE;
DROP TABLE IF EXISTS space CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS generic_user CASCADE;
DROP TABLE IF EXISTS likes_on_spaces CASCADE;
DROP TABLE IF EXISTS likes_on_comments CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS blocked CASCADE;

-- Create the 'generic_user' table
CREATE TABLE generic_user (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);

-- Create the 'user' table
CREATE TABLE users (
    id INTEGER PRIMARY KEY REFERENCES generic_user(id) ON UPDATE CASCADE,
    is_public BOOLEAN DEFAULT false NOT NULL
);
-- Create the 'group' table
CREATE TABLE groups (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON UPDATE CASCADE,
    name TEXT NOT NULL,
    is_public BOOLEAN NOT NULL DEFAULT false,
    description TEXT NOT NULL
);

-- Create the 'space' table
CREATE TABLE space (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    date DATE NOT NULL CHECK (date <= current_date),
    is_public BOOLEAN NOT NULL,
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    group_id INT REFERENCES groups(id)
);



-- Create the 'member' table
CREATE TABLE member (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    group_id INT REFERENCES groups(id) ON UPDATE CASCADE,
    is_favorite BOOLEAN DEFAULT false NOT NULL,
    PRIMARY KEY(user_id,group_id)
);

-- Create the 'configuration' table
CREATE TABLE configuration (
    user_id INT REFERENCES users(id),
    notification_type TEXT NOT NULL,
    active BOOLEAN DEFAULT true NOT NULL
);

-- Create the 'notification' table
CREATE TABLE notification (
    id SERIAL PRIMARY KEY,
    received_user INT NOT NULL REFERENCES users(id)  ON UPDATE CASCADE,
    emits_user INT NOT NULL REFERENCES users(id)  ON UPDATE CASCADE,
    viewed BOOLEAN DEFAULT false NOT NULL,
    date DATE NOT NULL CHECK (date <= current_date)
);


-- Create the 'comment' table
CREATE TABLE comment (
    id SERIAL PRIMARY KEY,
    space_id INT REFERENCES space(id) ON UPDATE CASCADE,
    author_id INT REFERENCES users(id) ON UPDATE CASCADE,
    username TEXT NOT NULL,
    content TEXT,
    date DATE NOT NULL CHECK (date <= current_date)
);

-- Create the 'follows_request' table
CREATE TABLE follows_request (
    user_id1 INT REFERENCES users(id) ON UPDATE CASCADE,
    user_id2 INT REFERENCES users(id) ON UPDATE CASCADE,
   PRIMARY KEY(user_id1,user_id2)
);

-- Create the 'follows' table
CREATE TABLE follows (
    user_id1 INT REFERENCES users(id) ON UPDATE CASCADE,
    user_id2 INT REFERENCES users(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id1,user_id2)
);

-- Create the 'admin' table
CREATE TABLE admin (
  id INTEGER PRIMARY KEY REFERENCES generic_user(id) ON UPDATE CASCADE
);

-- Create the 'comment_notification' table
CREATE TABLE comment_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    comment_id INT NOT NULL REFERENCES comment(id) ON UPDATE CASCADE,
    notification_type comment_notification_enum NOT NULL
);

-- Create the 'group_notification' table
CREATE TABLE group_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    group_id INT NOT NULL REFERENCES groups(id) ON UPDATE CASCADE,
    notification_type group_notification_enum NOT NULL
);

-- Create the 'user_notification' table
CREATE TABLE user_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    notification_type user_notification_enum NOT NULL
);

-- Create the 'space_notification' table
CREATE TABLE space_notification (
    id SERIAL PRIMARY KEY REFERENCES notification(id) ON UPDATE CASCADE,
    space_id INT NOT NULL REFERENCES space(id) ON UPDATE CASCADE,
    notification_type space_notification_enum NOT NULL
);

-- Create the 'group_join_request' table
CREATE TABLE group_join_request (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    group_id INT REFERENCES groups(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id,group_id)
);

-- Create the 'likes_on_spaces' table
CREATE TABLE likes_on_spaces (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    space_id INT REFERENCES space(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id,space_id)
);

-- Create the 'likes_on_comments' table
CREATE TABLE likes_on_comments (
    user_id INT REFERENCES users(id) ON UPDATE CASCADE,
    comment_id INT REFERENCES comment(id) ON UPDATE CASCADE,
    PRIMARY KEY(user_id,comment_id)
);

-- Create the 'blocked' table
CREATE TABLE blocked (
    user_id INT REFERENCES users(id)
);

-- Create the 'message' table 
CREATE TABLE message ( 
id SERIAL PRIMARY KEY,
received_id INTEGER REFERENCES users(id) ON UPDATE CASCADE,
emits_id INTEGER REFERENCES users(id) ON UPDATE CASCADE,
content TEXT NOT NULL,
date DATE NOT NULL CHECK (date <= current_date),
is_viewed BOOLEAN NOT NULL DEFAULT FALSE
);
