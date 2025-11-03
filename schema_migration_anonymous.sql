-- Migration: anonymous contributions + moderation
ALTER TABLE plants
  ADD COLUMN Status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved' AFTER Is_Public,
  ADD COLUMN Submitted_Email VARCHAR(150) NULL AFTER Status;
-- For existing rows treat them as approved
UPDATE plants SET Status = 'approved' WHERE Status IS NULL;
