# Voting System

### MILESTONE 1:
- Complete Single Page Application for SecureVote with 4 main pages
- Project structure with organized folders for frontend and backend
- Static frontend with Bootstrap styling and responsive design
- SPA navigation that works without page reloads
- Login/Register modals for user authentication
- Voting interface with candidate selection
- Results page with chart visualization
- ERD diagram with 5 database entities for the voting system

![ERD Diagram](./ERDdiagram.PNG)

### MILESTONE 2:
- MySQL database with 5 entities (Voters, Elections, Candidates, ElectionCandidates, Votes)
- Complete DAO layer with CRUD operations for all entities
- Database configuration using PDO with secure prepared statements
- Base DAO class with common database operations
- Entity-specific DAOs with custom voting business logic
- SQL schema file for database creation and setup
- Test file to verify all DAO functionality works correctly

### MILESTONE 3:
- Complete service layer with BaseService and entity-specific services
- Business logic with validation rules, password hashing, and vote integrity checks
- FlightPHP REST API with CRUD operations and JSON responses
- Organized route files for Voters, Elections, Candidates, and Votes
- OpenAPI documentation with Swagger UI for interactive testing

### MILESTONE 4:
- Implemented user authentication using FlightPHP middleware and JWT
- Added role-based access control (Admin vs User)
- Admins can perform full CRUD operations on elections and candidates
- Regular users can vote and view election results
- Frontend dynamically adapts based on authenticated user role
- Fully connected and secured frontend–backend communication

### MILESTONE 5:
- Implemented frontend MVC structure using Service-based architecture (only Services manage API calls and logic)
- Added client-side form validation (required fields, email checks) and backend input validation for improved security
- Enhanced authentication security with protected requests and validation before API calls
- Deployed full application to DigitalOcean (frontend, backend, and database connected)
- Configured live MySQL database and updated backend connection for production environment

### Live Application Links
- **Frontend:** https://votingsystemfrontend-i59en.ondigitalocean.app
- **Backend API:** https://votingsystem-xxmci.ondigitalocean.app/
- **API Documentation (Swagger):** https://votingsystem-xxmci.ondigitalocean.app/public/v1/docs/
 
### Admin Login (for testing)
| Email | Password |
|--------|-----------|
| admin@gmail.com | admin123 |