package main

import (
	"booking-api/internal/config"
	"booking-api/internal/customer"
	"booking-api/pkg/database"
	"log"
	"net/http"

	"github.com/gorilla/mux"
)

func main() {
	
	cfg := config.Load()

	db, err := database.NewPostgresConnection(cfg.Database)
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	customerRepo := customer.NewRepository(db)

	customerService := customer.NewService(customerRepo)

	customerHandler := customer.NewHandler(customerService)

	router := mux.NewRouter()
	
	api := router.PathPrefix("/api/v1").Subrouter()
	customer.RegisterRoutes(api, customerHandler)

	router.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
		w.WriteHeader(http.StatusOK)
		w.Write([]byte("OK"))
	}).Methods("GET")

	log.Printf("Server starting on %s:%s", cfg.Server.Host, cfg.Server.Port)
	log.Fatal(http.ListenAndServe(cfg.Server.Host+":"+cfg.Server.Port, router))
}