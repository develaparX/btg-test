
CREATE TABLE family_list (
    fl_id SERIAL PRIMARY KEY,
    cst_id INTEGER NOT NULL,
    fl_relation VARCHAR(50) NOT NULL,
    fl_name VARCHAR(50) NOT NULL,
    fl_dob VARCHAR(50) NOT NULL,
    FOREIGN KEY (cst_id) REFERENCES customer(cst_id) ON DELETE CASCADE
);

CREATE INDEX idx_family_list_customer ON family_list(cst_id);