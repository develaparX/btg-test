package customer

import (
	"booking-api/internal/models"
	"context"
	"fmt"
	"time"
)

type Service interface {
	CreateCustomer(ctx context.Context, req *models.CreateCustomerRequest) (*models.Customer, error)
	GetCustomerByID(ctx context.Context, id int) (*models.Customer, error)
	GetAllCustomers(ctx context.Context) ([]models.Customer, error)
	UpdateCustomer(ctx context.Context, id int, req *models.UpdateCustomerRequest) (*models.Customer, error)
	DeleteCustomer(ctx context.Context, id int) error
	GetAllNationalities(ctx context.Context) ([]models.Nationality, error)
}

type service struct {
	repo Repository
}

func NewService(repo Repository) Service {
	return &service{repo: repo}
}

func (s *service) CreateCustomer(ctx context.Context, req *models.CreateCustomerRequest) (*models.Customer, error) {
	// Validate nationality exists
	_, err := s.repo.GetNationalityByID(ctx, req.NationalityID)
	if err != nil {
		return nil, models.NewAppError(400, "Invalid nationality", err.Error())
	}

	// Parse date of birth
	dob, err := time.Parse("2006-01-02", req.DateOfBirth)
	if err != nil {
		return nil, models.NewAppError(400, "Invalid date format", err.Error())
	}

	// Create customer entity
	customer := &models.Customer{
		NationalityID: req.NationalityID,
		Name:          req.Name,
		DateOfBirth:   dob,
		PhoneNumber:   req.PhoneNumber,
		Email:         req.Email,
	}

	// Create customer
	createdCustomer, err := s.repo.CreateCustomer(ctx, customer)
	if err != nil {
		return nil, models.NewAppError(500, "Failed to create customer", err.Error())
	}

	// Create family members if provided
	if len(req.FamilyList) > 0 {
		var familyMembers []models.FamilyMember
		for _, familyReq := range req.FamilyList {
			familyMember := models.FamilyMember{
				CustomerID:  createdCustomer.ID,
				Relation:    familyReq.Relation,
				Name:        familyReq.Name,
				DateOfBirth: familyReq.DateOfBirth,
			}
			familyMembers = append(familyMembers, familyMember)
		}

		err = s.repo.CreateFamilyMembers(ctx, createdCustomer.ID, familyMembers)
		if err != nil {
			return nil, models.NewAppError(500, "Failed to create family members", err.Error())
		}
	}

	// Get complete customer data
	return s.GetCustomerByID(ctx, createdCustomer.ID)
}

func (s *service) GetCustomerByID(ctx context.Context, id int) (*models.Customer, error) {
	customer, err := s.repo.GetCustomerByID(ctx, id)
	if err != nil {
		return nil, models.NewAppError(404, "Customer not found", err.Error())
	}

	// Get family members
	familyMembers, err := s.repo.GetFamilyMembersByCustomerID(ctx, id)
	if err != nil {
		return nil, models.NewAppError(500, "Failed to get family members", err.Error())
	}

	customer.FamilyList = familyMembers
	return customer, nil
}

func (s *service) GetAllCustomers(ctx context.Context) ([]models.Customer, error) {
	customers, err := s.repo.GetAllCustomers(ctx)
	if err != nil {
		return nil, models.NewAppError(500, "Failed to get customers", err.Error())
	}

	// Get family members for each customer
	for i := range customers {
		familyMembers, err := s.repo.GetFamilyMembersByCustomerID(ctx, customers[i].ID)
		if err != nil {
			return nil, models.NewAppError(500, fmt.Sprintf("Failed to get family members for customer %d", customers[i].ID), err.Error())
		}
		customers[i].FamilyList = familyMembers
	}

	return customers, nil
}

func (s *service) UpdateCustomer(ctx context.Context, id int, req *models.UpdateCustomerRequest) (*models.Customer, error) {
	// Check if customer exists
	existingCustomer, err := s.repo.GetCustomerByID(ctx, id)
	if err != nil {
		return nil, models.NewAppError(404, "Customer not found", err.Error())
	}

	// Validate nationality exists
	_, err = s.repo.GetNationalityByID(ctx, req.NationalityID)
	if err != nil {
		return nil, models.NewAppError(400, "Invalid nationality", err.Error())
	}

	// Parse date of birth
	dob, err := time.Parse("2006-01-02", req.DateOfBirth)
	if err != nil {
		return nil, models.NewAppError(400, "Invalid date format", err.Error())
	}

	// Update customer data
	existingCustomer.NationalityID = req.NationalityID
	existingCustomer.Name = req.Name
	existingCustomer.DateOfBirth = dob
	existingCustomer.PhoneNumber = req.PhoneNumber
	existingCustomer.Email = req.Email

	// Update customer
	_, err = s.repo.UpdateCustomer(ctx, existingCustomer)
	if err != nil {
		return nil, models.NewAppError(500, "Failed to update customer", err.Error())
	}

	// Delete existing family members
	err = s.repo.DeleteFamilyMembersByCustomerID(ctx, id)
	if err != nil {
		return nil, models.NewAppError(500, "Failed to delete existing family members", err.Error())
	}

	// Create new family members if provided
	if len(req.FamilyList) > 0 {
		var familyMembers []models.FamilyMember
		for _, familyReq := range req.FamilyList {
			familyMember := models.FamilyMember{
				CustomerID:  id,
				Relation:    familyReq.Relation,
				Name:        familyReq.Name,
				DateOfBirth: familyReq.DateOfBirth,
			}
			familyMembers = append(familyMembers, familyMember)
		}

		err = s.repo.CreateFamilyMembers(ctx, id, familyMembers)
		if err != nil {
			return nil, models.NewAppError(500, "Failed to create family members", err.Error())
		}
	}

	// Get complete updated customer data
	return s.GetCustomerByID(ctx, id)
}

func (s *service) DeleteCustomer(ctx context.Context, id int) error {
	// Delete family members first (due to foreign key constraint)
	err := s.repo.DeleteFamilyMembersByCustomerID(ctx, id)
	if err != nil {
		return models.NewAppError(500, "Failed to delete family members", err.Error())
	}

	// Delete customer
	err = s.repo.DeleteCustomer(ctx, id)
	if err != nil {
		return models.NewAppError(500, "Failed to delete customer", err.Error())
	}

	return nil
}

func (s *service) GetAllNationalities(ctx context.Context) ([]models.Nationality, error) {
	nationalities, err := s.repo.GetAllNationalities(ctx)
	if err != nil {
		return nil, models.NewAppError(500, "Failed to get nationalities", err.Error())
	}
	return nationalities, nil
}