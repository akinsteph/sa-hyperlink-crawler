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
1. Create javascript trackiing script to collect visible links and screensize as dat on the homepage
2. Create a REST API endpoint leveraging WordPress inbuilt Rest API to collect the data from the javascript track script
3. Create a custom table in the wordpress installations database to store the collected data
4. Create an Admin Page where the site owner can see the stored data
5. Create scheduled task to delete stored data older than 7 days
Since we are done with all the creating, then:
6. We add the javascript tracking script to the homepage using the wordpress inbuilt wp_enqueue hook
7. We add the callback for the REST API that receives, processes and send the data to the database to be stored
8. We add method to fetch the data from the database and show it to the site owner on the admin page
9. We add the method for the cron that handles deleting the data older than 7 days

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

## Technical Decisions Made and Reasons

## How this plugin achieves desired outcome per user story