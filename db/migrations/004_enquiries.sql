-- ---------------------------------------------------------------------------
-- 004 Enquiries: the Inbox.
-- Staff reply from their own email client, so there is no outbound message
-- table here. enquiry_notes holds internal notes only, never seen by the sender.
-- ---------------------------------------------------------------------------

CREATE TABLE enquiries (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    -- contact | consultation | viewing | tour | career
    type        VARCHAR(24)  NOT NULL DEFAULT 'contact',
    name        VARCHAR(160) NOT NULL,
    email       VARCHAR(190) NOT NULL,
    phone       VARCHAR(48)  NULL,
    country     VARCHAR(120) NULL,
    interest    VARCHAR(120) NULL,
    location    VARCHAR(160) NULL,
    budget      VARCHAR(64)  NULL,
    timeline    VARCHAR(64)  NULL,
    method      VARCHAR(32)  NULL,   -- Email, WhatsApp, Phone call, Video call
    best_time   VARCHAR(64)  NULL,
    message     TEXT         NULL,
    listing_id  INT UNSIGNED NULL,   -- set when sent from a property page
    listing_ref VARCHAR(24)  NULL,   -- kept even if the listing is later removed
    source_url  VARCHAR(255) NULL,
    ip          VARCHAR(45)  NULL,
    user_agent  VARCHAR(255) NULL,
    -- new | assigned | replied | closed
    status      VARCHAR(16)  NOT NULL DEFAULT 'new',
    assigned_to INT UNSIGNED NULL,
    read_at     DATETIME     NULL,
    replied_at  DATETIME     NULL,   -- set by hand when staff have answered by email
    closed_at   DATETIME     NULL,
    created_at  DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_enquiry_queue (status, created_at),
    KEY idx_enquiry_type (type, created_at),
    KEY idx_enquiry_assigned (assigned_to, status),
    KEY idx_enquiry_listing (listing_id),
    CONSTRAINT fk_enquiry_listing FOREIGN KEY (listing_id) REFERENCES listings (id) ON DELETE SET NULL,
    CONSTRAINT fk_enquiry_user FOREIGN KEY (assigned_to) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Internal only. The enquirer never sees these.
CREATE TABLE enquiry_notes (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    enquiry_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NULL,
    user_name   VARCHAR(120) NULL,
    body        TEXT         NOT NULL,
    created_at  DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_note_enquiry (enquiry_id, created_at),
    CONSTRAINT fk_note_enquiry FOREIGN KEY (enquiry_id) REFERENCES enquiries (id) ON DELETE CASCADE,
    CONSTRAINT fk_note_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscribers (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email           VARCHAR(190) NOT NULL,
    source_url      VARCHAR(255) NULL,
    ip              VARCHAR(45)  NULL,
    confirmed_at    DATETIME     NULL,
    unsubscribed_at DATETIME     NULL,
    created_at      DATETIME     NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_subscriber_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- In-app bell. Email notification is separate and configured in Settings.
CREATE TABLE notifications (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NULL,   -- NULL means everyone
    type       VARCHAR(32)  NOT NULL,
    title      VARCHAR(190) NOT NULL,
    body       VARCHAR(255) NULL,
    url        VARCHAR(255) NULL,
    read_at    DATETIME     NULL,
    created_at DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_notification_user (user_id, read_at, created_at),
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
