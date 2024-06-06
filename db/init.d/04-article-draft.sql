-- article_draft
CREATE TABLE article_draft
(
    article_id INTEGER,
    user_id    INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (article_id, user_id),
    FOREIGN KEY (article_id, user_id) REFERENCES articles (id, user_id)
);
