    CREATE TABLE IF NOT EXISTS questionnaires
    (
        id int(10) unsigned AUTO_INCREMENT NOT NULL PRIMARY KEY,
        shop_id int(10) unsigned NOT NULL,
        item varchar(50) NOT NULL,
        flavour tinyint(1) NOT NULL,
        opinion varchar(500) NOT NULL,
        created_at timestamp NOT NULL DEFAULT current_timestamp,
        updated_at timestamp NOT NULL DEFAULT current_timestamp ON UPDATE current_timestamp,

        FOREIGN KEY fk_shop_id (shop_id) REFERENCES shops (id)
    );