package models

import "fmt"

type AppError struct {
	Code    int    `json:"code"`
	Message string `json:"message"`
	Details string `json:"details,omitempty"`
}

func (e *AppError) Error() string {
	return fmt.Sprintf("Error %d: %s", e.Code, e.Message)
}

func NewAppError(code int, message string, details ...string) *AppError {
	err := &AppError{
		Code:    code,
		Message: message,
	}
	if len(details) > 0 {
		err.Details = details[0]
	}
	return err
}

// Common errors
var (
	ErrNotFound     = NewAppError(404, "Resource not found")
	ErrBadRequest   = NewAppError(400, "Bad request")
	ErrInternalError = NewAppError(500, "Internal server error")
)