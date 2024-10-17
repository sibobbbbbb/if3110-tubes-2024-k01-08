-- Users
CREATE TYPE user_role AS ENUM ('jobseeker', 'company');

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role user_role NOT NULL
);

-- Company Details
CREATE TABLE company_details (
  user_id INTEGER PRIMARY KEY,
  location VARCHAR(255) NOT NULL,
  about TEXT NOT NULL,

  FOREIGN KEY (user_id) REFERENCES users(id)
);


-- job Kerja
CREATE TYPE job_type_enum AS ENUM ('full-time', 'part-time', 'internship');

CREATE TYPE location_type_enum AS ENUM ('on-site', 'hybrid', 'remote');

BLE jobs (
  job_id SERIAL PRIMARY KEY,
  company_id INTEGER NOT NULL,
  position VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  job_type job_type_enum NOT NULL,
  location_type location_type_enum NOT NULL,
  is_open BOOLEAN DEFAULT true NOT NULL,
  created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (company_id) REFERENCES users(id)
);

-- Attachment job
CREATE TABLE job_attachment (
    attachment_id SERIAL PRIMARY KEY,
    job_id INTEGER NOT NULL,
    file_path VARCHAR(255) NOT NULL,

    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
);

-- application
CREATE TYPE application_status_enum AS ENUM ('accepted', 'rejected', 'waiting');

CREATE TABLE applications (
    application_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    job_id INTEGER NOT NULL,
    cv_path VARCHAR(255) NOT NULL,
    video_path VARCHAR(255),
    status application_status_enum DEFAULT 'waiting',
    status_reason TEXT,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE SET NULL
    -- ON DELETE SET NULL agar saat job dihapus, tampilkan job tersebut telah dihapus;
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

CREATE OR REPLACE TRIGGER update_jobs_updated_at
BEFORE UPDATE ON jobs
FOR EACH ROW
EXECUTE FUNCTION update_updated_at();

-- Trigger to validate that user_id on jobs and company_details is referencing to a company
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

CREATE OR REPLACE TRIGGER validate_company_user_on_jobs
BEFORE INSERT ON jobs
FOR EACH ROW
EXECUTE FUNCTION validate_company_user();

CREATE OR REPLACE TRIGGER validate_company_user_on_company_details
BEFORE INSERT ON company_details
FOR EACH ROW
EXECUTE FUNCTION validate_company_user();

-- Trigger to validate that user_id on application is referencing to a jobseeker
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

CREATE OR REPLACE TRIGGER validate_jobseeker_user_on_applications
BEFORE INSERT ON applications
FOR EACH ROW
EXECUTE FUNCTION validate_jobseeker_user();