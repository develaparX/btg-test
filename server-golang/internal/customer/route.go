package customer

import (
	"github.com/gorilla/mux"
)

func RegisterRoutes(router *mux.Router, handler *Handler) {
	
	router.HandleFunc("/customers", handler.CreateCustomer).Methods("POST")
	router.HandleFunc("/customers", handler.GetAllCustomers).Methods("GET")
	router.HandleFunc("/customers/{id:[0-9]+}", handler.GetCustomer).Methods("GET")
	router.HandleFunc("/customers/{id:[0-9]+}", handler.UpdateCustomer).Methods("PUT")
	router.HandleFunc("/customers/{id:[0-9]+}", handler.DeleteCustomer).Methods("DELETE")

	router.HandleFunc("/nationalities", handler.GetNationalities).Methods("GET")
}