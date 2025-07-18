=== Events Manager - Calendar, Bookings, Tickets, and more!  ===
Contributors: msykes, pxlite, nutsmuggler, netweblogic
Donate link: https://wp-events-plugin.com
Tags: events, calendar, tickets, bookings, appointments
Text Domain: events-manager
Requires at least: 6.1
Tested up to: 6.8
Stable tag: 7.0.5
Requires PHP: 7.0
License: GPLv2

Fully featured events calendar, booking registration (RSVP), recurring events, locations management, Google Maps

== Description ==

Events Manager is a full-featured event calendar, bookings and registration management plugin for WordPress based on the principles of flexibility, reliability and powerful features!

* [Demo](https://eventsmanager.site)
* [Documentation](http://wp-events-plugin.com/documentation/)
* [Tutorials](http://wp-events-plugin.com/tutorials/)

= Main Features =

* Beautiful calendars, search pages, lists, grids and booking forms to enhance your site events.
* Easy event registration (single day with start/end times)
* Recurring and long (multi-day) event registration
* Bookings Management (including approval/rejections, export CVS, and more!)
* Multiple Tickets
* Fully-featured graph and statistics including bar/line/pie with comparison and stacking
* MultiSite Event Support
 * Cross-Network Event Sharing - show your events and booking fromss on other subsites or main site
 * Network-wide Global Booking Management
 * BuddyPress and BuddyBoss Support
 * Create modular (independent) event subsites or inter-networked events
* Multiple Location Types
 * Physical Locations
 * Online Events (URLs)
 * [Zoom Webinars/Meetings Integration](https://wordpress.org/plugins/events-manager-zoom/)
* BuddyPress & BuddyBoss Support
 * Submit Events
 * Group Events
 * Personal Events
 * Activity Stream
 * more on the way
* Guest/Member Event submissions
* Assign event locations and view events by location
* Event categories
* Easily create custom event attributes (e.g. dress code)
* Google Maps [(see our API usage recommendations)](https://wp-events-plugin.com/documentation/google-maps/api-usage/?utm_source=repo&utm_medium=readme&utm_campaign=gmaps-api)
* Advanced permissions - restrict user management of events and locations.
* Widgets for Events, Locations and Calendars
* Fine grained control of how every aspect of your events are shown on your site, easily modify templates from the settings pages and template files
* iCal Feed (single and all events)
* Add to Google Calendar buttons
* RSS Feeds
* Compatible with SEO plugins
* Timezone Support - create events in different timezones 
* Plenty of template tags and shortcodes for use in your posts and pages
* Actively maintained and supported
* Lots of documentation and tutorials
* And much more!

= Data Privacy and GDPR Compliance =
We provide the tools to [help you be GDPR compliant](http://wp-events-plugin.com/documentation/data-privacy-gdpr-compliance/), including:

* export/erasure of data via the WordPress Privacy Tools, including booking, event and location data
* consent checkboxes on our booking, event and location forms on the frontend
* settings to control what can be exported/erased as well as where/when to place consent requests
* sample text for your site privacy policy describing what Events Manager does with personal data

= Premium Features =

We have a premium [Pro add-on for Events Manager](http://eventsmanagerpro.com/gopro/) which not only demonstrates the flexibility of Events Manager, but also adds some important features including but not limited to:

* WooCommerce integration ([sold separately](https://em.cm/wc))
* PayPal, Stripe, Authorize.net and Offline Payments
* Custom booking forms
* Individual Attendee custom forms
* Upload fields for bookings, attendees and users
* Printable Invoices and Tickets
* Send PDF tickets/invoices by email automatically
* Check In/Out
* QR Scanning
 * Manage bookings on your phone
 * Check In/Out users
* Waitlists
* Automation - ultimate flexibility in automation!
 * Triggers:
  * X time before/after events start
  * When a booking status changes
  * When a booking was booked x time ago
 * Actions
  * Send Webhook (Zapier, MS Automation and many other services)
  * Send Email
  * Send WhatsApp, SMS, Telegram notifications
* WhatsApp, SMS, Telegram integration and interactive flows
* Coupon Codes
* Custom booking email per event and gateway
* Faster support via private Pro forums

For more information or to go pro, [visit our plugin website](http://wp-events-plugin.com/features/).

= Additional Plugin Integrations =

Whilst there's many third party integrations with our own plugin, here's some we've integrated ourselves!

* Included in Events Manager (automatic integration)
 * [BuddyPress](https://wordpress.org/plugins/buddypress/)
 * [WP FullCalendar](https://wordpress.org/plugins/wp-fullcalendar/)
 * [Thrive Automator](https://wordpress.org/plugins/thrive-automator/)
* Additional Add-Ons
 * [Zoom](https://wordpress.org/plugins/events-manager-zoom/)
 * [WPML Multilingual Sites](https://wordpress.org/plugins/events-manager-wpml/)

== Installation ==

Events Manager works like any standard Wordpress plugin, and requires little configuration to start managing events. If you get stuck, visit the our documentation and support forums.

Whenever installing or upgrading any plugin, or even Wordpress itself, it is always recommended you back up your database first!

= Installing =

1. If installing, go to Plugins > Add New in the admin area, and search for events manager.
2. Click install, once installed, activate and you're done!

Once installed, you can start adding events straight away, although you may want to visit the plugin site documentation and learn how to unleash the full power of Events Manager.

= Upgrading =

1. When upgrading, visit the plugins page in your admin area, scroll down to events manager and click upgrade.
2. Wordpress will help you upgrade automatically.

= Upgrading from version 4 to 5 =

Please [read these instructions](http://wp-events-plugin.com/updating-to-v5/).

== Upgrade Notice ==

For those upgrading from version 4 to 5, please [read these instructions](http://wp-events-plugin.com/updating-to-v5/).

== Frequently Asked Questions ==

See our [FAQ](http://wp-events-plugin.com/documentation/faq/) page for helps with Events Manager - Calendar, Bookings, Tickets, and more!

== Screenshots ==

1. Innovative responsive calendar with rings to show eventful dates, colored by category, clickable to expand more event information.
2. Completely customizable event widgets/blocks and shortcodes.
3. Beautiful event pages which can be completely customized via our settings page.
4. Share your events to popular calendar clients.
5. Display information about your location in widgets, blocks and shortcodes too.
6. Full-featured statistics and insights into your ticket sales via multiple graph types, including comparison and stacking.
7. Easily skip to the future with our new calendar navigation and search filters.
8. Easy-to-use search filters, whether on the phone or desktop.
9. Search for events and locations within a search radius using Google geo searches.
10. Intuitive search UI for your visitors.
11. QUickly switch between search views.
12. View your events on a Google Map, filter with searches.
13. Multiple calendar styles, fully responsive according to the width of the calendar.
14. Responsivve way for mini-calendar to intuitively show dates with many events within a quick glance.
15. Responsive lists that adapt to the size of its containing content.
16. Clean forms for submitting and managing events, as well as booking events for users.
17. Dashboard graph widget for quick review of your event bookings with tons of meaningful data views.
18. Grid view for displaying your upcoming events at a glance

== Changelog ==
= 7.0.5 =
* Improved installation/upgrade logic to allow users to re-trigger `dbem_version` upgrades from 6 to 7 without duplicating recurrence set data.
* Fixed confusing redirect when converting individual repeated events to recurring events—now correctly redirects to the converted event.
* Added `EM_Event->is_repated()` and `$include_repeated` boolean param to `EM_Event->is_recurring()` to differentiate between repeated events and instances of recurring events.
* Fixed image display issues for recurring events on event lists.
* Fixed orphan removals for Multisite installations.
* Fixed date-based event queries failing due to new timezone-relative search features in EM 7.
* Added short circuit to prevent current events from showing as past events in the admin menu so that recurrences appear correctly.
* Fixed front-end editor showing recurrences instead of the main recurring event in the admin events list.
* Fixed fatal error when calling `EM_Event->get_recurrence_description()` on a recurrence.
* Fixed JS error occurring after a successful booking.
* Fixed and mitigated issues where broken event data in the `wp_em_events` table (e.g., null dates) prevented saving that event in the admin area.
* Fixed buggy behavior including false positive datepicker validations when saving a recurring event with primary recurrence set to an "On" frequency.
* Fixed minor PHP warning when RSVP is disabled in the admin settings page.
* Added location duplication feature.
* Added automatic disabling of event and location duplication via third-party plugins (Yoast Duplicate Post, Duplicate Pages, and Copy & Duplicate) to prevent data breakage.

= 7.0.4 =
* Fixed 3 security vulnerabilities reported by Muhammad Yudha and @mikemyers via WordFence. Update strongly advaised.
* Fixed pagination errors introduced in 7.0.3.
* Fixed potential collation issues with DB tables and added more meaningful errors when saving index tables so admins see clearer error messages.
* Tweaked installation and update process to schedule DB updates via `wp_cron` for WP-CLI and auto-updates, rather than relying on a dashboard page load.
* Fixed uninstall fatal error caused by the new `recurrences` table introduced in v7.

= 7.0.3 =
* Fixed code to prevent fatal error in some instances where users run a widget.
* Added additional CSS selector/detector to booking form JS dynamic loading to detect waitlist forms.
* Changed uploader to initialize on `init` so that multisite global options are applied.
* Moved `EM_MS_Globals` out of `events-manager.php` into its own class file.
* Fixed bug in multisite global tables mode showing faulty recurrence set records in the editor.
* Added recurring event recurrence description to events admin list.
* Changed default scope to 'all' for any post status other than 'All' or 'Published' in admin events list.
* Added `event_type` search attribute for `EM_Events::get()`, accepting comma-separated list or array of event types to include.
* Added `post_id` accepted boolean values (or `'true'` / `'0'`) to include or exclude events with a post ID (essentially, include/exclude recurrences).
* Fixed search form not working in shortcode using `has_search` due to view container ID mismatches.
* Fixed potential widget fatal errors.
* Added cache flushing when editing category colors or images to update cached pages throughout a site.
* Fixed `EM_Event->save()` invocation trying to create a post even if it’s an event recurrence.
* Fixed ability to add higher than `event_status` 1 and added `em_get_post_status` to allow custom post statuses in WP admin.
* Fixed end-of-month jumps when `empty_months` is set to false.
* Changed newly added `empty_months` shortcode prop to `true`, which mimics previous calendar behavior.
* Fixed orphaned events remover in admin tools incorrectly including new recurrences as orphaned events.
* Fixed conversion issues from repeated to recurring events, now prompting re-conversion for upgraded event installs.

= 7.0.2 =
* Fixed recurring event editor UI display issues on front-end for recurring/repeated event patterns.
* Fixed bbPress fatal error.
* Fixed ticket start/end times being ignored due to new overriding ticket settings.

= 7.0.1 =
* Added recurring events functionality, which now hosts one page for all events of that recurrence type.
* Added recurrence booking form picker including a calendar and dropdown selection.
* Added support for true timezone-relative calendar and recurrence selection listings via the `calendar_timezone` attribute.
* Added support for searching in timezone-relative scopes for events using the `timezone_scope` attribute.
* Added admin support for viewing bookings belonging to recurring events.
* Added conversion features to transfer repeated (previously called "recurring") events into recurring events.
* Added 302 redirection functionality for converted repeated > recurring events.
* Moved booking form JS into externally and dynamically loaded JS file.
* Added month skipping navigation in calendars allowing skipping months with no events.
* Added multiple calendar UI display tweaks/fixes to eventful and today months.
* Fixed blank calendar dates showing display dates.
* Added different calendar header option via the `calendar_header` attribute.
* Added `setStartOfMonth()` and `setEndOfMonth()` to `EM_DateTime`.
* Added timezone display options in `EM_DateTime::getDate()` and `getDateTime()` functions.
* Changed selectize JS to dispatch a `CustomEvent` object from parent element with `detail` containing selectize objects.
* Post ID is now optional for events.
* Fixed loading order of translated string assignments so they occur after init, while hard-coding potentially required strings during previously translated actions/filters.
* Updated readme "tested up to".
* Namespaced Selectize and the `.selectize()` functions to `EM_Selectize` and `.em_selectize()` respectively to avoid collisions with other plugins.
* Fixed phone input button styling clashes in manual bookings and potentially other pages.
* Moved `EM_Scripts_and_Styles` out of `events-manager.php` into its own class.
* Added `EM\Scripts_and_Styles::add_js_var()` allowing for footer localization.
* Added `em_wp_localize_script_footer` allowing plugins to override EM localized script vars.
* Added a catch exception in `EM_DateTime::modify()`.
* Renamed some PHP variables in overriding methods (minor).
* Added multi-layer recurrence patterns including exclusions so events can have multiple recurrence patterns.
* Added `event_type` field allowing for recurrences, repeated, and recurring event types.
* Changed recurring event saving logic to prevent deletion unless explicitly rescheduled or removed.
* Added cancellation/deletion options for recurrences not included in newly rescheduled patterns.
* Improved event update logic so that only new recurrences are added during rescheduling.
* Changed vocabulary from “recurring events” to “repeating events” for clarity and future compatibility.
* Updated ticket logic to support recurrence/override patterns while maintaining parent-child relationships.
* Added `Recurring_Sets` and `Recurring_Set` objects to handle recurrence data.
* Rewritten recurring event logic for greater flexibility and future extensibility.
* Added `EM_Ticket::get()` for cache-friendly ticket retrieval.
* Added `EM_DateTimeZone::getCity()`.
* Deprecated `recurrence_` fields in `events` table.
* Added `em_event_recurrences` table.
* Changed ticket deletion so it requires an event save.
* Added nonce safeguards for disabling RSVP/bookings, deleting recurrences, and rescheduling tickets.
* Added `em_datepicker_format()` function to output datepicker format.
* Fixed uploader validation issues in JS and PHP caused by blank default extensions settings for event/location image uploads.
* Added non-escape option to allow HTML sub-values in attendees mulitple column data views on bookings table.
* Removed JS requirement for asset selectors to be wrapped by `em` in dynamic asset loading.
* Improved dynamic asset loading by pre-loading asset groups before firing `onload` events.
* Improved booking form JS by encapsulating container scopes within functions and events rather than scoping at the document level.
* Fixed phone input field JS error.
* Fixed missing uploader field minified CSS files.
* Fixed calendars showing the wrong month when there's a long event starting in an earlier month.
* Fixed ticket caching issue when saving events, which caused the event editor/page to show outdated ticket data such as prices.
* Added `EM_Event->just_disabled_rsvp` to detect RSVP being disabled during an event save process.

= 6.6.4.4 =
* Re-added cancellation checks before processing a booking, previously added in 6.6.4.2 and removed in .3 due to urgent validation reports.
* Fixed image validation issues introduced in 6.6.4.2

= 6.6.4.3 =
* Fixed bug in 6.4.4.2 showing events as cancelled when attempting to make a booking

= 6.6.4.2 =
* Fixed map display issues when using empty `[locations_map]` and `[events_map]` shortcode.
* Fixed improper loading of default permitted upload extensions which can cause fatal errors in Pro form builder.
* Fixed grouped events list pagination breaking due to incorrect AJAX action command.
* Fixed category searches not persisting between AJAX reloads.
* Fixed and removed ability to book an event in the trash, reported by Revan Arifio via PatchStack.
* Fixed breaking change in BuddyBoss groups due to updated function use for BuddyPress integration.

= 6.6.4.1 =
* Changed `EM_Mailer` so that `send()` can be called statically and non-statically to avoid legacy errors.
* Removed redundant `EM_Booking->process_meta()` function already in `EM_Object`.
* Fixed booking meta processing issues omitting certain field keys with special non-alphanumeric characters.
* Fixed potential PHP fatal error when exporting CSV reports.
* Fixed image size validation issues in visual uploader.
* Fixed PHP notice when validation fails for phone numbers during booking user initial registration.
* Tweaked UI visuals to improve sizing and compatibility.
* Fixed issue where undo upload deletion options were not appearing.
* Fixed uploader booking admin views loading uploaded files twice for preview in editor/viewer modes.
* Improved `EM_Ticket_Booking->update_meta()` so it deletes array keys that aren't defined anymore.
* Improved `EM_Booking->update_meta()` so a third parameter `$subkey` restricts deletion to a specific key in a meta group.
* Added further encapsulation of data in uploader to avoid dependence on `$_REQUEST` data and allowing for multi-dimensional layers of a booking (such as a multi-booking upload).

= 6.6.4 =
* Fixed security vulnerability allowing SQL injection responsibly disclosed by mikemyers via WordFence Security Services.
* Added `em_bookings_table_display_hidden_input` hook to bookings table.
* Fixed missing bookings for person/user view of bookings.
* Added possibility for multiple person search in bookings, allowing viewing of bookings from more than one user programmatically.
* Updated plugin header with license and updated copyright year.
* Added HTML props to booking editor view allowing access to booking sections by ticket ID via JS.
* Fixed PHP notices with interfering plugins via `the_content`.
* Fixed PHP fatal error when installing/upgrading in WP versions < 6.1.
* Fixed PHP fatal error due to plugin conflict invoking `parse_query` in different ways.
* Fixed translation issue in calendar modal title not using localized domain.
* Fixed missing div in `templates/forms/event-editor.php`.
* Changed `maps-global.php` to store map JSON data as an `application/json` script element rather than a regular div to improve SEO and coding standards.
* Migrated partial use of jQuery in `maps.js` (minor).
* Fixed `not_all_day` conditional placeholder showing same result as `all_day`.
* Fixed EM taking over 'scheduled' posts view for any CPT type and showing all posts instead.
* Fixed BuddyPress group nav links generating PHP error due to deprecated function.
* Fixed events BuddyPress group link not showing in groups nav bar since a recent EM update.
* Fixed calendar advanced search disappearing when filters are chosen and first search is initiated.
* Fixed taxonomy single term pages showing up blank on some themes when overriding formatting is enabled.
* Changed `EM_Taxonomy_Frontend` to use static binding.
* Fixed duplicate map placeholders showing in AJAX calls when searching.
* Fixed non-AJAX pagination persistence issues when coupled with search form.
* Added `has_search` support to `events_list`, `events_map`, `events_list_grouped`, `locations_map`, `locations_list` shortcodes allowing for search forms to be added above.
* Optimized code by removing redundant/duplicate code fragments and centralized shortcode and list generation using `em_output_events_view()` and `em_output_locations_view()`.
* Fixed maps JS display bug after an AJAX search.
* Fixed search form JS issues when searching with non-AJAX mode.
* Fixed minor attendee form aesthetic issues in booking editor.
* Fixed consent functionality preventing event submission forms from going through.
* Fixed phone number field setting saving issues for restricting countries.
* Added support for `EM_Object->add_error()` for `EM_Exceptions`.
* Implemented new uploader UI and further integration with FilePond by pqina.
* Completely revamped uploading API via `EM\Uploads\Uploader` and `EM\Uploads\API` classes.
* Added `update_meta` function for updating individual `EM_Ticket_Booking` object meta items.
* Added `em_ticket_booking_save` filter for `EM_Ticket_Booking` object.
* Added new JS/CSS loading module which loads individual assets only when needed via JS.
* Fixed countries list inconsistency if adding blank files consecutively followed by another call without adding blanks.

= 6.6.3 =
* Fixed JS error preventing full bookings table admin AJAX functionality in certain languages such as French.
* Fixed SQL ordering issues causing empty attendee and ticket views in bookings admin area.
* Added 'booking' array arg to em_bookings_table_get_booking_allowed_actions filter.
* Fixed WPML error when duplicating an event.

= 6.6.2 =
* Added aria-label to ticket selection.
* Disabled phone module for PHP versions 8.0 due to compatibility issues.
* Fixed consent not passing validation if marked required yet checked.
* Fixed ordering issues in booking admin tables, including the inability to order booking fields in a ticket view.
* Fixed general orderby failing to apply if passed with an ASC or DESC definition.
* Fixed status filtering issues in booking admin tables.
* Changed some PHP array declarations to bracket shortform.

= 6.6.1 =
* fixed some initial phone number display issues overlapping country selectors on front-end
* fixed fatal error in settings page

= 6.6 =
* Added communications consent. Ask or require users to consent to being contacted, with history of last acceptance or revocation accross all bookings/user (if admin has caps)
* Added international phone number input field and validation, see the Phone Numbers section on Settings > General
* Fixed bookings not showing in bookings table when in Multisite Global Mode
* Fixed scope filter saving issues in booking/event-booking admin tables
* Changed default scope to 'future' for booking/event-booking admin tables
* Transitioned known booking meta keys from legacy to new format for storage, with plans to phase out support for previous format of _x_... for arrays, opting for _x|... instead ('registration', 'attendees', 'coupon', 'booking', 'zoom', 'test', 'discounts', 'surcharges')
* Updated countries list to include more translations, file-separated storage for optimal loading, and extra missing countries - Kosovans, we've migrated the KV country code to the more recognized XK code including migrating previous location data
* Moved admin-settings.js into external included file rather than inline.
* Transitioned phone numbers into real feature.
* Added example real-time input in settings.
* Added `EM_Booking->get_meta()`, `EM_Booking->update_user_meta()`, and `EM_Booking->get_user_meta()`.
* Added `em_bookings_get_sql_orderby_joins` filter for custom ordering options.
* Added `EM_DateTime::create()` for chaining quick dates.
* Added `em_person_display_summary_bottom` action.
* Updated intlTelInput to 23.0.8.
* Fixed selectize.js not allowing custom data- properties in non-multiple selectize dropdowns.
* Fixed JS modal.remove() JS error in list tables introduced in dev versions.
* Moved all JS UI setups (datepicker, time, tippy, phone, selectize) into `em_setup_ui_elements()` for easy reloading in containers.
* Added `em_nouser_booking_details_modified` action.
* Moved data privacy/consent into own classes folder with parent/child structure and standardized functionality between privacy/comms consent.
* Transitioned known booking meta keys into newer piped format to break up arrays rather than underscores including (registration, attendees, coupon, booking, zoom, test, discounts, surcharges).
* Fixed some display issues on view person bookings admin page.
* Fixed PHP warnings on empty graphs in booking dashboards.
* Fixed empty graph data when viewing booking data as event admins without manage_others_bookings capabilities.
* Added more WP_Screen compatibility on front-end in case other plugins load template.php but not WP_Class which results in a fatal error.
* Fixed backend events with bookings admin tables linking to front-end.
* Fixed PHP notices on bookings admin tables.
* Fixed $location_fields fatal PHP errors when loading maps and location-dependent event search queries.
* Added option to exclude taxes from subtotal in booking summary pricing.
* Added `EM_Bookings` search by `booking_id`.
* Fixed `EM_Booking->can_rsvp()` occasionally providing incorrect result if number types are strings, as well as check to make sure booking is approved.
* Added requirements check class.
* Added `events_manager_plugin_loaded` action for loading any EM-dependent plugins early on in `plugins_loaded`.
* Improved consent options including better `EM_Person` consent checks and a default to consented option if user already active (in development).
* Fixed rows setting not getting saved.
* Fixed summary issues showing empty summary section, fixed typo in new option from last commit (in development).

= 6.5.2 =
* Fixed fatal error on dashboard with Charts widget enabled.
* Fixed 'array_key_exists' fatal error (not reproduced) on bookings dashboard in limited cases - pending further confirmation to reproduce/fix potential underlying issue.
* Fixed JS issues preventing event links from being clicked on the 'events with bookings' list table.
* Minor CSS fixes showing button outlines when clicked.
* Fixed rare calendar issue preventing future months from showing events.
* Removed all unnecessary query string params aside from yr/mo in calendar navigation links.
* Fixed calendar search trigger not working when default search is set to inline mode.
* Fixed rare selectize JS issue not being initialized properly due to jQuery firing before DOMContentLoaded.
* Fixed settings/tags/taxonomies having white as default color as opposed to the settings page default and also defaulting to #80b538 avoiding white-on-white display issues.
* Added ordering to savable filtering options.

= 6.5.1 =
* Fixed hard-coded naming of bookings table for SQL query affecting list table searches on WP installs with custom DB prefixes.
* Fixed PHP warning on bookings list tables.
* Fixed some default settings not saving properly.
* Added `em_bookings_table_get_item_limits` filter to add/modify custom limits, applicable to other tables such as `em_transactions_table_get_item_limits`.
* Fixed status filter not working in bookings admin table.
* Fixed fatal PHP error when grouping events in shortcode (bug introduced in 6.5).
* Fixed "Ticket Spaces" column not ordered due to naming conflict with `ticket_spaces` db field name.
* Changed "Ticket Spaces" column key from `ticket_spaces` to `ticket_booking_spaces`, the "Ticket Spaces" column index is now "Ticket Capacity".

= 6.5 =
* Added multi-array support for EM_Bookings_Table::get_booking_actions() allowing for separate sections of actions
* Removed em-bookings-action and em-bookings-action-X classes from booking actions links in preference of data- attributes and an em-list-bookings-row-action
* Added sortable ticket columns to bookings admin tables
* Improved bookings table search input allowing more search options, including user name and email
* Fixed bug where em_bookings_get_sql_conditions filter is called twice in EM_Bookings_Table::get_sql_conditions()
* Changed minimum PHP requirement to 7.0 due to use of shortcut array syntax, null coalescing and ternary operators
* Added sanitization/decoding options for shortcode format content due to security implications.
* Fixed bug in calendar widget preventing saving/loading on widget area.
* Added calendar_size option to calendar widget.
* Fixed PHP warning when calendar_size is undefined.
* Added medium calendar size options to widget and settings ddms (in development).
* Added .em-loading and .em-working (wrapper) classes for more cross-theme compatibility when loading something via AJAX.
* Moved .em-warning into scss.
* Removed legacy search form CSS from events_manager.css.
* Updated and rewritten EM_List_Tables to provide base table functionality for other data tables, support front-end and advanced ordering/searching capabilities.
* Updated Bookings and Event with Bookings tables to unify front/back-end, added new views (tickets, attendees), sortable columns, responsiveness and much more.
* Unified/standardized exporting of list tables to support exporting for any EM_List_Table-extended tables.
* Added #_BOOKING_UUID placeholder.
* Added EM_Bookings::get(), EM_Ticket_Bookings::get() and EM_Tickets_Bookings::get() search functionality with advanced ordering capabilities.
* Added EM_Events::get_accepted_fields() for SQL ordering detection.
* Transitioned out of jQueryUI sortable for booking tables to a vanilla JS alternative - [Sortable 1.15.2](https://github.com/SortableJS/Sortable).
* Removed all outdated tables in events-manager/admin/bookings folder.
* Added booking dashboard charts support for front-end with option to specifically enable/disable in settings.
* Added default calendar size option.
* Fixed calendar JS errors when switching months.
* Added custom event for modal opens.
* Fixed tippy regeneration issues due to uncontained calls to tippy().
* Fixed tooltips referencing external elements via the data-content attribute not displaying that content accordingly.
* Fixed privacy nag issues when editing bookings already made in the past before enabling privacy settings.

= 6.4.10.2 =
* added default calendar size option to settings page
* fixed bug in calendar widget preventing saving/loading on widget area
* added calendar_size option to calendar widget
* fixed PHP warning when calendar_size is undefined
* added shortcode format options due to security implications, see [our docs](http://em.cm/shortcode-security) for more info.

= 6.4.10 =
* 'fixed' false positive Avast vulnerability alert on browsers, caused by the minified EM js file
* added option to include minified or non-minified JS/CSS files in Advanced Optimization settings, JS turned off by default due to above errors
* fixed taxonomy images size display issues in admin area if too large
* fixed taxonomy colors not seeming being saved due to caching issues (now clears color cache upon save)
* added calendar_month_nav and calendar_nav args for calendars
* added future events only option for calendar widget

= 6.4.9 =
* changed escaping of HTML so that admins with unflitered_html cap can submit anything to settings or alternatively allows if EM_UNFILTERED_HTML is defined true
* fixed bookings graph views not comparing previous periods in some filter combinations, showing only the first period
* Fixed vertical scrolling issues with multidropdown mode (headings) on advanced search modals.
* Fixed hidden advanced search modal blocking trigger buttons from re-displaying advanced search on mobile view.
* Fixed #_EVENTTIMES_LOCAL and #_EVENTDATES_LOCAL not working when supplied JS formatting unless placeholder supplied beforehand without formatting.
* Fixed #_BOOKINGBUTTON issues with cancellation clicks.
* Fixed PHP error with errant redirected permalink with sites using legacy permalink structures.
* Added missing_creds error to OAuth API EM_Exception.
* Removed id query param from calendar nav links to improve SEO and reduce server load.
* Added calendar_nav_nofollow parameter for shortcode, allowing nav links to be nofollow.

= 6.4.8 =
* fixed XSS vulnerability in shortcodes we recommend updating if you allow guest event submissions and shortcdoes (props to WordFence Security for responsible disclosure)
* fixed permalink clashes for location/taxonomy ical links getting overriden by general events if their permalinks path are within the events subdirectory
* improved location search parameters to accept comma-separated values for filtering by multiple towns,countries,states,postcodes and regions, including exclusions
* added format value to events_calendar shortcode, [event_tags] and [event_tag] shortcodes
* added EM_UNFILTERED_HTML constant, which does not apply wp_kses_post to settings if user has 'unfiltered_html' capability
* fixed calendar navigation issues when supplying a format argument,
* fixed calendar navigation not persisting when using some new options such as calendar_preview_mode_date,
* fixed calendar property calendar_dates_height 'auto' value being ignored,
* fixed calendar show_search being ignored and not showing search bar above calendars in shortcode/php calls,
* fixed display issues for selectize in wp admin area
* fixed #_EVENTTAGSLINE showing 'no categories' message if empty,
* added 'missing' #_EVENTOFFICE365LINK placeholder
* tweaked category/tag shortcode to use em_get_ functions instead of directly invoking class
* fixed some situations showing incorrect URLS on login redirect_to rather than current page reload
* added telephone field type to booking form phone input field (should default to text if not enabled)
* fixed extra line breaks in bookings table actions dropdown
* fixed typo translation domain in "my bookings" page button
* added mail filters em_mailer_send_parameters (for all email methods) and em_mailer_wp_mail (for wp_mail)
* fixed aesthetic display issues in admin for selectize by loading all partials into .wp-admin selector context

= 6.4.7.3 =
* fixed pagination issues in non-event lists where PAGE is double url-encoded
* fixed array to string conversion PHP warning in calendars
* changed default event templates to be in a page format rather than post

= 6.4.7.2 =
* fixed buddypress menu issue introduced in 6.4.7 by re-adding commented-out line
* fixed advanced search options not showing up in some inline setting combinations
* fixed 'hidden' feature in 6.4.7 for saved searches via cookies defaulting to enabled (can now be re-enabled in wp_options -> dbem_search_form_cookies)
* updated 6.4.7 changelog with some missing changes
* added selectize options to em_options_select() function
* alpha feature - added phone field options to settings page if EM_PHONE_INTL_ENABLED is enabled (validation and more options on the way)
* moved welcome notice option to EM_Admin_Notices
* removed timthumb admin notice (outdated)
* fixed minor security vulnerability allowing multisite blog admins to dismiss Events Manager network admin welcome notice (reported by PatchStack)
* fixed medium security vulnerability allowing unauthorized users to modify booking statuses (reported by WordFence Security)
* fixed medium security vulnerability allowing stored XSS to be submitted when adding an event (reported by WordFence Security Team)

= 6.4.7.1 =
* Fixed JS error preventing customizing columns in bookings admin table

= 6.4.7 =
* Fixed PHP warning on my-bookings page.
* Fixed ticket selection dropdown not showing max spaces if `EM_Bookings::$disable_restrictions` is enabled (such as for manual bookings).
* Fixed export and view setting overlays not working for booking admins table front-end after filtering once or more.
* Fixed issues with category/tag selection in Firefox.
* Added responsive options to the search form, allowing search form fields to stack on smaller screen sizes and also choose which main search options are hidden.
* Fixed localized times via #_EVENTDATES_LOCAL not working in AJAX calls such as searches or pagination
* Fixed cache PHP error in taxonomy objects if accessing a blank taxonomy
* Fixed 2 vulnerabilities, reported by PatchStack and WordFence
* added alternative dropdown view/flow for multiple selections on search form options
* updated selectize.js to 0.15.2
* fixed JS error preventing admin booking column selection in 6.4.7
* fixed issues when clearing search criteria on search form
* added support for counting search criteria using custom fields in search form
* added multiple actions for search form templates
* fixed advanced trigger not showing in modal mode if hidden inline mode settings are set to hide trigger
* added escaping for cookie-set EM notices for security hardening
* added url escaping (security precaution) for pagination links
* made improvements to ical permalinks to account for more complex permalink structures (such as date-based),
* added em_ical_output_content_summary and em_ical_output_content_location filters

= 6.4.6.4 =
* Fixed issues with multiple bookings and anonymous bookings that could rewrite the user name of the account making the booking with the latest user information.
* Fixed setting `is_available` to `EM_Ticket` not having any effect due to protected visibility, which can cause unpredictable behavior.
* Fixed form `.em-ajax-form` not outputting the correct notice box on success.
* Added JS for `em-cancel` button to include custom data into AJAX via the `data-` attribute.
* Added `em_my_bookings_booking_action_links` filter allowing for array insertion of action links for my booking page bookings.

= 6.4.6.3 =
* Fixed telephone field auto-enabling and ignoring EM_PHONE_INTL_ENABLED constant introduced in 6.4.6

= 6.4.6.2 =
* Fixed booking summary showing for free events despite setting set to no.
* Fixed edge cases where bookings do not get deleted properly due to unloaded ticket data.
* Fixed various PHP warnings.

= 6.4.6.1 =
* Fixed array meta key retrieval and saving issues introduced in version 6.4.6.
* Fixed first-time installation PHP errors and errant update notices.
* Added `em_booking_form_js_fields_change_match` filter allowing for programmatic listening of custom field changes to update the booking form summary section.
* Fixed front-end display issues of bookings containing two ticket types.
* Fixed search form button not enabling after changing search params when advanced search is disabled.

= 6.4.6 =
* fixed fatal error caused by use of Pro function in settings page when Pro isn't activated, bug introduced in EM 6.4.5
* fixed XSS vulnerability, disclosure to be followed
* fixed minor security vulnerability allowing anauthorized logged-in users to dismiss EM admin notices
* fixed OAuth errors (such as with PayPa) due to implementations not requring a scope credential
* fixed PHP Warnings in event editor frontend,
* fixed EM Notices appearing twice on booking forms in backend for manual bookings in Pro,
* fixed datetime offset issue when migrating old EM versions without timezones
* fixed file loading issues in some server edge case environments by providing absolute include paths in events-manager.php
* fixed multiple PHP Deprecated dynamic variables warning for PHP 8.2 by adding a $fields_shortcuts static map for short variable names, as well as storing unknown 'dynamic' variables into a dynamic_variables protected property array via __set() and __get() in EM_Object
* changed preference for $shortnames map of field shortcuts for $fields_shortcuts although still supported in EM_Object but preferable to use one static storage for performance improvements
* fixed issues with storage of booking and attendee/spaces meta data
* developers should be aware that storage of array meta data going forwards takes the format of _key|subkey for associative or _key| for sequential arrays, if you stored array keys with an underscore, or subkeys with underscores, please review EM_Object::process_meta() and EM_Booking::process_meta() and the function comments for a better understanding of what to do, as a future update may involve an SQL migration script to migrate old meta fields into the new format
* added sorting option on event search forms - enabled by default on first install, disabled for existing sites updating,
* added international phone number picker - BETA - requires activation via define('EM_PHONE_INTL_ENABLED', true); in wp-config
* fixed PHP 8.2 warnings in tickets
* fixed ordering issues in attendee booking editor displays
* added ical output filters em_ical_output_content_description and em_ical_event_output_content

= 6.4.5 =
* added RSVP functionality (re-confirming a booking)
* add uncancel option so users can undo a cancellation if spaces still available
* switched my bookings page action links to a button dropdown rather than loose links
* fixed class static binding issues with EM_OAuth libraries causing problems in Zoom-enabled bookings
* fixed tippy dropdown button width issues

= 6.4.4 =
* fixed session wakeup issues for the EM_Booking object due to recent atomic tickets update,
* fixed EM_Tickets_Bookings and EM_Ticket_Booking possibly returning erroneous booking property
* added JS booking form helper functions em_booking_form_unhide_success, em_booking_form_enable_button, em_booking_form_disable_button,
* added backwards compatibility for booking ajax responses including the 'result' property rather than the new 'success' property
* fixed calendar navigation issues showing default calendar size according to responsive sizing even when using forced calendar_size="large"
* fixed advanced filters button in calendar not working when search forms disabled in settings page
* moved advanced search trigger button html/php into separate template
* fixed view of calendar changing to default events list format if default search forms disabled in settings
* fixed error messages when updating Events Manager
* moved default view setting to events formatting section, outside of search form options
* fixed booking forms not always auto-hiding after submission is complete,
* fixed scrolling overflow issues for skeleton loaders on booking form

For changelog of 6.4.3 and lower, see the [earlier reamde.txt](https://plugins.svn.wordpress.org/events-manager/tags/6.4.3/readme.txt).