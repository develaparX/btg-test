package customer

import (
	"booking-api/internal/models"
	"context"
	"database/sql"
	"fmt"
)

type Repository interface {
	CreateCustomer(ctx context.Context, customer *models.Customer) (*models.Customer, error)
	GetCustomerByID(ctx context.Context, id int) (*models.Customer, error)
	GetAllCustomers(ctx context.Context) ([]models.Customer, error)
	UpdateCustomer(ctx context.Context, customer *models.Customer) (*models.Customer, error)
	DeleteCustomer(ctx context.Context, id int) error
	
	CreateFamilyMembers(ctx context.Context, customerID int, familyMembers []models.FamilyMember) error
	GetFamilyMembersByCustomerID(ctx context.Context, customerID int) ([]models.FamilyMember, error)
	DeleteFamilyMembersByCustomerID(ctx context.Context, customerID int) error
	
	GetAllNationalities(ctx context.Context) ([]models.Nationality, error)
	GetNationalityByID(ctx context.Context, id int) (*models.Nationality, error)
}

type repository struct {
	db *sql.DB
}

func NewRepository(db *sql.DB) Repository {
	return &repository{db: db}
}

func (r *repository) CreateCustomer(ctx context.Context, customer *models.Customer) (*models.Customer, error) {
	query := `
		INSERT INTO customer (nationality_id, cst_name, cst_dob, cst_phonenum, cst_email)
		VALUES ($1, $2, $3, $4, $5)
		RETURNING cst_id`

	var id int
	err := r.db.QueryRowContext(ctx, query,
		customer.NationalityID,
		customer.Name,
		customer.DateOfBirth,
		customer.PhoneNumber,
		customer.Email,
	).Scan(&id)

	if err != nil {
		return nil, fmt.Errorf("failed to create customer: %w", err)
	}

	customer.ID = id
	return customer, nil
}

func (r *repository) GetCustomerByID(ctx context.Context, id int) (*models.Customer, error) {
	query := `
		SELECT c.cst_id, c.nationality_id, c.cst_name, c.cst_dob, c.cst_phonenum, c.cst_email,
			   n.nationality_id, n.nationality_name, n.nationality_code
		FROM customer c
		LEFT JOIN nationality n ON c.nationality_id = n.nationality_id
		WHERE c.cst_id = $1`

	var customer models.Customer
	var nationality models.Nationality

	err := r.db.QueryRowContext(ctx, query, id).Scan(
		&customer.ID,
		&customer.NationalityID,
		&customer.Name,
		&customer.DateOfBirth,
		&customer.PhoneNumber,
		&customer.Email,
		&nationality.ID,
		&nationality.Name,
		&nationality.Code,
	)

	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("customer not found")
		}
		return nil, fmt.Errorf("failed to get customer: %w", err)
	}

	customer.Nationality = &nationality
	return &customer, nil
}

func (r *repository) GetAllCustomers(ctx context.Context) ([]models.Customer, error) {
	query := `
		SELECT c.cst_id, c.nationality_id, c.cst_name, c.cst_dob, c.cst_phonenum, c.cst_email,
			   n.nationality_id, n.nationality_name, n.nationality_code
		FROM customer c
		LEFT JOIN nationality n ON c.nationality_id = n.nationality_id
		ORDER BY c.cst_id`

	rows, err := r.db.QueryContext(ctx, query)
	if err != nil {
		return nil, fmt.Errorf("failed to get customers: %w", err)
	}
	defer rows.Close()

	var customers []models.Customer
	for rows.Next() {
		var customer models.Customer
		var nationality models.Nationality

		err := rows.Scan(
			&customer.ID,
			&customer.NationalityID,
			&customer.Name,
			&customer.DateOfBirth,
			&customer.PhoneNumber,
			&customer.Email,
			&nationality.ID,
			&nationality.Name,
			&nationality.Code,
		)
		if err != nil {
			return nil, fmt.Errorf("failed to scan customer: %w", err)
		}

		customer.Nationality = &nationality
		customers = append(customers, customer)
	}

	return customers, nil
}

func (r *repository) UpdateCustomer(ctx context.Context, customer *models.Customer) (*models.Customer, error) {
	query := `
		UPDATE customer 
		SET nationality_id = $2, cst_name = $3, cst_dob = $4, cst_phonenum = $5, cst_email = $6
		WHERE cst_id = $1`

	_, err := r.db.ExecContext(ctx, query,
		customer.ID,
		customer.NationalityID,
		customer.Name,
		customer.DateOfBirth,
		customer.PhoneNumber,
		customer.Email,
	)

	if err != nil {
		return nil, fmt.Errorf("failed to update customer: %w", err)
	}

	return customer, nil
}

func (r *repository) DeleteCustomer(ctx context.Context, id int) error {
	query := `DELETE FROM customer WHERE cst_id = $1`

	result, err := r.db.ExecContext(ctx, query, id)
	if err != nil {
		return fmt.Errorf("failed to delete customer: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("customer not found")
	}

	return nil
}

func (r *repository) CreateFamilyMembers(ctx context.Context, customerID int, familyMembers []models.FamilyMember) error {
	if len(familyMembers) == 0 {
		return nil
	}

	query := `INSERT INTO family_list (cst_id, fl_relation, fl_name, fl_dob) VALUES ($1, $2, $3, $4)`

	for _, member := range familyMembers {
		_, err := r.db.ExecContext(ctx, query,
			customerID,
			member.Relation,
			member.Name,
			member.DateOfBirth,
		)
		if err != nil {
			return fmt.Errorf("failed to create family member: %w", err)
		}
	}

	return nil
}

func (r *repository) GetFamilyMembersByCustomerID(ctx context.Context, customerID int) ([]models.FamilyMember, error) {
	query := `SELECT fl_id, cst_id, fl_relation, fl_name, fl_dob FROM family_list WHERE cst_id = $1 ORDER BY fl_id`

	rows, err := r.db.QueryContext(ctx, query, customerID)
	if err != nil {
		return nil, fmt.Errorf("failed to get family members: %w", err)
	}
	defer rows.Close()

	var familyMembers []models.FamilyMember
	for rows.Next() {
		var member models.FamilyMember
		err := rows.Scan(
			&member.ID,
			&member.CustomerID,
			&member.Relation,
			&member.Name,
			&member.DateOfBirth,
		)
		if err != nil {
			return nil, fmt.Errorf("failed to scan family member: %w", err)
		}
		familyMembers = append(familyMembers, member)
	}

	return familyMembers, nil
}

func (r *repository) DeleteFamilyMembersByCustomerID(ctx context.Context, customerID int) error {
	query := `DELETE FROM family_list WHERE cst_id = $1`

	_, err := r.db.ExecContext(ctx, query, customerID)
	if err != nil {
		return fmt.Errorf("failed to delete family members: %w", err)
	}

	return nil
}

func (r *repository) GetAllNationalities(ctx context.Context) ([]models.Nationality, error) {
	query := `SELECT nationality_id, nationality_name, nationality_code FROM nationality ORDER BY nationality_name`

	rows, err := r.db.QueryContext(ctx, query)
	if err != nil {
		return nil, fmt.Errorf("failed to get nationalities: %w", err)
	}
	defer rows.Close()

	var nationalities []models.Nationality
	for rows.Next() {
		var nationality models.Nationality
		err := rows.Scan(&nationality.ID, &nationality.Name, &nationality.Code)
		if err != nil {
			return nil, fmt.Errorf("failed to scan nationality: %w", err)
		}
		nationalities = append(nationalities, nationality)
	}

	return nationalities, nil
}

func (r *repository) GetNationalityByID(ctx context.Context, id int) (*models.Nationality, error) {
	query := `SELECT nationality_id, nationality_name, nationality_code FROM nationality WHERE nationality_id = $1`

	var nationality models.Nationality
	err := r.db.QueryRowContext(ctx, query, id).Scan(
		&nationality.ID,
		&nationality.Name,
		&nationality.Code,
	)

	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("nationality not found")
		}
		return nil, fmt.Errorf("failed to get nationality: %w", err)
	}

	return &nationality, nil
}