# Student Progress Tracker

A Simple Student Progress Tracker. Utilised JSON data to store the progress of the student (simulating an initial Database call),
Stores data for duration of session. 

Utilises Laravel 10, PHP 8.3, Bootstrap 5 and jQuery. 

SEE ME LIVE: 

Please let me know if there's any issues with the launch. 

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
4. Run `npm run dev`
5. Run `php artisan serve` and then `npm run dev`. 
* Please note, I couldn't get the php artisan serve barebones version working, as it clashed with Laravel Herd. 
* So if the code doesn't work consider launching it through that.


## License

This component is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
