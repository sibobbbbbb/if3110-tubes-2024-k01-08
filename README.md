# Tugas Besar IF3110 2024/2025

## Description

LinkinPurry is a sophisticated job market platform designed to connect talented agents from O.W.C.A. (Organization Without a Cool Acronym) with meaningful employment opportunities. Built with pure PHP, vanilla JavaScript, and CSS, this platform serves as a bridge between job seekers and companies, offering a streamlined recruitment process.

### For Job Seekers

- Advanced Job Search: Intuitive search functionality with filters for job type and location
- Smart Filtering: Filter opportunities by work arrangement (remote, hybrid, on-site) and employment type
- Application Tracking: Comprehensive application history and status monitoring
- Dynamic Job Applications: Submit applications with CV (required) and optional video introductions
- Real-time Status Updates: Track application statuses and receive detailed feedback

### For Companies

- Job Posting Management: Create, edit, and manage job listings with rich text descriptions
- Applicant Management: Review applications, manage candidates, and provide structured feedback
- Company Profile: Maintain and update company information and branding
- Application Review System: Streamlined process for reviewing and responding to applications
- Recruitment Analytics: Track application statistics and manage hiring pipelines

### Platform Features

- Secure Authentication: Robust login and registration system
- Responsive Design: Optimized viewing experience across devices
- Rich Text Integration: Enhanced content creation with Quill.js editor
- File Management: Secure handling of CVs, videos, and company attachments
- Pagination: Efficient browsing of job listings and applications

## Tech Stacks

- Frontend: Pure JavaScript, HTML5, CSS3
- Backend: PHP (vanilla) with the clean architecture pattern
- Database: MySQL/PostgreSQL for reliable data management
- Security: Password hashing, input sanitization, and XSS protection
- Rich Text: Quill.js integration for enhanced content editing
- Docker: Containerized deployment for consistent environments

## Requirements

- Git
- Makefile
- Docker version 27.x

## How To Install & Run

1. Clone the repository

   ```bash
   https://github.com/Labpro-21/if3110-tubes-2024-k01-08.git
   ```

2. Create a `.env` file and copy `.env.example` to it.

3. Create docker network

   ```bash
   make network
   ```

   or

   ```bash
   docker network create linkinpurry-network
   ```

4. Run the database (with seeding). To turn off seeding, comment the SQL script from line 159 untill the end.

   ```bash
   make db
   ```

   or

   ```bash
   docker compose up --build linkinpurry-db
   ```

5. Run the website

   - For development,

     ```bash
     make web-dev
     ```

     or

     ```bash
     docker compose up --build linkinpurry-web-dev
     ```

   - For production

     ```
     make web-prod
     ```

     or

     ```bash
     docker compose up --build linkinpurry-web-prod
     ```

6. Stop the container

   ```bash
   make stop
   ```

   or

   ```bash
   docker compose down
   ```

7. Hard reset (delete docker volume for database & image storage)

   ```bash
   make reset
   ```

   or

   ```bash
   docker compose down
   docker volume rm linkinpurry-db-data
   docker volume rm linkinpurry-upload-data
   ```

## Screenshots

## Lighthouse

Most of the issue from the light house benchmark is in the Accessibility section. It was easily fixed by adding `aria-label` attribute to a `button` or `anchor` that doesn't have any text in it (e.g. icon). The other issue was that the form fields doesn't have a label to it (even though it is already there). The fix was by simply adding `id` attribute to the input field that matches the `for` attribute in the label field. All pages successfully achieve > 90 lighthouse score on mobile.

## Job Distribution

### Server-side

1. Sign In Page: 13522011

2. Register Page: 13522009

3. Landing Page: No Serverside

4. Home Page (Job Seeker): 13522015

5. Home Page (Company): 13522011

6. Add Job Vacancy Page (Company): 13522011

7. Job Vacancy Detail Page (Company): 13522011

8. Application Detail Page (Company): 13522011

9. Edit Job Vacancy Page (Company): 13522011

10. Job Vacancy Detail Page (Job Seeker): 13522011

11. Application Page (Job Seeker): 13522015

12. History Page (Job Seeker): 13522011

13. Profile Page (Company): 13522011

14. Error Page: 13522009

### Client-side

1. Sign In Page: 13522011

2. Register Page: 13522009

3. Landing Page: 13522011, 13522015

4. Home Page (Job Seeker): 13522011, 13522015

5. Home Page (Company): 13522011

6. Add Job Vacancy Page (Company): 13522011

7. Job Vacancy Detail Page (Company): 13522011

8. Application Detail Page (Company): 13522011

9. Edit Job Vacancy Page (Company): 13522011

10. Job Vacancy Detail Page (Job Seeker): 13522011

11. Application Page (Job Seeker): 13522015

12. History Page (Job Seeker): 13522009, 13522011

13. Profile Page (Company): 13522011

14. Error Page: 13522009
