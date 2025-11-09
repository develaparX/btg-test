
CREATE TABLE nationality (
    nationality_id SERIAL PRIMARY KEY,
    nationality_name VARCHAR(50) NOT NULL,
    nationality_code CHAR(2) NOT NULL
);

-- Insert some sample data
INSERT INTO nationality (nationality_name, nationality_code) VALUES
('Indonesian', 'ID'),
('American', 'US'),
('British', 'GB'),
('Australian', 'AU'),
('Japanese', 'JP'),
('Korean', 'KR'),
('Chinese', 'CN'),
('Indian', 'IN'),
('German', 'DE'),
('French', 'FR');