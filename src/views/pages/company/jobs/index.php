<div class="container">
    <!-- For filters & sorts -->
    <aside class="card--filter">
        <form method="GET" class="filter-form">
            <!-- Sort created at -->
            <div class="filter-form__section">
                <h2 class="filter-form__section-title">Sort by</h2>

                <div class="filter-form__group">
                    <label for="job-type" class="filter-form__label">
                        Created At
                    </label>

                    <div class="select-wrapper filter-form__select-wrapper">
                        <select class="custom-select filter-form__custom-select" name="job-type" aria-label="Select an option">
                            <option value="" selected disabled hidden>Select an option</option>
                            <option value="newest-first">Newest First</option>
                            <option value="oldest-first">Oldest First</option>
                        </select>

                        <svg class="custom-select__chevron-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Filters -->
            <div class="filter-form__section">
                <h2 class="filter-form__section-title">Filter by</h2>

                <div class="filter-form__groups">
                    <!-- Date range -->
                    <div class="filter-form__group">
                        <label for="created-at-from" class="filter-form__label">Date From</label>
                        <input type="date" value="" name="created-at-from" class="input filter-form__input--date">
                    </div>

                    <div class="filter-form__group">
                        <label for="created-at-to" class="filter-form__label">Date To</label>
                        <input type="date" value="" name="created-at-to" class="input filter-form__input--date">
                    </div>

                    <!-- Open / closed -->
                    <fieldset>
                        <legend class="filter-form__label">Status</legend>

                        <label class="checkbox-container">
                            <input name="is-open[]" type="checkbox" value="true">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Open</span>
                        </label>

                        <label class="checkbox-container">
                            <input name="is-open[]" type="checkbox" value="false">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Closed</span>
                        </label>
                        </legend>
                    </fieldset>

                    <!-- Job type -->
                    <fieldset>
                        <legend class="filter-form__label">Job Type</legend>

                        <label class="checkbox-container">
                            <input name="job-types[]" value="full-time" type="checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Full Time</span>
                        </label>

                        <label class="checkbox-container">
                            <input name="job-types[]" value="part-time" type="checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Part Time</span>
                        </label>

                        <label class="checkbox-container">
                            <input name="job-types[]" value="internship" type="checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Internship</span>
                        </label>
                    </fieldset>

                    <!-- Location Type -->
                    <fieldset>
                        <legend class="filter-form__label">Location Type</legend>

                        <label class="checkbox-container">
                            <input name="location-types[]" value="on-site" type="checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">On-Site</span>
                        </label>

                        <label class="checkbox-container">
                            <input name="location-types[]" value="hybrid" type="checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Hybrid</span>
                        </label>

                        <label class="checkbox-container">
                            <input name="location-types[]" value="remote" type="checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Remote</span>
                        </label>
                    </fieldset>
                </div>
            </div>

            <div class="filter-form__actions">
                <!-- Reset Filter -->
                <button type="button" class="button button--destructive button--icon filter-actions__button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter-x filter-actions__icon">
                        <path d="M13.013 3H2l8 9.46V19l4 2v-8.54l.9-1.055" />
                        <path d="m22 3-5 5" />
                        <path d="m17 3 5 5" />
                    </svg>
                </button>


                <!-- Submit -->
                <button type="submit" class="button button--default-color button--default-size filter-actions__button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search filter-actions__icon">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                    <span>
                        Apply
                    </span>
                </button>
            </div>
        </form>
    </aside>

    <!-- For job lists -->
    <main class="card--jobs">
        <!-- Title -->
        <h1 class="card__h1">
            Company Jobs
        </h1>

        <!-- Job lists -->
        <ul>
            <li>
                <article>

                </article>
            </li>
        </ul>

        <!-- Pagination -->
        <div>
            <button>Previous</button>
            <button></button>
            <button>Next</button>
        </div>
    </main>
</div>