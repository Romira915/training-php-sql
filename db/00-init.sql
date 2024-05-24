-- users
CREATE TABLE users
(
    id         SERIAL PRIMARY KEY,
    created_at TIMESTAMP NOT NULL
);

-- user_detail
CREATE TABLE user_detail
(
    user_id      INTEGER PRIMARY KEY REFERENCES users (id),
    display_name VARCHAR(191) UNIQUE NOT NULL,
    created_at   TIMESTAMP           NOT NULL,
    updated_at   TIMESTAMP           NOT NULL
);

-- user_hashed_password
CREATE TABLE user_hashed_password
(
    user_id         INTEGER PRIMARY KEY REFERENCES users (id),
    hashed_password VARCHAR(255),
    created_at      TIMESTAMP NOT NULL,
    updated_at      TIMESTAMP NOT NULL
);

-- articles
CREATE TABLE articles
(
    id         SERIAL,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL,
    PRIMARY KEY (id, user_id),
    FOREIGN KEY (user_id) REFERENCES users (id)
);

-- article_published
CREATE TABLE article_published
(
    article_id INTEGER,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

-- article_deleted
CREATE TABLE article_deleted
(
    article_id INTEGER,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

-- article_images
CREATE TABLE article_images
(
    id         SERIAL PRIMARY KEY,
    article_id INTEGER   NOT NULL,
    user_id    INTEGER   NOT NULL,
    image_path TEXT      NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

CREATE INDEX article_images_article_id_user_id_index ON article_images (article_id, user_id);

-- article_detail
CREATE TABLE article_detail
(
    article_id   INTEGER,
    user_id      INTEGER,
    title        VARCHAR(191) NOT NULL,
    body         TEXT         NOT NULL,
    thumbnail_id INTEGER,
    created_at   TIMESTAMP    NOT NULL,
    updated_at   TIMESTAMP    NOT NULL,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id),
    FOREIGN KEY (thumbnail_id) REFERENCES article_images (id)
);

-- article_tags
CREATE TABLE article_tags
(
    id         SERIAL PRIMARY KEY,
    article_id INTEGER      NOT NULL,
    user_id    INTEGER      NOT NULL,
    title      VARCHAR(191) NOT NULL,
    created_at TIMESTAMP    NOT NULL,
    updated_at TIMESTAMP    NOT NULL,
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

CREATE INDEX article_tags_article_id_user_id_index ON article_tags (article_id, user_id);
