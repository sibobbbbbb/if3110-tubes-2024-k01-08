<?php

namespace src\controllers;

use DateTime;
use Exception;
use src\core\{Response, Request};
use src\dao\{LocationType, JobType};
use src\dto\DtoFactory;
use src\exceptions\BadRequestHttpException;
use src\exceptions\BaseHttpException;
use src\exceptions\HttpExceptionFactory;
use src\services\{JobService, UserService};
use src\utils\{UserSession, Validator};
use src\views\components\PaginationComponent;

class JobController extends Controller
{
    private JobService $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * Render and handle the jobs list page for job-seeker
     * I.S. user authenticated & authorized as JOBSEEKER (from middleware)
     * F.S. render the jobs list page
     * sorts (ony one at a time): sortCreatedAtAsc (default false)
     * filters: (no filter => all jobs)
     *  - open/closed (key=is-open, value=true/false)
     *  - job type (key=job-types, value=enum)
     *  - location type (key=location-types, value=enum)
     *  - daterange (key=created-at-from, value=date) & (key=created-at-to, value=timestamp)
     *  - search bar (position, location, job type) (key=search, value=string)
     *  - pagination (key=page, value=integer) (note: limit is fixed to 20)
     *  Invalid query parameters will be ignored (no error response)
     */
    public function renderJobs(Request $req, Response $res): void
    {
        $viewPathFromPages = 'jobs/index.php';

        // Base data to pass to the view
        $title = 'LinkInPurry | Jobs';
        $description = 'List of jobs available for you';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/jobs/job-list.css" />
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        // Get query parameters
        $rawIsOpens = $req->getQueryParams('is-opens');
        $rawJobTypes = $req->getQueryParams('job-types');
        $rawLocationTypes = $req->getQueryParams('location-types');
        $rawCreatedAtFrom = $req->getQueryParams('created-at-from');
        $rawCreatedAtTo = $req->getQueryParams('created-at-to');
        $rawSearch = $req->getQueryParams('search');
        $rawSortCreatedAt = $req->getQueryParams('sort-created-at');
        $rawPage = $req->getQueryParams('page');

        // Parse query parameters
        $queryParams = $this->parseJobQueryParams($rawIsOpens, $rawJobTypes, $rawLocationTypes, $rawCreatedAtFrom, $rawCreatedAtTo, $rawSearch, $rawSortCreatedAt, $rawPage);
        $parsedIsOpens = $queryParams['is-opens'];
        $parsedJobTypes = $queryParams['job-types'];
        $parsedLocationTypes = $queryParams['location-types'];
        $parsedCreatedAtFrom = $queryParams['created-at-from'];
        $parsedCreatedAtTo = $queryParams['created-at-to'];
        $parsedSearch = $queryParams['search'];
        $parsedSortCreatedAt = $queryParams['is-created-at-asc'];
        $parsedPage = $queryParams['page'];

        try {
            // Get jobs data
            [$jobs, $meta] = $this->jobService->getJobs($parsedIsOpens, $parsedJobTypes, $parsedLocationTypes, $parsedCreatedAtFrom, $parsedCreatedAtTo, $parsedSearch, $parsedSortCreatedAt, $parsedPage);

            // Generate pagination component
            $paginationComponent = PaginationComponent::renderPagination($meta, $req->getUri());

            // Add data to pass to the view
            $data['jobs'] = $jobs;
            $data['meta'] = $meta;
            $data['filters'] = $queryParams;
            $data['paginationComponent'] = $paginationComponent;
        } catch (BaseHttpException $e) {
            // TODO: Render error view
            echo $e->getMessage();
        } catch (Exception $e) {
            // TODO: Render Internal server error
            echo $e->getMessage();
        }

        $this->renderPage($viewPathFromPages, $data);
    }

    /**
     * Parse job page query params
     */
    private function parseJobQueryParams(
        ?array $rawIsOpens,
        ?array $rawJobTypes,
        ?array $rawLocationTypes,
        ?string $rawCreatedAtFrom,
        ?string $rawCreatedAtTo,
        ?string $rawSearch,
        ?string $rawSortCreatedAt,
        ?string $rawPage
    ): array {
        $isCreatedAtAsc = false; // Default sort
        $isOpens = $jobTypes = $locationTypes = $createdAtFrom = $createdAtTo = $search = $page = null;

        // Is open
        if ($rawIsOpens !== null) {
            foreach ($rawIsOpens as $isOpenValue) {
                if ($isOpenValue === "true") {
                    if ($isOpens === null) {
                        $isOpens = [];
                    }
                    $isOpens[] = true;
                } else if ($isOpenValue === "false") {
                    if ($isOpens === null) {
                        $isOpens = [];
                    }
                    $isOpens[] = false;
                }
            }
        }

        // Job types
        if ($rawJobTypes !== null) {
            foreach ($rawJobTypes as $jobTypeValue) {
                if (in_array($jobTypeValue, JobType::getValues())) {
                    if ($jobTypes === null) {
                        $jobTypes = [];
                    }
                    $jobTypes[] = JobType::fromString($jobTypeValue);
                }
            }
        }

        // Location types
        if ($rawLocationTypes !== null) {
            foreach ($rawLocationTypes as $locationTypeValue) {
                if (in_array($locationTypeValue, LocationType::getValues())) {
                    if ($locationTypes === null) {
                        $locationTypes = [];
                    }
                    $locationTypes[] = LocationType::fromString($locationTypeValue);
                }
            }
        }

        // Date formatiing:
        // yyyy-mm-dd => date
        // Created at from
        if ($rawCreatedAtFrom !== null) {
            $createdAtFromDateTime = DateTime::createFromFormat('Y-m-d', $rawCreatedAtFrom);
            if ($createdAtFromDateTime && $createdAtFromDateTime->format('Y-m-d') === $rawCreatedAtFrom) {
                $createdAtFrom = $createdAtFromDateTime;
            }
        }

        // Created at to
        if ($rawCreatedAtTo !== null) {
            $createdAtToDateTime = DateTime::createFromFormat('Y-m-d', $rawCreatedAtTo);
            if ($createdAtToDateTime && $createdAtToDateTime->format('Y-m-d') === $rawCreatedAtTo) {
                $createdAtTo = $createdAtToDateTime;
            }
        }

        // Search
        if ($rawSearch !== null && strlen($rawSearch) > 0) {
            $search = $rawSearch;
        }

        // Created at sort
        if ($rawSortCreatedAt !== null && $rawSortCreatedAt === "oldest-first") {
            $isCreatedAtAsc = true;
        }

        // Page number (default to 1)
        $page = 1;
        if ($rawPage !== null) {
            // Parse to integer
            $pageResult = filter_var($rawPage, FILTER_VALIDATE_INT);
            if ($pageResult == false || $pageResult < 1) {
                $page = 1;
            } else {
                $page = $pageResult;
            }
        }

        return [
            'is-opens' => $isOpens,
            'job-types' => $jobTypes,
            'location-types' => $locationTypes,
            'created-at-from' => $createdAtFrom,
            'created-at-to' => $createdAtTo,
            'search' => $search,
            'is-created-at-asc' => $isCreatedAtAsc,
            'page' => $page
        ];
    }

    public function renderandHandleHistory(Request $req, Response $res): void
    {
        // // Redirect if user is authenticated
        // $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'history/index.php';

        // Data to pass to the view (SSR)
        $title = 'LinkInPurry | Sign In';
        $description = 'Sign in to your LinkInPurry account';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/history/history.css" />
                <script src="/scripts/auth/sign-up/job-seeker.js" defer></script>
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        if ($req->getMethod() == "GET") {
            // Get
            $this->renderPage($viewPathFromPages, $data);
        } else {
        }
    }
}
