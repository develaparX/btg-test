package customer

import (
	"booking-api/internal/models"
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"
)

type Handler struct {
	service Service
}

func NewHandler(service Service) *Handler {
	return &Handler{service: service}
}

func (h *Handler) CreateCustomer(w http.ResponseWriter, r *http.Request) {
	var req models.CreateCustomerRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		h.writeErrorResponse(w, models.NewAppError(400, "Invalid request body", err.Error()))
		return
	}

	customer, err := h.service.CreateCustomer(r.Context(), &req)
	if err != nil {
		h.writeErrorResponse(w, err)
		return
	}

	h.writeSuccessResponse(w, http.StatusCreated, customer, "Customer created successfully")
}

func (h *Handler) GetCustomer(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	id, err := strconv.Atoi(vars["id"])
	if err != nil {
		h.writeErrorResponse(w, models.NewAppError(400, "Invalid customer ID"))
		return
	}

	customer, err := h.service.GetCustomerByID(r.Context(), id)
	if err != nil {
		h.writeErrorResponse(w, err)
		return
	}

	h.writeSuccessResponse(w, http.StatusOK, customer, "")
}

func (h *Handler) GetAllCustomers(w http.ResponseWriter, r *http.Request) {
	customers, err := h.service.GetAllCustomers(r.Context())
	if err != nil {
		h.writeErrorResponse(w, err)
		return
	}

	h.writeSuccessResponse(w, http.StatusOK, customers, "")
}

func (h *Handler) UpdateCustomer(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	id, err := strconv.Atoi(vars["id"])
	if err != nil {
		h.writeErrorResponse(w, models.NewAppError(400, "Invalid customer ID"))
		return
	}

	var req models.UpdateCustomerRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		h.writeErrorResponse(w, models.NewAppError(400, "Invalid request body", err.Error()))
		return
	}

	customer, err := h.service.UpdateCustomer(r.Context(), id, &req)
	if err != nil {
		h.writeErrorResponse(w, err)
		return
	}

	h.writeSuccessResponse(w, http.StatusOK, customer, "Customer updated successfully")
}

func (h *Handler) DeleteCustomer(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	id, err := strconv.Atoi(vars["id"])
	if err != nil {
		h.writeErrorResponse(w, models.NewAppError(400, "Invalid customer ID"))
		return
	}

	err = h.service.DeleteCustomer(r.Context(), id)
	if err != nil {
		h.writeErrorResponse(w, err)
		return
	}

	h.writeSuccessResponse(w, http.StatusOK, nil, "Customer deleted successfully")
}

func (h *Handler) GetNationalities(w http.ResponseWriter, r *http.Request) {
	nationalities, err := h.service.GetAllNationalities(r.Context())
	if err != nil {
		h.writeErrorResponse(w, err)
		return
	}

	h.writeSuccessResponse(w, http.StatusOK, nationalities, "")
}

func (h *Handler) writeSuccessResponse(w http.ResponseWriter, statusCode int, data interface{}, message string) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(statusCode)
	
	response := map[string]interface{}{
		"success": true,
		"data":    data,
	}
	
	if message != "" {
		response["message"] = message
	}
	
	json.NewEncoder(w).Encode(response)
}

func (h *Handler) writeErrorResponse(w http.ResponseWriter, err error) {
	w.Header().Set("Content-Type", "application/json")
	
	if appErr, ok := err.(*models.AppError); ok {
		w.WriteHeader(appErr.Code)
		json.NewEncoder(w).Encode(map[string]interface{}{
			"success": false,
			"error":   appErr.Message,
			"details": appErr.Details,
		})
	} else {
		w.WriteHeader(http.StatusInternalServerError)
		json.NewEncoder(w).Encode(map[string]interface{}{
			"success": false,
			"error":   "Internal server error",
		})
	}
}