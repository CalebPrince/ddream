-- ---------------------------------------------------------------------------
-- 001 Core: accounts, authentication, settings, media, audit trail.
-- MySQL 5.7+ / MariaDB 10.3+. InnoDB and utf8mb4 throughout.
-- ---------------------------------------------------------------------------

CREATE TABLE users (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(120)  NOT NULL,
    email           VARCHAR(190)  NOT NULL,
    password_hash   VARCHAR(255)  NOT NULL,
    -- Not an ENUM, so a new role is a change to the capability map rather
    -- than a schema migration. See src/admin/capabilities.php.
    role            VARCHAR(32)   NOT NULL DEFAULT 'admin',
    status          VARCHAR(16)   NOT NULL DEFAULT 'active',
    last_login_at   DATETIME      NULL,
    created_at      DATETIME      NOT NULL,
    updated_at      DATETIME      NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Throttling. Rows are pruned by the login controller, not kept forever.
CREATE TABLE login_attempts (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email        VARCHAR(190) NOT NULL,
    ip           VARCHAR(45)  NOT NULL,
    succeeded    TINYINT(1)   NOT NULL DEFAULT 0,
    attempted_at DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_attempts_email (email, attempted_at),
    KEY idx_attempts_ip (ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    token_hash CHAR(64)     NOT NULL,
    expires_at DATETIME     NOT NULL,
    used_at    DATETIME     NULL,
    created_at DATETIME     NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_reset_token (token_hash),
    KEY idx_reset_user (user_id),
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Key/value site configuration. `group_name` drives the Settings tabs.
CREATE TABLE settings (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    group_name  VARCHAR(32)  NOT NULL,
    setting_key VARCHAR(64)  NOT NULL,
    value       TEXT         NULL,
    type        VARCHAR(16)  NOT NULL DEFAULT 'text',
    label       VARCHAR(160) NOT NULL,
    hint        VARCHAR(255) NULL,
    sort        SMALLINT     NOT NULL DEFAULT 0,
    updated_at  DATETIME     NULL,
    updated_by  INT UNSIGNED NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_setting_key (setting_key),
    KEY idx_setting_group (group_name, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    path        VARCHAR(255) NOT NULL,
    alt         VARCHAR(255) NOT NULL DEFAULT '',
    title       VARCHAR(190) NULL,
    mime        VARCHAR(64)  NULL,
    width       SMALLINT UNSIGNED NULL,
    height      SMALLINT UNSIGNED NULL,
    bytes       INT UNSIGNED NULL,
    uploaded_by INT UNSIGNED NULL,
    created_at  DATETIME     NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_media_path (path),
    KEY idx_media_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Who changed what. Written by src/admin/activity.php on every write.
CREATE TABLE activity_log (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NULL,
    user_name  VARCHAR(120) NULL,
    action     VARCHAR(32)  NOT NULL,
    entity     VARCHAR(48)  NOT NULL,
    entity_id  VARCHAR(64)  NULL,
    summary    VARCHAR(255) NULL,
    changes    TEXT         NULL,
    ip         VARCHAR(45)  NULL,
    created_at DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_activity_created (created_at),
    KEY idx_activity_entity (entity, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
