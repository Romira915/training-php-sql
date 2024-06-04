-- Create test user data
DO
$$
    DECLARE
        user_id INT;
    BEGIN
        FOR i IN 1..100
            LOOP
                INSERT INTO users DEFAULT VALUES RETURNING id INTO user_id;
                INSERT INTO user_detail (user_id, display_name, icon_path)
                VALUES (user_id, 'testuser' || user_id, '/images/icon.png');
                -- password: password
                INSERT INTO user_hashed_password (user_id, hashed_password)
                VALUES (user_id, '$2y$10$P/ds2511WmZRZlAf3.DZIu.EOubgcqxNpdO32ONQcO0R6fvlpvM0m');
            END LOOP;
    END;
$$;

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
                INSERT INTO article_images (article_id, user_id, image_path)
                VALUES (article_id, 1, '/images/image.png')
                RETURNING id INTO thumbnail_id;
                INSERT INTO article_images (article_id, user_id, image_path)
                VALUES (article_id, 1, '/images/image.png');
                INSERT INTO article_detail (article_id, user_id, title, body, thumbnail_id)
                VALUES (article_id, 1, 'title ' || article_id, 'body ' || article_id, thumbnail_id);
            END LOOP;
    END;
$$;
