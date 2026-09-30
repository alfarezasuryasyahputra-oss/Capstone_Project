USE posyandu;

-- Relasi satu akun orang tua dengan banyak anak.
CREATE TABLE IF NOT EXISTS parent_children (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 parent_id INT UNSIGNED NOT NULL,
 child_id INT UNSIGNED NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_parent_child (parent_id, child_id),
 INDEX idx_pc_parent (parent_id),
 INDEX idx_pc_child (child_id)
);

-- Relasi akun dengan satu atau beberapa data ibu bila diperlukan.
CREATE TABLE IF NOT EXISTS parent_mothers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 parent_id INT UNSIGNED NOT NULL,
 mother_id INT UNSIGNED NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_parent_mother (parent_id, mother_id),
 INDEX idx_pm_parent (parent_id),
 INDEX idx_pm_mother (mother_id)
);

-- Migrasikan relasi lama dari parent_users agar akun lama tetap bekerja.
INSERT IGNORE INTO parent_children (parent_id, child_id)
SELECT id, child_id FROM parent_users WHERE child_id IS NOT NULL;

INSERT IGNORE INTO parent_mothers (parent_id, mother_id)
SELECT id, mother_id FROM parent_users WHERE mother_id IS NOT NULL;
