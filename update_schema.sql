-- Add status column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';

-- Update existing users to have a status of 'active'
UPDATE users SET status = 'active' WHERE status IS NULL;

-- Add unique constraint on email
ALTER TABLE users ADD CONSTRAINT IF NOT EXISTS unique_email UNIQUE (email);

-- Add created_at column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add updated_at column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at);
CREATE INDEX IF NOT EXISTS idx_users_updated_at ON users(updated_at);

-- Add NOT NULL constraints where required
ALTER TABLE users MODIFY COLUMN IF EXISTS first_name VARCHAR(255) NOT NULL;
ALTER TABLE users MODIFY COLUMN IF EXISTS last_name VARCHAR(255) NOT NULL;
ALTER TABLE users MODIFY COLUMN IF EXISTS email VARCHAR(255) NOT NULL;
ALTER TABLE users MODIFY COLUMN IF EXISTS password VARCHAR(255) NOT NULL;

-- Add phone column if it doesn't exist (optional)
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20);

-- Add soft delete column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Add trigger to update updated_at on any update
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END;
//
DELIMITER ;
