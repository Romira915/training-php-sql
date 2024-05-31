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
