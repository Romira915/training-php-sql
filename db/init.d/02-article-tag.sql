CREATE TABLE article_tags
(
    id         SERIAL PRIMARY KEY,
    article_id INTEGER      NOT NULL,
    user_id    INTEGER      NOT NULL,
    tag_name   VARCHAR(191) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);

CREATE INDEX article_tags_article_id_user_id_index ON article_tags (article_id, user_id);

CREATE TRIGGER set_article_tags_update_at
    BEFORE UPDATE
    ON article_tags
    FOR EACH ROW
EXECUTE FUNCTION set_update_at();
