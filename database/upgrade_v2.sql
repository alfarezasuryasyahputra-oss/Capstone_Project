USE posyandu;

-- 1. Tambahan NIK ibu hamil
ALTER TABLE ibu_hamil ADD COLUMN nik VARCHAR(20) NULL AFTER id;
CREATE UNIQUE INDEX idx_ibu_hamil_nik ON ibu_hamil (nik);

-- 2. Jam appointment
ALTER TABLE appointments ADD COLUMN requested_time TIME NULL AFTER requested_date;

-- 3. Hubungan balita dengan ibu (data lama tetap aman)
ALTER TABLE bayi_balita ADD COLUMN mother_id INT UNSIGNED NULL AFTER mother_name;
ALTER TABLE bayi_balita ADD INDEX idx_bayi_mother (mother_id);

-- 4. Jadwal Posyandu
CREATE TABLE IF NOT EXISTS jadwal_posyandu (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(150) NOT NULL,
 event_date DATE NOT NULL,
 event_time TIME NULL,
 location VARCHAR(200) NULL,
 description TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Pelayanan / KMS digital balita
CREATE TABLE IF NOT EXISTS pelayanan_balita (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 child_id INT UNSIGNED NOT NULL,
 service_date DATE NOT NULL,
 weight DECIMAL(5,2) NULL,
 height DECIMAL(5,2) NULL,
 head_circumference DECIMAL(5,2) NULL,
 nutrition_status VARCHAR(80) NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_pb_child_date (child_id, service_date)
);

-- 6. Imunisasi balita
CREATE TABLE IF NOT EXISTS imunisasi (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 child_id INT UNSIGNED NOT NULL,
 immunization_date DATE NOT NULL,
 vaccine_name VARCHAR(120) NOT NULL,
 dose VARCHAR(80) NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_imunisasi_child (child_id)
);

-- 7. Vitamin balita
CREATE TABLE IF NOT EXISTS vitamin (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 child_id INT UNSIGNED NOT NULL,
 vitamin_date DATE NOT NULL,
 vitamin_name VARCHAR(120) NOT NULL,
 dose VARCHAR(80) NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_vitamin_child (child_id)
);

-- 8. Pemeriksaan ANC ibu hamil
CREATE TABLE IF NOT EXISTS pemeriksaan_ibu_hamil (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 mother_id INT UNSIGNED NOT NULL,
 examination_date DATE NOT NULL,
 gestational_age_weeks DECIMAL(4,1) NULL,
 lila DECIMAL(5,2) NULL,
 weight DECIMAL(5,2) NULL,
 blood_pressure VARCHAR(30) NULL,
 complaints TEXT NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_anc_mother_date (mother_id, examination_date)
);

-- 9. Vitamin/suplemen ibu hamil
CREATE TABLE IF NOT EXISTS vitamin_ibu_hamil (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 mother_id INT UNSIGNED NOT NULL,
 vitamin_date DATE NOT NULL,
 supplement_name VARCHAR(120) NOT NULL,
 dose VARCHAR(80) NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_vih_mother (mother_id)
);

-- 10. Akun orang tua/ibu
CREATE TABLE IF NOT EXISTS parent_users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 name VARCHAR(150) NOT NULL,
 phone VARCHAR(30) NULL,
 mother_id INT UNSIGNED NULL,
 child_id INT UNSIGNED NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_parent_mother (mother_id),
 INDEX idx_parent_child (child_id)
);
