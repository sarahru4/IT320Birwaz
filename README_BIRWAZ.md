<p align="center">
  <img src="birwaz-header.png" alt="BIRWAZ Project Header" width="100%">
</p>

# BIRWAZ

**BIRWAZ** is a web-based studio customization and booking platform that gives customers more control over how their studio setup will look **before they reserve it**.

Instead of choosing only from fixed studio setups, users can browse ready-made designs or create a personalized design by changing elements such as the **background, lighting, and decorations**. The platform provides a visual preview of the customized setup, allowing the customer to make design decisions online and book the studio with a clearer expectation of the final result.

> **Design it. Preview it. Book it.**

---

## The Problem

Many studios offer a limited set of predefined designs. Customers may not be able to personalize the background, lighting, or decorations according to their event and personal style.

This creates two main problems:

- **Limited customization** — customers may have to accept a design that does not fully match their vision.
- **Lack of preview before booking** — the customer may only discover a mismatch between expectations and the actual studio setup on the event day.

BIRWAZ was created to make the studio-booking experience more **personalized, transparent, and convenient**.

---

## The Solution

BIRWAZ provides a complete digital experience where users can:

- Create an account and securely log in.
- Browse available ready-made studio designs.
- Customize a studio using different backgrounds, lighting options, and decorations.
- Preview the customized design before confirming a reservation.
- Save customized designs.
- Book available dates and time slots.
- View existing reservations.
- Edit reservation details.
- Cancel reservations when needed.
- View saved and ready-made designs.

The platform also includes administrator functionality for managing designs and reservations.

---

## Product Vision

BIRWAZ is designed for individuals planning private events and celebrations who want to **design, customize, and preview their studio décor before booking**.

The goal is to provide a digital experience that connects **creative customization** with **practical reservation management**, helping ensure that the studio prepared for the event reflects what the customer selected online.

---

## Key Features

### Studio Customization

Users can personalize studio designs by selecting different visual elements such as:

- Backgrounds
- Lighting
- Decorations

Customization information and the resulting design can be saved for later use.

### Visual Design Preview

The customization experience allows users to see the appearance of their selected design before booking, reducing uncertainty between the customer's idea and the final studio arrangement.

### Ready-Made Designs

Users who do not want to build a design from scratch can browse existing studio designs and select an option that fits their event.

### Booking Management

BIRWAZ supports the reservation journey from selecting a design to choosing an available date and time.

Users can:

- Create reservations.
- View reservations.
- Edit booking information.
- Cancel reservations.
- Check available time slots.

### User Accounts

The platform supports account creation, login, logout, and session-based user interaction.

### Administrator Management

Administrator pages support operational management of the platform, including:

- Viewing reservations.
- Viewing available designs.
- Adding designs.
- Uploading designs.
- Editing designs.
- Deleting designs.

---

## User Journey

```text
Create Account / Login
          |
          v
Browse Ready-Made Designs
          |
          +----------------------+
          |                      |
          v                      v
Choose Existing Design     Customize a Design
                                  |
                                  v
                       Background / Lighting /
                            Decorations
                                  |
                                  v
                              Preview
          |                      |
          +----------+-----------+
                     |
                     v
               Select Booking
                Date & Time
                     |
                     v
             Confirm Reservation
                     |
                     v
        View / Edit / Cancel Booking
```

---

## System Architecture

BIRWAZ follows a **Client–Server Architecture**.

```text
User
  |
  v
Web Interface
HTML + CSS + JavaScript
  |
  | HTTP Requests
  v
Server-Side Logic
PHP
  |
  v
MySQL Database
  |
  +--> Users
  +--> Designs
  +--> Customized Designs
  +--> Decorations
  +--> Reservations
  +--> Notifications
```

This architecture separates the user interface from the server-side logic and centralized data management.

---

## Database

The MySQL database supports the main data required by the platform.

Core tables include:

| Table | Purpose |
|---|---|
| `user` | Stores user accounts and roles |
| `design` | Stores available studio designs |
| `customizedesign` | Stores user customization choices and customized design data |
| `decoration` | Stores decoration options and prices |
| `reservation` | Stores booking information, dates, time slots, prices, and status |
| `notification` | Stores reservation-related notification information |

---

## Technologies

### Interface
- HTML
- CSS
- JavaScript

### Server
- PHP
- PHP Sessions

### Database
- MySQL
- MySQLi

### Development & Testing
- MAMP / Local Web Server Environment
- Integration Testing
- User Acceptance Testing

---

## Project Structure

```text
IT320Birwaz/
│
├── birwasproject/
│   ├── index.html
│   ├── login.php
│   ├── signup.php
│   ├── homeUser.php
│   ├── Designs.php
│   ├── Customize.php
│   ├── saveDesign.php
│   ├── viewDesign.php
│   ├── Booking.php
│   ├── BookingEdit.php
│   ├── viewReservations.php
│   ├── cancelReservation.php
│   ├── viewReservationsAdmin.php
│   ├── viewDesignsAdmin.php
│   ├── addDesignsAdmin.php
│   ├── uploadDesign.php
│   ├── EditDesign.php
│   ├── update_design.php
│   ├── deleteDesign.php
│   ├── getBookedTimes.php
│   ├── db_config.php
│   └── logout.php
│
├── Database_birwaz/
│   ├── birwaz.sql
│   └── databaseinfo.pdf
│
├── Documentation/
│   └── Birwaz_Documentation.pdf
│
└── Team & Roles/
    └── Team & Roles.pdf
```

---

## Project Objectives

BIRWAZ aims to:

- Provide a user-friendly platform for creating and reserving customized studio setups.
- Give customers greater control over the appearance of their event studio.
- Allow customers to preview a design before making a booking.
- Make booking and design decisions accessible online.
- Store users, reservations, designs, and customization details in a structured database.
- Reduce misunderstandings between the customer's expected setup and the final studio arrangement.
- Support a smoother and more organized studio reservation experience.

---

## Target Users

The platform is intended primarily for adults planning celebrations and private events, particularly users looking for customizable studio experiences in Riyadh.

Examples include:

- Graduation celebrations
- Birthdays
- Weddings and private occasions
- Photography sessions
- Other personalized events

---

## Team Members

- **Nora Alkhudair**
- **Malak Basloom**
- **Sarah Alruwayte**
- **Atheer Budie**

---

## Academic Context

**King Saud University**  
College of Computer and Information Sciences  
Department of Information Technology  
**IT 320 — Course Project**

---

## Documentation

The repository includes the full project documentation covering:

- Problem and proposed solution
- Product vision and objectives
- Domain analysis
- Requirements engineering
- System users and use cases
- Product backlog
- System architecture
- Class diagram
- Data design
- Component design
- Interface design
- Implementation
- Testing and project evaluation

---

## Future Direction

BIRWAZ can be expanded with additional customization options, richer visualization, broader studio support, enhanced notifications, and additional services that make the journey from design inspiration to confirmed reservation even more seamless.

---

<p align="center">
  <b>BIRWAZ — turning a studio booking into a design experience.</b>
</p>
