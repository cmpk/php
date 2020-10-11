    CREATE TABLE IF NOT EXISTS shops
    (
        id int(10) unsigned AUTO_INCREMENT NOT NULL PRIMARY KEY,
        name varchar(50) NOT NULL,
        is_enabled boolean NOT NULL DEFAULT false,
        created_at timestamp NOT NULL DEFAULT current_timestamp,
        updated_at timestamp NOT NULL DEFAULT current_timestamp ON UPDATE current_timestamp
    );