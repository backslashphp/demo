CREATE TABLE IF NOT EXISTS `projection_store`
(
    `projection_id`      TEXT       NOT NULL,
    `projection_class`   TEXT       NOT NULL,
    `projection_payload` MEDIUMTEXT NOT NULL,
    UNIQUE (`projection_id`, `projection_class`)
);
