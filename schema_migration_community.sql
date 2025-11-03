-- Migration: community contributions
ALTER TABLE plants
  ADD COLUMN Created_By INT NULL AFTER ID,
  ADD COLUMN Is_Public TINYINT(1) NOT NULL DEFAULT 1 AFTER Image;
ALTER TABLE plants
  ADD CONSTRAINT fk_plants_created_by FOREIGN KEY (Created_By) REFERENCES users(ID) ON DELETE SET NULL;
