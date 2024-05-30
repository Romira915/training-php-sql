ALTER TABLE article_images
    DROP CONSTRAINT article_images_article_id_user_id_fkey;
ALTER TABLE article_images
    ADD FOREIGN KEY (article_id, user_id) REFERENCES article_detail (article_id, user_id) ON DELETE CASCADE;
