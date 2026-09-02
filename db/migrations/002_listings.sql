-- ---------------------------------------------------------------------------
-- 002 Listings: the inventory behind Selling, Rentals and Airbnb.
-- Column names match the array keys already used in src/data/listings.php,
-- so the front end templates need no changes.
-- ---------------------------------------------------------------------------

CREATE TABLE locations (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120) NOT NULL,
    city       VARCHAR(120) NOT NULL,
    slug       VARCHAR(140) NOT NULL,
    featured   TINYINT(1)   NOT NULL DEFAULT 0,
    sort       SMALLINT     NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_location_slug (slug),
    KEY idx_location_featured (featured, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE listings (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ref           VARCHAR(24)   NOT NULL,               -- DD-1042, shown to clients
    -- 'For sale' | 'To rent' | 'Short stay'. Kept as text rather than an ENUM
    -- so a fourth section does not need a schema change.
    basis         VARCHAR(24)   NOT NULL,
    -- houses | apartments | commercial | land
    category      VARCHAR(24)   NOT NULL,
    title         VARCHAR(190)  NOT NULL,
    slug          VARCHAR(200)  NULL,
    address       VARCHAR(190)  NOT NULL,
    location_id   INT UNSIGNED  NULL,
    price         DECIMAL(12,2) NOT NULL DEFAULT 0,
    currency      CHAR(3)       NOT NULL DEFAULT 'USD',
    period        VARCHAR(24)   NULL,                   -- per month | per night | NULL
    status        VARCHAR(32)   NOT NULL DEFAULT '',    -- New build, Off-plan, Furnished
    beds          TINYINT UNSIGNED NULL,
    baths         TINYINT UNSIGNED NULL,
    area          MEDIUMINT UNSIGNED NULL,              -- square metres
    summary       TEXT          NULL,                   -- the card blurb
    description   MEDIUMTEXT    NULL,                   -- the detail page body
    cover_id      INT UNSIGNED  NULL,
    verified      TINYINT(1)    NOT NULL DEFAULT 1,
    featured      TINYINT(1)    NOT NULL DEFAULT 0,     -- shows in the home page trio
    published_at  DATETIME      NULL,                   -- NULL means draft
    archived_at   DATETIME      NULL,                   -- Admins archive, never delete
    created_at    DATETIME      NOT NULL,
    updated_at    DATETIME      NULL,
    updated_by    INT UNSIGNED  NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_listing_ref (ref),
    KEY idx_listing_browse (basis, category, published_at),
    KEY idx_listing_price (price),
    KEY idx_listing_featured (featured, published_at),
    KEY idx_listing_location (location_id),
    CONSTRAINT fk_listing_location FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE SET NULL,
    CONSTRAINT fk_listing_cover FOREIGN KEY (cover_id) REFERENCES media (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE listing_images (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    listing_id INT UNSIGNED NOT NULL,
    media_id   INT UNSIGNED NOT NULL,
    sort       SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_listing_media (listing_id, media_id),
    KEY idx_listing_images_sort (listing_id, sort),
    CONSTRAINT fk_li_listing FOREIGN KEY (listing_id) REFERENCES listings (id) ON DELETE CASCADE,
    CONSTRAINT fk_li_media FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The gold tag chips on each card.
CREATE TABLE listing_features (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    listing_id INT UNSIGNED NOT NULL,
    label      VARCHAR(120) NOT NULL,
    sort       SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_feature_listing (listing_id, sort),
    CONSTRAINT fk_feature_listing FOREIGN KEY (listing_id) REFERENCES listings (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
