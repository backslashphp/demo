# Backslash Demo Application

This is a simple todo list application designed to organize tasks within projects and monitor their completion.

Its goal is to demonstrate how [Backslash](https://github.com/backslashphp/backslash) components work together in order
to power event-sourced systems in PHP.

The application is interacted with through a few scripts located in the `bin` folder. It relies solely on Backslash for
simplicity in learning, without using any additional libraries.

Featured Backslash components are:

- Commands and command dispatcher
- Projectors and projections
- Processor
- Projection rebuilding
- Stream enrichment
- Testing scenarios

## Getting started

Start by installing dependencies with Composer:

```sh
composer install
```

Then run the `demo.php` script:

```sh
php bin/demo.php
```

It initializes the SQLite database in `data/demo.sqlite` where events and projections are persisted. It also creates
some projects and tasks.

As you play with the app, you may open the SQLite database in your favourite IDE to inspect its content.

## Usage

### List projects and tasks

```sh
php bin/list.php
```

### Create a project

```sh
php bin/create-project.php "My project"
```

### Create a task

```sh
php bin/create-task.php 1 "My task"
```

### Mark a task as started

### Mark a task as completed

### Move a task to another project

### Rename a task

### Rename a project

### Delete a task

### Regenerate demo data

### Clear all data

## Projection rebuilding

This script deletes all projections and rebuilds them by replaying events.

```bash
php bin/rebuild-projections.php
```

## Testing

```bash
vendor/bin/phpunit
```
