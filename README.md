# Student Progress Tracker

A Simple Student Progress Tracker. 
Utilises JSON data to store the progress of the student (simulating an HTML payload of the object),
Stores data for duration of session. 

Utilises Laravel 10, PHP 8.3, Bootstrap 5 and jQuery. Hosted via Laravel Vapor.

#### SEE THIS PROJECT LIVE: https://gv3j4lkxur7g4itjabgwaay4y40pjncf.lambda-url.ap-southeast-2.on.aws/


## Objectives/Features

A simple Student Progress Tracker application that:

1. Displays a student name and course title (e.g. Certificate III in Business).

2. Lists five course units, each with a checkbox or button to “Mark Complete”.

3. Shows overall progress as a percentage with a Bootstrap progress bar.

4. Allows the user to mark units as complete and updates the progress dynamically using jQuery and AJAX.

5. Stores progress in-memory or session for the duration of the session (no persistence required).


## Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install` (or your package manager of choice. I use pnpm)
4. Run `php artisan serve` to launch the server (It should be on http://localhost:8000/ but it double check the console.)
    * Note: I ran this via Laravel Herd.

## Bugs

Please let me know if there's any bugs.
- On the live version, the page's title defaults to 'Laravel'. On the local version, it defaults to 'Student Progress Tracker'


## License

This component is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
