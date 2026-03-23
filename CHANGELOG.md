# Changelog

All notable changes to this project will be documented in this file.

## [1.0.26-1] - 2026-02-04

### Added
- **100% Local Font Asset Strategy**:
    - Integrated `log1x/laravel-webfonts` for self-hosting Google Fonts.
    - Downloaded and local-hosted **Inter** (400, 500, 600, 700) and **Poppins** (400, 700) font families.
    - Configured `@preloadFonts` Blade directive in all master layouts.
    - Standardized global font scheme: Inter (Sans) and Poppins (Display) via Tailwind 4 theme.
- **Worship Academy: Devotionals**:
    - New "Devotional" lesson type with Bible search integration.
    - Integrated with Bible module API (`/api/v1/projection/find`).
    - Added `bible_reference` field to `AcademyLesson` model.
- **Worship Academy: Localização PT-BR**:
    - Complete translation of Course Builder (`CourseBuilder.vue`) and Lesson Editor (`LessonEditor.vue`).
    - Localized all form labels, placeholders, alert messages, and instructional texts.

### Improved
- **ChordPro Editor**:
    - Implemented real-time reactive preview using computed properties.
    - Added styling support for ChordPro directives like `{c: comment}` and metadata.
    - Enhanced preview aesthetics with better spacing and high-contrast styling.
- **Projection Module & Bible Plan PDF**:
    - Corrected Bible search API paths to use `/api` prefix, resolving 404 errors.
    - Localized Bible Plan PDF view (Print simulation) by removing Google Fonts and implementing local preloading.
    - Standardized 15+ module master layouts with `@preloadFonts`, eliminating external Bunny Fonts links project-wide.
    - Verified 0 external calls for fonts/icons across entire codebase.

### Security & Privacy
- Removed all external dependencies on Bunny Fonts and Google Fonts API to ensure 100% internet-independent operation and user privacy.
- Standardized Font Awesome Pro 7.1.0 to be served exclusively from the local `public/vendor` directory.
