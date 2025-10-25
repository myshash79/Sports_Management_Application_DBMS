# Sports Database Management System
---

## 1. Project Description
The **Sports Database Management System (Sports DBMS)** is designed to efficiently manage and organize sports-related data such as players, teams, tournaments, and match information.  
It provides a centralized web platform for coaches, team managers, league organizers, and administrators to store, retrieve, and analyze sports data.  
The system replaces manual record-keeping with an automated, structured, and user-friendly solution built on PHP and MySQL.

---

## 2. Scope
The Sports DBMS supports:

- Managing player profiles and statistics.  
- Maintaining team information, coaches, and historical performance.  
- Scheduling matches and storing match results.  
- Managing tournaments and associated venues.  
- Providing role-based access to authorized users.  

The system ensures data integrity, quick retrieval, and simplified sports data management for educational or professional organizations.

---

## 3. Objectives
- Provide a single digital platform to manage sports-related data.  
- Enable real-time updates for matches, teams, and tournaments.  
- Support secure, multi-user access with authentication and access control.  
- Simplify data handling for administrators and coaches.  
- Allow future integration with analytics or web portals.

---

## 4. Users and Stakeholders

| User Role | Description |
|------------|-------------|
| **Administrator** | Manages the database, users, and all modules. |
| **Coach / Manager** | Handles team, player, and match data entry and updates. |
| **Player** | Views personal statistics and team details. |
| **Spectator / Analyst** | Can view match results and tournament standings. |

---

## 5. Functional Requirements

| ID | Functionality | Description |
|----|----------------|-------------|
| FR1 | Player Management | Add, update, or remove player profiles and statistics. |
| FR2 | Team Management | Create and edit team details, assign players/coaches. |
| FR3 | Match Management | Schedule matches, enter results, and update standings. |
| FR4 | Tournament Management | Manage tournament data and participating teams. |
| FR5 | Venue Management | Add, update, or delete venue information. |
| FR6 | Authentication | Secure login for admin and authorized users. |

---

## 6. Non-Functional Requirements

| Type | Requirement |
|-------|--------------|
| **Performance** | The system should handle multiple concurrent users without lag. |
| **Security** | Authentication, authorization, and SQL-injection prevention are ensured. |
| **Usability** | The interface is designed to be intuitive and easy to navigate. |
| **Reliability** | Data integrity and consistency are maintained at all times. |
| **Scalability** | The system can be extended for additional sports or functionalities. |

---

## 7. Technology Stack

| Layer | Technology Used |
|--------|----------------|
| **Frontend** | HTML, CSS, JavaScript |
| **Backend** | PHP |
| **Database** | MySQL |
| **Server Environment** | Apache (via XAMPP or WAMP) |
| **IDE / Editor** | Visual Studio Code |
| **Version Control** | Git and GitHub |
| **Design Tools** | Draw.io, Canva |
| **Testing** | Manual testing (Unit, Integration, and System test cases) |

---

## 8. System Design and Implementation

### 8.1 Architecture
The system follows a **three-tier architecture**:
1. **Presentation Layer** – Web interface for all user interactions.  
2. **Application Layer** – PHP logic handling input validation, CRUD operations, and communication with the database.  
3. **Database Layer** – MySQL storing normalized tables for players, teams, matches, tournaments, and venues.

### 8.2 Major Implementation Modules
- **Admin Module** – Login, authentication, and data control.  
- **Team Management Module** – Create and manage team details.  
- **Player Management Module** – Maintain player data and statistics.  
- **Match Management Module** – Schedule matches and record results.  
- **Tournament and Venue Module** – Manage events and venues.

---

## 9. Software Development Life Cycle

- **Model Used:** Incremental Model  
  - Phase 1: Core features (Teams, Players, Matches).  
  - Phase 2: Tournaments, Venues, and additional validations.  
  - Phase 3: Testing and integration.  
- **Justification:** The model supports progressive development, flexible refinement after each iteration, and suited the small team and academic timeframe.

---

## 10. Effort Estimation (COCOMO)

- **Estimated Size:** 2.5 KLOC  
- **Formula:** Effort = 2.4 × (2.5)^1.05 ≈ 6.28 person-months  
- **Team Size:** 2 members  
- **Estimated Duration:** ≈ 3.1 months  

This estimation aligns with the actual semester schedule and deliverables.

---

## 11. Features Implemented
- Admin login and authentication  
- Player and team management (CRUD)  
- Match scheduling and results recording  
- Tournament and venue management  
- Input validation for required fields  
- MySQL database connectivity and data persistence  
- Complete manual testing coverage for all modules  

---

## 12. Testing Summary

| Testing Type | Purpose | Approach | Result |
|---------------|----------|-----------|---------|
| **Unit Testing** | Verify individual modules (Admin, Team, Match) | Manual test cases | Passed |
| **Integration Testing** | Validate database connectivity and data flow | Manual SQL verification | Passed |
| **System Testing** | Ensure complete workflow functionality | Scenario-based testing | Passed |
| **Performance Testing** | Observe system response under concurrent use | Manual observation | Acceptable |

All test cases from login, team creation, match scheduling, and tournament updates executed successfully.

---

## 13. Results and Observations
- Functional coverage achieved across all major modules.  
- CRUD operations validated with expected database outcomes.  
- User interface and data validation performed accurately.  
- System operated reliably under normal workload conditions.

---
