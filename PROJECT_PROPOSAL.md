# Learning Management System (LMS) Platform

## Project Proposal

**Submitted By:**
**Kazi Sazzad Hossain Rohan**
8th Semester, Department of Computer Science
Mangrove Institute of Science and Technology

---

### 1. Project Overview

**Project Title:** Spattie LMS - A Comprehensive Online Learning Platform

**Problem Statement:**
In today's digital age, traditional education systems face challenges in reaching remote learners, providing flexible learning schedules, and offering personalized learning experiences. Educational institutions and independent instructors lack a unified platform to deliver quality content efficiently while tracking student progress and engagement.

**Proposed Solution:**
Spattie LMS is a web-based Learning Management System designed to bridge the gap between educators and learners. The platform provides a robust infrastructure for course creation, content delivery, student enrollment, progress tracking, and comprehensive administration through an intuitive interface.

**Project Duration:** 1 Month (Development Phase)

---

### 2. Objectives

#### Primary Objectives:

1. Develop a scalable web platform supporting multiple user roles (administrators, instructors, and students)
2. Enable instructors to create, manage, and deliver courses with structured content (modules and lessons)
3. Provide students with seamless enrollment, learning, and progress tracking capabilities
4. Implement secure authentication with two-factor authentication (2FA) for enhanced security
5. Develop an intuitive learning interface with video integration and progress monitoring

#### Secondary Objectives:

1. Ensure responsive design for cross-device compatibility
2. Implement role-based access control for data security
3. Optimize performance for smooth user experience
4. Create an extensible architecture for future enhancements
5. Follow industry-standard coding practices and maintainable code structure

---

### 3. Technical Architecture

#### 3.1 Technology Stack

**Backend Technologies:**

- **Framework:** Laravel 12.0 (PHP 8.2+)
- **Authentication:** Laravel Fortify
- **Authorization:** Spatie Laravel Permission (RBAC)
- **API Layer:** Inertia.js 2.0
- **Database:** SQLite (development), MySQL/PostgreSQL (production)
- **Testing Framework:** Pest 4.1
- **Queue Management:** Database Queue System

**Frontend Technologies:**

- **Framework:** Vue.js 3.5 with TypeScript 5.2
- **Styling:** TailwindCSS 4.1
- **UI Component Library:** Reka UI 2.7
- **Icons:** Lucide Vue Next
- **Build Tool:** Vite 7.0
- **State Management:** Composition API with @vueuse/core

**Development Tools:**

- Version Control: Git
- Code Quality: ESLint, Prettier, Laravel Pint
- Containerization: Docker (Laravel Sail)
- API Testing: Postman

#### 3.2 System Architecture

The application follows a **Model-View-Controller (MVC)** architectural pattern:

- **Presentation Layer:** Vue.js components with TypeScript for type safety
- **Application Layer:** Laravel controllers handling business logic
- **Data Layer:** Eloquent ORM for database operations
- **Service Layer:** Action classes for complex operations (authentication, user creation)

**Communication Flow:**

- Inertia.js bridges Laravel backend with Vue.js frontend
- Server-driven routing without REST API overhead
- Automatic data propagation through Inertia props
- Shared state management for authentication and user sessions

---

### 4. Features and Functionalities

#### 4.1 Authentication & Security Module

- User registration with email verification
- Secure login with password hashing (bcrypt)
- Two-factor authentication (TOTP) with QR code setup
- Password reset functionality via email
- Session management with configurable lifetime
- Role-based access control (Admin, Instructor, Student)

#### 4.2 User Management Module

- **Administrator:**
    - Create, update, delete user accounts
    - Assign roles and permissions
    - View system-wide user statistics
    - Manage platform settings

- **Instructor:**
    - Create and manage courses
    - Organize content into modules
    - Add lessons with video support
    - Track student enrollments and progress
    - Access instructor dashboard and analytics

- **Student:**
    - Browse course catalog
    - Enroll in available courses
    - Access enrolled courses
    - Track learning progress
    - Manage personal schedule

#### 4.3 Course Management Module

- **Course Creation:**
    - Title, description, and overview
    - Course level (Beginner, Intermediate, Advanced)
    - Pricing information
    - Thumbnail image upload
    - Curriculum and objectives
    - Automatic slug generation for SEO

- **Course Publishing:**
    - Draft status for courses under development
    - Scheduled publishing with future availability
    - Published status for live courses
    - Archived status for inactive courses

#### 4.4 Content Management Module

- **Module System:**
    - Hierarchical organization within courses
    - Ordering and sequencing capability
    - Module descriptions

- **Lesson System:**
    - Rich text content support
    - YouTube video integration with automatic ID extraction
    - Duration tracking
    - Preview lessons for marketing
    - Scheduled publishing
    - Lesson completion tracking

#### 4.5 Student Learning Module

- **Course Catalog:**
    - Browse all published courses
    - View course details and curriculum
    - Filter by level and price
    - Featured courses on homepage

- **Learning Interface:**
    - Video player integration
    - Lesson navigation
    - Progress indicators
    - Mark lessons as complete
    - Resume learning from last position

- **Progress Tracking:**
    - Per-lesson completion status
    - Course-level progress percentage
    - Watch time tracking
    - Completion timestamps

#### 4.6 Dashboard & Analytics

- **Instructor Dashboard:**
    - Course overview with enrollment counts
    - Quick access to course management
    - Student progress statistics

- **Student Dashboard:**
    - Enrolled courses overview
    - Continue learning shortcuts
    - Progress summaries

- **Settings Pages:**
    - Profile management
    - Password change
    - Two-factor authentication setup
    - Appearance preferences (light/dark mode)

---

### 5. Database Design

The system utilizes a relational database schema with the following core entities:

**Primary Tables:**

- `users` - User accounts with authentication and 2FA data
- `courses` - Course catalog with metadata and status
- `modules` - Course organization units with ordering
- `lessons` - Individual lessons with content and video URLs
- `enrollments` - Student-course enrollment relationships
- `lesson_progress` - Student progress tracking per lesson
- `roles` & `permissions` - Role-based access control

**Key Relationships:**

- Users → Courses (instructor relationship)
- Users → Courses (enrollment relationship)
- Courses → Modules (one-to-many)
- Modules → Lessons (one-to-many)
- Enrollments → Lesson Progress (one-to-many)

**Data Integrity:**

- Foreign key constraints
- Cascade deletion for related records
- Soft deletes for courses (data recovery)
- Unique constraints on slugs and emails

---

### 6. Methodology

#### Development Approach: Agile with Scrum

**Phase 1: Planning & Design (Weeks 1-2)**

- Requirement analysis and specification finalization
- Database schema design
- UI/UX wireframing
- Technology stack confirmation

**Phase 2: Backend Development (Weeks 3-10)**

- Laravel project setup and configuration
- Database migration creation
- Authentication system implementation
- Role-based permission system
- Course, module, and lesson CRUD operations
- Enrollment and progress tracking logic
- API controller development

**Phase 3: Frontend Development (Weeks 11-18)**

- Vue.js application setup with Vite
- Component library integration (TailwindCSS, Reka UI)
- Authentication pages (login, register, password reset)
- Dashboard layouts for all user roles
- Course management interfaces
- Learning interface development
- Settings pages implementation

**Phase 4: Integration & Testing (Weeks 19-22)**

- Backend-frontend integration via Inertia.js
- Unit testing with Pest
- Feature testing
- Security testing (authentication, authorization)
- Performance optimization
- Bug fixing and refinement

**Phase 5: Deployment & Documentation (Weeks 23-24)**

- Production server setup
- Database migration to production
- Environment configuration
- User documentation creation
- Final testing and launch

---

### 7. Scope & Limitations

#### In Scope:

- Web-based platform accessible through browsers
- Three user roles (Admin, Instructor, Student)
- Course management with modules and lessons
- YouTube video integration
- Progress tracking and analytics
- Two-factor authentication
- Basic course catalog with filtering

#### Out of Scope (Future Enhancements):

- Payment gateway integration (Stripe, PayPal)
- Mobile application (iOS/Android)
- Advanced analytics and reporting
- Discussion forums and messaging
- Assessment and certification system
- Multi-language support
- Live session integration
- File attachments and downloads

#### Limitations:

- YouTube video hosting only (no self-hosted video support)
- Single language interface (English)
- No real-time chat or notification system
- Basic analytics without advanced visualization
- Limited course pricing models

---

### 8. Risk Assessment & Mitigation

| Risk                                | Impact | Probability | Mitigation Strategy                                                   |
| ----------------------------------- | ------ | ----------- | --------------------------------------------------------------------- |
| Technical complexity of Inertia.js  | Medium | Low         | Comprehensive research and prototyping before implementation          |
| Video platform dependency (YouTube) | High   | Medium      | Design architecture to support multiple video platforms in future     |
| Database scalability                | Medium | Low         | Implement proper indexing, query optimization, and caching strategies |
| Security vulnerabilities            | High   | Low         | Regular security audits, implement 2FA, follow OWASP guidelines       |
| Time constraints                    | Medium | Medium      | Prioritize core features, defer non-essential features to Phase 2     |
| User adoption resistance            | Low    | Medium      | Conduct user testing, create comprehensive documentation              |

---

### 9. Project Deliverables

1. **Functional Web Application**
    - Fully deployed LMS platform
    - Tested and bug-free codebase
    - Optimized for performance

2. **Source Code**
    - Version-controlled repository (Git)
    - Well-documented code
    - Installation and setup instructions

3. **Documentation**
    - Technical documentation (architecture, API references)
    - User manual for instructors and students
    - Administrator guide
    - Project report with development journey

4. **Presentation Materials**
    - Project demonstration
    - Slide deck presentation
    - Live demo environment

---

### 10. Expected Outcomes

Upon completion, this project will deliver:

1. **Functional Learning Platform:** A fully operational LMS capable of supporting real educational scenarios

2. **Demonstrated Technical Skills:** Practical application of full-stack development, database design, and modern web technologies

3. **Scalable Solution:** Architecture designed for growth and feature expansion

4. **Academic Contribution:** A reference implementation for modern web application development using Laravel and Vue.js

5. **Industry Relevance:** Platform aligned with current e-learning market trends and requirements

---

### 11. Conclusion

The Spattie LMS project addresses a critical need in the education sector by providing a scalable, user-friendly platform for online learning. The combination of Laravel's robust backend capabilities with Vue.js's reactive frontend creates an optimal solution for course delivery and management.

This project demonstrates the practical application of computer science concepts including software engineering principles, database management, web technologies, and security practices. The modular architecture ensures maintainability while providing opportunities for future enhancements.

The platform has potential applications in:

- Educational institutions (universities, colleges)
- Corporate training programs
- Professional development platforms
- Online course marketplaces
- Private tutoring services

With proper execution and continuous improvement, Spattie LMS can evolve into a comprehensive solution serving diverse educational needs in the digital learning landscape.

---

**Department:** Computer Science and Engineering
**Institution:** Mangrove Institute of Science and Technology

**Project Prepared By:**
_Kazi Sazzad Hossain Rohan_
**Board Role**
_638048_
