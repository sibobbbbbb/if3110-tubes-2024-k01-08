-- Users
CREATE OR REPLACE TYPE user_role AS ENUM ('jobseeker', 'company');

CREATE OR REPLACE TABLE users (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role user_role NOT NULL
);

-- Company Details
CREATE OR REPLACE TABLE company_details (
  user_id INTEGER PRIMARY KEY,
  lokasi VARCHAR(255) NOT NULL,
  about TEXT NOT NULL,

  FOREIGN KEY (user_id) REFERENCES users(id)
);


-- Lowongan Kerja
CREATE OR REPLACE TYPE jenis_pekerjaan_enum AS ENUM ('full-time', 'part-time', 'internship');

CREATE OR REPLACE TYPE jenis_lokasi_enum AS ENUM ('on-site', 'hybrid', 'remote');

CREATE OR REPLACE TABLE lowongan (
  lowongan_id SERIAL PRIMARY KEY,
  company_id INTEGER NOT NULL,
  posisi VARCHAR(255) NOT NULL,
  deskripsi TEXT NOT NULL,
  jenis_pekerjaan jenis_pekerjaan_enum NOT NULL,
  jenis_lokasi jenis_lokasi_enum NOT NULL,
  is_open BOOLEAN DEFAULT true NOT NULL,
  created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (company_id) REFERENCES users(id)
);

-- Attachment Lowongan
CREATE OR REPLACE TABLE attachment_lowongan (
    attachment_id SERIAL PRIMARY KEY,
    lowongan_id INTEGER NOT NULL,
    file_path VARCHAR(255) NOT NULL,

    FOREIGN KEY (lowongan_id) REFERENCES lowongan(lowongan_id) ON DELETE CASCADE
);

-- Lamaran
CREATE OR REPLACE TYPE lamaran_status AS ENUM ('accepted', 'rejected', 'waiting');
CREATE OR REPLACE TABLE lamaran (
    lamaran_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    lowongan_id INTEGER NOT NULL,
    cv_path VARCHAR(255) NOT NULL,
    video_path VARCHAR(255),
    status lamaran_status DEFAULT 'waiting',
    status_reason TEXT,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (lowongan_id) REFERENCES lowongan(lowongan_id) ON DELETE SET NULL
    -- ON DELETE SET NULL agar saat lowongan dihapus, tampilkan lowongan tersebut telah dihapus;
);

-- Trigger for updated at
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER 
LANGUAGE PLpgSQL AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;

CREATE OR REPLACE TRIGGER update_lowongan_updated_at
BEFORE UPDATE ON lowongan
FOR EACH ROW
EXECUTE FUNCTION update_updated_at();

-- Trigger to validate that user_id on lowongan and company_details is referencing to a company
CREATE OR REPLACE FUNCTION validate_company_user()
RETURNS TRIGGER
LANGUAGE PLpgSQL AS $$
BEGIN
  IF (SELECT role FROM users WHERE id = NEW.user_id) <> 'company' THEN
    RAISE EXCEPTION 'User with id % is not a company', NEW.user_id;
  END IF;

  RETURN NEW;
END;
$$;

CREATE OR REPLACE TRIGGER validate_company_user_on_lowongan
BEFORE INSERT ON lowongan
FOR EACH ROW
EXECUTE FUNCTION validate_company_user();

CREATE OR REPLACE TRIGGER validate_company_user_on_company_details
BEFORE INSERT ON company_details
FOR EACH ROW
EXECUTE FUNCTION validate_company_user();

-- Trigger to validate that user_id on lamaran is referencing to a jobseeker
CREATE OR REPLACE FUNCTION validate_jobseeker_user()
RETURNS TRIGGER
LANGUAGE PLpgSQL AS $$
BEGIN
  IF (SELECT role FROM users WHERE id = NEW.user_id) <> 'jobseeker' THEN
    RAISE EXCEPTION 'User with id % is not a jobseeker', NEW.user_id;
  END IF;

  RETURN NEW;
END
$$;

CREATE OR REPLACE TRIGGER validate_jobseeker_user_on_lamaran
BEFORE INSERT ON lamaran
FOR EACH ROW
EXECUTE FUNCTION validate_jobseeker_user();