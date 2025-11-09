package models

import "time"

type Customer struct {
	ID           int            `json:"id" db:"cst_id"`
	NationalityID int           `json:"nationality_id" db:"nationality_id"`
	Name         string         `json:"name" db:"cst_name"`
	DateOfBirth  time.Time      `json:"date_of_birth" db:"cst_dob"`
	PhoneNumber  string         `json:"phone_number" db:"cst_phonenum"`
	Email        string         `json:"email" db:"cst_email"`
	Nationality  *Nationality   `json:"nationality,omitempty"`
	FamilyList   []FamilyMember `json:"family_list,omitempty"`
}

type Nationality struct {
	ID   int    `json:"id" db:"nationality_id"`
	Name string `json:"name" db:"nationality_name"`
	Code string `json:"code" db:"nationality_code"`
}

type FamilyMember struct {
	ID         int    `json:"id" db:"fl_id"`
	CustomerID int    `json:"customer_id" db:"cst_id"`
	Relation   string `json:"relation" db:"fl_relation"`
	Name       string `json:"name" db:"fl_name"`
	DateOfBirth string `json:"date_of_birth" db:"fl_dob"`
}

// Request DTOs
type CreateCustomerRequest struct {
	NationalityID int                        `json:"nationality_id"`
	Name          string                     `json:"name"`
	DateOfBirth   string                     `json:"date_of_birth"`
	PhoneNumber   string                     `json:"phone_number"`
	Email         string                     `json:"email"`
	FamilyList    []CreateFamilyMemberRequest `json:"family_list"`
}

type UpdateCustomerRequest struct {
	NationalityID int                        `json:"nationality_id"`
	Name          string                     `json:"name"`
	DateOfBirth   string                     `json:"date_of_birth"`
	PhoneNumber   string                     `json:"phone_number"`
	Email         string                     `json:"email"`
	FamilyList    []CreateFamilyMemberRequest `json:"family_list"`
}

type CreateFamilyMemberRequest struct {
	Relation    string `json:"relation"`
	Name        string `json:"name"`
	DateOfBirth string `json:"date_of_birth"`
}