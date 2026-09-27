-- Notices and alerts published by District Administrators (FR-07, FR-25).
-- Run once in phpMyAdmin on the resq_lanka database.

CREATE TABLE IF NOT EXISTS notices (
    notice_id     INT AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(150) NOT NULL,
    message       TEXT NOT NULL,
    notice_type   ENUM('notice', 'alert') NOT NULL DEFAULT 'notice',
    scope         ENUM('district', 'national') NOT NULL DEFAULT 'district',
    district      VARCHAR(50) NULL,
    published_by  INT NOT NULL,
    published_at  DATETIME NOT NULL,
    expires_at    DATETIME NULL,
    INDEX idx_notices_district (district),
    CONSTRAINT fk_notices_published_by
        FOREIGN KEY (published_by) REFERENCES users (user_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
