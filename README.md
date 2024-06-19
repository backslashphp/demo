# Backslash Demo Application

This is a simple console-based application designed to manage students, courses and enrollments.

Its goal is to demonstrate how [Backslash](https://github.com/backslashphp/backslash) components work together in order
to power event-sourced systems in PHP.

The application is interacted with through a few scripts located in the `bin` folder. It relies solely on Backslash for
simplicity in learning, without using any additional libraries.

Featured Backslash components are:

- State and domain events
- Commands and command dispatcher
- Projectors and projections
- Projection rebuilding
- Stream enrichment
- Testing scenarios
- Middlewares

## Domain rules

- A student cannot be enrolled in more than 3 courses.
- A course cannot take more students than its capacity.
- Enrollments cannot occur outside the enrollment period.

## Getting started

Start by installing dependencies with Composer:

```sh
composer install
```

Move to the `bin` folder and run the `demo.php` script:

```sh
cd bin
php demo.php
```

It initializes the SQLite database in `data/demo.sqlite` where events and projections are persisted. It also runs some
commands to create students and courses.

As you play with the app, you may open the SQLite database in your favourite IDE to inspect its content.

## Usage

> Commands in these examples must be run from the `bin` folder.

### Register a student

```sh
php register-student.php
```

**--id**: The student ID (integer)  
**--name**: The student name

### Create a course

```sh
php create-course.php
```

**--id**: The student ID (integer)  
**--name**: The course name  
**--capacity**: How many students can enroll in this course (integer)

### Change course capacity

```sh
php create-course.php
```

**--id**: The course ID (integer)  
**--capacity**: The new capacity (integer)

### Open enrollment period

Enrollment period must be open for student to enroll in courses.

```sh
php open-enrollment-period.php
```

### Close enrollment period

```sh
php close-enrollment-period.php
```

### Enroll a student in a course

```sh
php enroll.php
```

**--student**: The student ID (integer)  
**--course**: The course ID (integer)

### Withdraw a student from a course

```sh
php withdraw.php
```

**--student**: The student ID (integer)  
**--course**: The course ID (integer)

### Cancel a course

Students enrolled in this course will be withdrawn.

```sh
php cancel-course.php
```

**--id**: The course ID (integer)

## Management scripts

### Show the content of event store

```sh
php events.php
```

### Delete events and projections

```sh
php reset.php
```

### Restart demo

```sh
php demo.php
```

## Rebuild projections

This script deletes all projections and rebuilds them by replaying events.

```bash
php rebuild-projections.php
```

## Testing

Some tests can be found in the `tests` folder. They use on the `Scenario` component.

```bash
vendor/bin/phpunit
```
