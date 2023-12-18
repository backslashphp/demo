CREATE TABLE IF NOT EXISTS `event_store`
(
    `sequence`          INTEGER PRIMARY KEY,
    `aggregate_type`    TEXT       NOT NULL,
    `aggregate_id`      TEXT       NOT NULL,
    `aggregate_version` INTEGER    NOT NULL,
    `event_id`          TEXT       NOT NULL,
    `event_class`       TEXT       NOT NULL,
    `event_metadata`    MEDIUMTEXT NOT NULL,
    `event_payload`     MEDIUMTEXT NOT NULL,
    `event_time`        TEXT       NOT NULL,
    UNIQUE (`aggregate_type`, `aggregate_id`, `aggregate_version`)
);
