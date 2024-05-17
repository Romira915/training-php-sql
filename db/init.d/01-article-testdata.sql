DO
$$
    DECLARE
        article_id   INT;
        thumbnail_id INT;
    BEGIN
        FOR i IN 1..1000
            LOOP
                INSERT INTO articles (id, user_id)
                VALUES ((SELECT COALESCE(MAX(article_id), 0) + 1 FROM articles WHERE user_id = 1), 1)
                RETURNING id INTO article_id;
                INSERT INTO article_published (article_id, user_id) VALUES (article_id, 1);
                INSERT INTO article_images (article_id, user_id, image_url)
                VALUES (article_id, 1, '/images/image.png')
                RETURNING id INTO thumbnail_id;
                INSERT INTO article_images (article_id, user_id, image_url) VALUES (article_id, 1, '/images/image.png');
                INSERT INTO article_detail (article_id, user_id, title, body, thumbnail_id)
                VALUES (article_id, 1, 'title ' || article_id, 'body ' || article_id, thumbnail_id);
            END LOOP;
    END;
$$;
