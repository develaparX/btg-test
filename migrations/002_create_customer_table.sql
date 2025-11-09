
CREATE TABLE customer (
    cst_id SERIAL PRIMARY KEY,
    nationality_id INTEGER NOT NULL,
    cst_name VARCHAR(50) NOT NULL,
    cst_dob DATE NOT NULL,
    cst_phoneNum VARCHAR(20) NOT NULL,
    cst_email VARCHAR(50) NOT NULL,
    FOREIGN KEY (nationality_id) REFERENCES nationality(nationality_id)
);

CREATE INDEX idx_customer_nationality ON customer(nationality_id);
CREATE INDEX idx_customer_email ON customer(cst_email);