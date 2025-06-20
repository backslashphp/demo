# Backslash Demo Application

This is a simple console-based application designed to manage students, courses and enrollments.

Its goal is to demonstrate how [Backslash](https://github.com/backslashphp/backslash) components work together in order
to power event-sourced systems in PHP.

The application is interacted with through a few scripts located in the `bin` folder. It relies solely on Backslash for
simplicity in learning, without using any additional libraries.

Featured Backslash components are:

- Domain events and states
- Commands and command handlers
- Projections
- Test scenarios

## Domain rules

Rules are inspired by the [Course Subscriptions example](https://dcb.events/examples/course-subscriptions/) of
the Dynamic Consistency Boundary website.

- A course cannot accept more students than its capacity.
- The course capacity can change at any time to any positive integer different from the current one.
- A student cannot subscribe to more than 3 courses.

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

It initializes the SQLite database in `data/demo.sqlite` where events and projections are persisted. It also runs
commands to create some demo data.

The script outputs projections of students, courses and current subscriptions.

```
----- STUDENTS -----
[1] John (Biology)
[2] Mary (Arts, Physics)
[3] Bill (Physics)
[4] James
[5] Lucy
[6] Brad
[7] Kelly
[8] Alice

----- COURSES -----
[1] Algebra (0/5)
[2] Biology (1/4)
    - John
[3] Arts (1/3)
    - Mary
[4] Physics (2/3)
    - Mary
    - Bill
[5] Grammar (0/4)
```

As you play with the scripts, you may open the SQLite database in your favourite IDE to inspect its content.

To understand how all parts of the application are connected, take a look
at [src/Infrastructure/Container.php](https://github.com/backslashphp/demo/blob/2.x/src/Infrastructure/Container.php).

## Usage

> Commands in these examples must be run from the `bin` folder.

### Register a student

```sh
php register-student.php --id=123 --name=Max
```

### Create a course

```sh
php define-course.php --id=1000 --name=Geology --capacity=10
```

### Change course capacity

```sh
php change-course-capacity.php --id=1000 --capacity=15
```

### Subscribe a student to a course

```sh
php subscribe.php --student=123 --course=1000
```

### Unsubscribe a student from a course

```sh
php unsubscribe.php --student=123 --course=1000
```

### Output the current state of the system

```sh
php show.php
```

## Management scripts

### List events persisted in the event store

```sh
php events.php
```

```
| #  | UID                                  | CLASS                                            | PAYLOAD                                        | IDENTIFIERS                      | METADATA                                                  | TIMESTAMP                        |
| 1  | 9ffd43a3-dee5-4caa-9d91-6e29c8ae3fd9 | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"1","name":"John"}                | {"studentId":"1"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.638777+00:00 |
| 2  | 33a197d5-a8cc-4ef6-b9be-f63608af5fd0 | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"2","name":"Mary"}                | {"studentId":"2"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.654526+00:00 |
| 3  | 8784f39f-f1bd-472f-a543-426ba4f3ac2e | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"3","name":"Bill"}                | {"studentId":"3"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.667294+00:00 |
| 4  | 231aa070-03bb-43aa-9482-e00052ae1780 | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"4","name":"James"}               | {"studentId":"4"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.687732+00:00 |
| 5  | 9a1fe35d-afe7-4fe9-ae61-213da79326e2 | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"5","name":"Lucy"}                | {"studentId":"5"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.702003+00:00 |
| 6  | c99af1e6-4d9d-4042-955c-567ddae78219 | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"6","name":"Brad"}                | {"studentId":"6"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.718004+00:00 |
| 7  | 3404d47e-25f0-48db-bab2-199feb13880e | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"7","name":"Kelly"}               | {"studentId":"7"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.734666+00:00 |
| 8  | 4770359c-0a0f-4872-971e-134ea2fdc4cb | Demo\Domain\Event\StudentRegisteredEvent         | {"studentId":"8","name":"Alice"}               | {"studentId":"8"}                | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.750996+00:00 |
| 9  | aa16a7c9-8902-4e91-92da-ef9803e00c63 | Demo\Domain\Event\CourseDefinedEvent             | {"courseId":"1","name":"Algebra","capacity":5} | {"courseId":"1"}                 | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.767753+00:00 |
| 10 | 776f7f2d-591b-4c2f-86af-e595b8e86a9e | Demo\Domain\Event\CourseDefinedEvent             | {"courseId":"2","name":"Biology","capacity":4} | {"courseId":"2"}                 | {"correlation_id":"b74e7829-2810-4a23-8a44-6e9d9a4f59c3"} | 2025-06-20T02:35:31.783081+00:00 |

...
```

### Rebuild projections

This script deletes all stored projections and rebuilds them by replaying events.

```bash
php rebuild-projections.php
```

```
No:                 1
Event:              Demo\Domain\Event\StudentRegisteredEvent
Timestamp:          2025-06-20T02:38:32.082613+00:00

No:                 2
Event:              Demo\Domain\Event\StudentRegisteredEvent
Timestamp:          2025-06-20T02:38:32.098321+00:00

No:                 3
Event:              Demo\Domain\Event\StudentRegisteredEvent
Timestamp:          2025-06-20T02:38:32.110229+00:00

...
```

## Testing

Test scenarios can be found in the `tests` folder.

```bash
vendor/bin/phpunit
```
