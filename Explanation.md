# SA Hyperlink Crawler

This document outlines:
- the problem to be solved in my own understanding
- the technical specification of the design of this plugin and how it works
- how this plugin achieves the desired outcome per the user story
- the planned structure for the plugin

## What exactly is the problem?
First, I can understand that website owners always want to improve their website homepage. And the main factor to know  the best way to improve is by understanding wha users see when on the website's homepage. The issue with this is the website owner does not have visibility into which links users actually see when the homepage loads.

So the goal is to create a plugin that:
- Tracks which hyperlinks are visible in the portion of the homepgae that a user sees immediately upon loading (without scrolling down).
- Record this data, including the screen size, for each visit.
- Cleanup the data so it only has the record of the last 7 days ( i.e consistently remove data older than 7 days)
- Let the site owner be able to view this data

## How this is to be achieved technically
This plugin achieves the goals above by:
1. Create Javascript trackiing script to collect visible links and screensize as dat on the homepage
2. Create a REST API endpoint leveraging WordPress inbuilt Rest API to collect the data from the javascript track script
3. Create a custom table in the wordpress installations database to store the collected data
4. Create an Admin Page where the site owner can see the stored data
5. Create scheduled task to delete stored data older than 7 days
Since we are done with all the creating, then:
6. Add the javascript tracking script to the homepage using the wordpress inbuilt wp_enqueue hook
7. Add the callback for the REST API that receives, processes and send the data to the database to be stored
8. Add method to fetch the data from the database and show it to the site owner on the admin page
9. Add the method for the cron that handles deleting the data older than 7 days

This entire process needs 4 major components, which are:
### JavaScript Tracker Script
- Runs on homepage.
- Detects links currently visible at load (above the fold).
- Captures viewport dimensions(screen size).
- Sends JSON payload to backend endpoint.
  
### Backend (REST API Endpoint)
- Receives POST data at REST or admin endpoint.
- Validates and sanitizes input.
- Stores visits in a database table: `id, timestamp, screen_width, screen_height, hyperlink`.

### Admin Interface
- Accessible to site owner.
- Displays recent visits (past 7 days).
- Shows screen size and list of visible links per visit.
- Includes filter or summary view (nice to have).

### Cleanup Mechanism
- WP cron to delete records older than 7 days.

## Why WordPress
- The platform already powers many sites so testing is easy.
- REST routes, cron jobs and admin pages come built in.
- You can manage settings and data without leaving the dashboard.
- The plugin approach lets you install or remove features without touching core files.

## Technical Decisions Made and Reasons
- Each feature sits in its own class so you can extend or replace parts later.
- Namespaces keep everything scoped and avoid clashes.
- Visits live in a dedicated table for quick queries.
- Nonces protect the REST route from bad requests.
- The tracking script gets its endpoint and nonce via `wp_localize_script`.
- A daily cron clears data older than seven days.
- Unit and end-to-end tests cover key paths.

## How this plugin achieves desired outcome per user story
- The script runs on the homepage and finds links that appear without scrolling.
- It sends those links and the viewport size to the REST endpoint.
- The endpoint stores the visit with a timestamp.
- The admin page shows each visit with screen size and links seen.
- Cleanup keeps the list to the last seven days so reports stay relevant.
- You can review this data and adjust your layout based on what visitors saw.

## My approach and why
- Break the work into tracking, storing, viewing and cleanup.
- Use namespaced classes so you can add features without clashes.
- Build the JavaScript tracker first to capture real data early.
- Store visits in a custom table for faster lookups.
- Provide basic tests even if some fail in this environment.
- Focus on simple code that follows WordPress conventions.

## Detailed Technical Implementation

### Architecture Overview
The plugin follows a modular architecture with clear separation of concerns:

1. **Core Plugin Class (`SHC_Plugin_Class`)**
   - Manages plugin lifecycle (activation, deactivation, uninstall)
   - Coordinates between different components
   - Handles dependency injection and initialization

2. **Database Layer (`SHC_Database`)**
   - Custom table creation and management
   - CRUD operations for visit data
   - Data sanitization and validation
   - Implements WordPress database best practices

3. **Frontend Tracking (`SHC_Crawler`)**
   - JavaScript implementation using modern ES6+ features
   - Intersection Observer API for viewport detection
   - Efficient DOM traversal for link detection
   - Error handling and fallback mechanisms

4. **API Layer (`SHC_RestEndpoint`)**
   - RESTful endpoint implementation
   - Request validation and sanitization
   - Rate limiting and security measures
   - Proper error responses and status codes

5. **Admin Interface (`SHC_AdminPage`)**
   - WordPress admin page integration
   - Data visualization and filtering
   - Export capabilities
   - User-friendly interface design

6. **Maintenance (`SHC_Cron`)**
   - Scheduled cleanup tasks
   - Database optimization
   - Logging and monitoring

### Security Considerations
- Input validation at all entry points
- Nonce verification for all requests
- Capability checks for admin functions
- Data sanitization before storage
- XSS prevention in output
- CSRF protection
- Rate limiting for API endpoints

### Performance Optimizations
- Efficient database queries with proper indexing
- Minimal JavaScript footprint
- Asynchronous data collection
- Caching where appropriate
- Batch processing for cleanup tasks

### Testing Strategy
- Unit tests for core functionality
- Integration tests for component interaction
- End-to-end tests for critical paths
- Performance testing for data handling
- Security testing for vulnerabilities

### Future Enhancements
- Advanced analytics and reporting, considering how much data the plugin might be storing been able seeing how this data can be utilised for analytics looks so interesting to me, might end up been a premium feature
- Usually users and corporates always have GDPR concerns would be nice to be able to tell the user that a certain type of data is been collected stored and delete within 7 days ( notice how i didn;t save any of the user data as well)
- Custom dashboard widgets 
- Export/import functionality, the site owner will definitely think of exporting the data out of the platform for analytics and processing as well would be a good feature to have
- API rate limiting configuration this is mainly for performance especially for sites with medium to high traffic.
- Custom retention periods this will be a great premium feature this might have to be a user(site visitor) or site owner centered feature.
- Real-time monitoring

This implementation provides a solid foundation that can be extended while maintaining performance, security, and maintainability.