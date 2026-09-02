-- ---------------------------------------------------------------------------
-- 003 Content: page sections, blog, careers, services.
-- Each public page is a page row plus an ordered list of page_sections, which
-- is what the Page contents screen edits.
-- ---------------------------------------------------------------------------

CREATE TABLE pages (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug             VARCHAR(80)  NOT NULL,   -- home, about, contact, virtual-tours
    name             VARCHAR(120) NOT NULL,   -- shown in the admin sidebar
    title            VARCHAR(190) NULL,       -- <title>
    meta_description VARCHAR(255) NULL,
    og_image_id      INT UNSIGNED NULL,
    editable         TINYINT(1)   NOT NULL DEFAULT 1,
    sort             SMALLINT     NOT NULL DEFAULT 0,
    updated_at       DATETIME     NULL,
    updated_by       INT UNSIGNED NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_page_slug (slug),
    CONSTRAINT fk_page_og FOREIGN KEY (og_image_id) REFERENCES media (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One row per editable band. `data` is the section's own fields as JSON, so a
-- new band on a page is a row here rather than a schema change. `type` tells
-- the admin which field set to render.
CREATE TABLE page_sections (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    page_id     INT UNSIGNED NOT NULL,
    section_key VARCHAR(64)  NOT NULL,   -- matches src/sections/<key>.php
    name        VARCHAR(120) NOT NULL,   -- label in the admin
    type        VARCHAR(32)  NOT NULL DEFAULT 'fields',
    data        LONGTEXT     NULL,
    sort        SMALLINT     NOT NULL DEFAULT 0,
    enabled     TINYINT(1)   NOT NULL DEFAULT 1,
    updated_at  DATETIME     NULL,
    updated_by  INT UNSIGNED NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_page_section (page_id, section_key),
    KEY idx_section_sort (page_id, sort),
    CONSTRAINT fk_section_page FOREIGN KEY (page_id) REFERENCES pages (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_categories (
    id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL,
    sort SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_category_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug          VARCHAR(200) NOT NULL,
    category_id   INT UNSIGNED NULL,
    title         VARCHAR(200) NOT NULL,
    excerpt       TEXT         NULL,
    body          LONGTEXT     NULL,   -- paragraphs, "## " prefix marks a heading
    cover_id      INT UNSIGNED NULL,
    read_minutes  TINYINT UNSIGNED NULL,
    featured      TINYINT(1)   NOT NULL DEFAULT 0,
    published_at  DATETIME     NULL,   -- NULL means draft
    created_at    DATETIME     NOT NULL,
    updated_at    DATETIME     NULL,
    updated_by    INT UNSIGNED NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_post_slug (slug),
    KEY idx_post_published (published_at),
    KEY idx_post_category (category_id),
    CONSTRAINT fk_post_category FOREIGN KEY (category_id) REFERENCES post_categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_post_cover FOREIGN KEY (cover_id) REFERENCES media (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vacancies (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title        VARCHAR(190) NOT NULL,
    location     VARCHAR(190) NOT NULL,
    type         VARCHAR(48)  NOT NULL DEFAULT 'Full time',
    team         VARCHAR(80)  NULL,
    summary      TEXT         NULL,
    requirements LONGTEXT     NULL,   -- JSON array of bullet strings
    sort         SMALLINT     NOT NULL DEFAULT 0,
    published_at DATETIME     NULL,
    closed_at    DATETIME     NULL,
    created_at   DATETIME     NOT NULL,
    updated_at   DATETIME     NULL,
    updated_by   INT UNSIGNED NULL,
    PRIMARY KEY (id),
    KEY idx_vacancy_open (closed_at, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(80)  NOT NULL,
    title      VARCHAR(190) NOT NULL,
    note       VARCHAR(255) NULL,   -- the small gold line under the title
    body       TEXT         NULL,   -- only the six detailed ones have this
    icon       VARCHAR(48)  NOT NULL DEFAULT 'check',
    featured   TINYINT(1)   NOT NULL DEFAULT 0,   -- one of the six detailed cards
    sort       SMALLINT     NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     NULL,
    updated_by INT UNSIGNED NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_service_slug (slug),
    KEY idx_service_sort (featured, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
