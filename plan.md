1. **Understand the problem**:
    - The method `updateAcademyCourseStructure` in `Modules/Worship/App/Services/WorshipApiService.php` takes an array of `$data['modules']` and loops through it.
    - Inside the loop, it performs an N+1 query: `$module = AcademyModule::where('course_id', $course->id)->find($moduleData['id']);`
    - It then updates or creates the module, and goes into another loop for lessons, doing `$lesson = AcademyLesson::find($lessonData['id']);` which is another N+1.
2. **Optimize**:
    - Load existing modules for the course ahead of time and store them in an associative array keyed by ID.
    - Load existing lessons for those modules ahead of time and store them in an associative array keyed by ID.
    - Loop over the provided data. Find modules/lessons in the pre-loaded arrays. Only issue DB updates/inserts, no selects inside the loops.
3. **Verify**:
    - Run `php artisan test` or similar to ensure tests pass.
    - Pre-commit checks.
