-- Office contact number for District Administrator accounts.
-- Run once in phpMyAdmin on the resq_lanka database.

ALTER TABLE users
    ADD COLUMN office_contact VARCHAR(20) NULL AFTER address;
