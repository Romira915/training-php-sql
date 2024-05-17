CREATE FUNCTION set_update_at() RETURNS TRIGGER AS
$$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- articles
CREATE TABLE articles
(
    id         INTEGER,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id, user_id)
);

-- article_published
CREATE TABLE article_published
(
    article_id INTEGER,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

-- article_deleted
CREATE TABLE article_deleted
(
    article_id INTEGER,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

-- article_images
CREATE TABLE article_images
(
    id         SERIAL PRIMARY KEY,
    article_id INTEGER   NOT NULL,
    user_id    INTEGER   NOT NULL,
    image_url  TEXT      NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

CREATE INDEX article_images_article_id_user_id_index ON article_images (article_id, user_id);
CREATE TRIGGER set_article_images_update_at
    BEFORE UPDATE
    ON article_images
    FOR EACH ROW
EXECUTE FUNCTION set_update_at();

-- article_detail
CREATE TABLE article_detail
(
    article_id   INTEGER,
    user_id      INTEGER,
    title        VARCHAR(191) NOT NULL,
    body         TEXT         NOT NULL,
    thumbnail_id INTEGER,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id),
    FOREIGN KEY (thumbnail_id) REFERENCES article_images (id)
);

CREATE INDEX article_detail_created_at_index ON article_detail (created_at DESC);

CREATE TRIGGER set_article_detail_update_at
    BEFORE UPDATE
    ON article_detail
    FOR EACH ROW
EXECUTE FUNCTION set_update_at();
