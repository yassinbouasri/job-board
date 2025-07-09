# 💼 Job Board Application

![Symfony](https://img.shields.io/badge/Symfony-7.2-black?logo=symfony&style=flat)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-blue?logo=postgresql)
![Tailwind CSS](https://img.shields.io/badge/Frontend-Tailwind_CSS-38B2AC?logo=tailwindcss&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-enabled-2496ED?logo=docker&logoColor=white)
![License](https://img.shields.io/github/license/yassinbouasri/job-board?style=flat)
![Issues](https://img.shields.io/github/issues/yassinbouasri/job-board)

  # Job Board Application

A modern web application connecting job seekers with employers. Built with Symfony PHP framework.


## Features

### Job Seeker
- **Browse Jobs**: Filter by experience, salary range, and job type  
- **Apply Online**: Submit applications directly through the platform 
- **Profile Management**: Create and update professional profiles with resume/CV upload, or genereate a CV
- **Bookmark/unbookmark**: Any job posting, View a dedicated list of saved jobs, Remove jobs from bookmarks
- **Job Notification**: Sends users email alerts (daily or weekly) with matching new job posts from the Job Board platform based on saved preferences like keywords, location, and tags.
- **In-app notifications**: Displays job alerts based on user preferences
<img width="300" alt="image" src="https://github.com/user-attachments/assets/90e7cbce-0125-4afc-9d9b-a411f2b360c5" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/82c06cb6-4a75-4d8a-bc7a-0c827039e269" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/fda7dd21-144c-42c1-8f5e-723bc86919c9" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/0c800316-5ce8-42e3-81bc-01a72f62e77f" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/3bae269a-56a2-4cce-9e4b-5e090df84f4d" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/76cf560c-d59a-4257-9272-abe9ba28b634" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/b1b78c44-3366-4519-b2a5-ba2b5665c919" />







### Employer
- **Post Jobs**: Easy job listing creation  
- **Manage Applications**: Track candidate applications in dashboard
- **Free Email Domain Restriction**: The system prevents registration as a company using common free email providers (Gmail, Yahoo, etc.)
<img width="200" height="200" alt="image" src="https://github.com/user-attachments/assets/32fd397f-c04e-42fc-989a-3a2df7f7c506" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/43b4b947-196d-4cfd-91aa-e74ea4470afd" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/0ed530cf-f2d8-4f16-a181-c3d5bcecddae" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/6c7ded6c-503f-4a2d-88cd-045e67ed7a6b" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/f1bd6818-62d0-44e0-936f-50d5ded30e97" />
<img width="200" height="200" alt="image" src="https://github.com/user-attachments/assets/eac3de5f-79f8-4788-ad57-250578f02229" />





### Admin
- **User Management**: Manage users and roles  
- **Content Moderation**: Review and approve job postings  
- **Analytics**: View platform usage statistics
<img width="300" alt="image" src="https://github.com/user-attachments/assets/fb70296c-c73f-459e-a2a8-b7b24edb313b" />
<img width="300" alt="image" src="https://github.com/user-attachments/assets/5c851d9b-0745-4d55-b09b-011b528a9524" />


## Technology Stack

**Backend**
- Symfony 7.2
- Doctrine ORM

**Frontend**
- Twig templates
- Tailwind CSS

**Database**
- PostgreSQL

**DevOps**
- Docker
- GitHub

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- PostgreSQL

### Setup Steps

1. **Clone Repository**
```bash
git clone https://github.com/yourusername/job-board.git
cd job-board
```
1. **Install Dependencies**
```bash
npm install
npm run dev
```

2. **Configure Environment**
```bash
  cp .env .env.local
```

3. **Database Configuration**
```bash
  DATABASE_URL="postgresql://app:your_password@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
  # Mailer Configuration (Mailtrap Example)
  MAILER_DSN="smtp://*****:*****@sandbox.smtp.mailtrap.io:2525"
```

4. **Database Setup**
```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

5. **Docker Setup**
```bash
docker-compose up -d --build
docker-compose exec php composer install

docker compose up -d
```

 6. **Run the Development Server**
```bash
symfony serve
```
Visit `http://localhost:8000/login` in your browser.

7. **Future Features**
- Job Alerts: Email notifications for new job listings based on preferences
- Dark Mode UI
- API
