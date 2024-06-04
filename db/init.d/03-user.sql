-- users
CREATE TABLE users
(
    id         SERIAL PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE articles
    ADD FOREIGN KEY (user_id) REFERENCES users (id);

-- user_detail
CREATE TABLE user_detail
(
    user_id      INTEGER PRIMARY KEY REFERENCES users (id),
    display_name VARCHAR(191) UNIQUE NOT NULL,
    icon_path    TEXT                NOT NULL,
    created_at   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER set_user_detail_update_at
    BEFORE UPDATE
    ON user_detail
    FOR EACH ROW
EXECUTE FUNCTION set_update_at();

-- user_hashed_password
CREATE TABLE user_hashed_password
(
    user_id         INTEGER PRIMARY KEY REFERENCES users (id),
    hashed_password VARCHAR(255),
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER set_user_hashed_password_update_at
    BEFORE UPDATE
    ON user_hashed_password
    FOR EACH ROW
EXECUTE FUNCTION set_update_at();

