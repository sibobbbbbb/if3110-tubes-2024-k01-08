<?php

namespace src\controllers;

use DateTime;
use Exception;
use src\core\{Response, Request};
use src\dao\{LocationType, JobType, UserRole};
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
     * If user is authenticated and is company, redirect to company dashboard
     * I.S. user authenticated & authorized as JOBSEEKER (from middleware)
     * F.S. render the jobs list page
     * sorts (ony one at a time): sortCreatedAtAsc (default false)
     * filters: (no filter => all jobs)
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

        // If user has session and is company, redirect to company dashboard
        if (UserSession::isLoggedIn() && UserSession::getUserRole() == UserRole::COMPANY) {
            $res->redirect('/company/dashboard');
        }

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
        $rawJobTypes = $req->getQueryParams('job-types');
        $rawLocationTypes = $req->getQueryParams('location-types');
        $rawCreatedAtFrom = $req->getQueryParams('created-at-from');
        $rawCreatedAtTo = $req->getQueryParams('created-at-to');
        $rawSearch = $req->getQueryParams('search');
        $rawSortCreatedAt = $req->getQueryParams('sort-created-at');
        $rawPage = $req->getQueryParams('page');

        // Parse query parameters
        $queryParams = $this->parseJobQueryParams($rawJobTypes, $rawLocationTypes, $rawCreatedAtFrom, $rawCreatedAtTo, $rawSearch, $rawSortCreatedAt, $rawPage);
        $parsedJobTypes = $queryParams['job-types'];
        $parsedLocationTypes = $queryParams['location-types'];
        $parsedCreatedAtFrom = $queryParams['created-at-from'];
        $parsedCreatedAtTo = $queryParams['created-at-to'];
        $parsedSearch = $queryParams['search'];
        $parsedSortCreatedAt = $queryParams['is-created-at-asc'];
        $parsedPage = $queryParams['page'];

        try {
            // Get jobs data
            [$jobs, $meta] = $this->jobService->getJobs($parsedJobTypes, $parsedLocationTypes, $parsedCreatedAtFrom, $parsedCreatedAtTo, $parsedSearch, $parsedSortCreatedAt, $parsedPage);

            // Generate pagination component
            $paginationComponent = PaginationComponent::renderPagination($meta, $req->getUri());

            // Add data to pass to the view
            $data['jobs'] = $jobs;
            $data['meta'] = $meta;
            $data['filters'] = $queryParams;
            $data['paginationComponent'] = $paginationComponent;
        }  catch (Exception $e) {
            // TODO: Render Internal server error
            $dataError = [
                'statusCode' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
    
            $res->renderError($dataError);
        }

        $res->renderPage($viewPathFromPages, $data);
    }

    /**
     * Render job detail page for job-seeker
     * 
     * Case 1: Unauthenticated user, in header render login button
     * Case 2: Authenticated user, company -> redirect to company dashboard
     * Case 3: Authenticated user, jobseeker:
     * Case 3a: Haven't applied -> render apply button
     * Case 3b: Already applied -> render path to CV & Video
     * Case 3c: Job closed -> render not found page
     */
    public function renderJobDetail(Request $req, Response $res): void
    {
        $viewPathFromPages = 'jobs/[jobId]/index.php';
        $jobId = $req->getPathParams('jobId');
        $currentUserId = UserSession::getUserId();

        // this is a public route, but should only be accessible by non auth or jobseeker
        // If user has session and is company, redirect to company dashboard
        if (UserSession::isLoggedIn() && UserSession::getUserRole() == UserRole::COMPANY) {
            $res->redirect('/company/dashboard');
        }

        // Base data to pass to the view
        $title = 'LinkInPurry | Job Detail';
        $description = 'Job detail';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/jobs/job-detail.css" />
                <link rel="stylesheet" href="/styles/carousel.css" />
                <script src="/scripts/carousel.js" defer></script>
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        try {
            // Get data needed
            [$job, $application] = $this->jobService->getJobDetail($currentUserId, $jobId);

            // Add data to pass to the view
            $data['job'] = $job;
            $data['application'] = $application;
        }  catch (Exception $e) {
            // Render Internal server error
            $dataError = [
                'statusCode' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
    
            $res->renderError($dataError);
        }

        $res->renderPage($viewPathFromPages, $data);
    }


    /**
     * Parse job page query params
     */
    private function parseJobQueryParams(
        ?array $rawJobTypes,
        ?array $rawLocationTypes,
        ?string $rawCreatedAtFrom,
        ?string $rawCreatedAtTo,
        ?string $rawSearch,
        ?string $rawSortCreatedAt,
        ?string $rawPage
    ): array {
        $isCreatedAtAsc = false; // Default sort
        $jobTypes = $locationTypes = $createdAtFrom = $createdAtTo = $search = $page = null;

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
            'job-types' => $jobTypes,
            'location-types' => $locationTypes,
            'created-at-from' => $createdAtFrom,
            'created-at-to' => $createdAtTo,
            'search' => $search,
            'is-created-at-asc' => $isCreatedAtAsc,
            'page' => $page
        ];
    }

    public function renderApplicationsHistory(Request $req, Response $res): void
    {
        $viewPathFromPages = 'history/index.php';
        $currentUserId = UserSession::getUserId();

        // Data to pass to the view (SSR)
        $title = 'LinkInPurry | History';
        $description = 'Your job applications history';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/jobs/history.css" />
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        // Get query parameters
        $rawPage = $req->getQueryParams('page');

        // Parse query parameters
        $queryParams = $this->parseHistoryQueryParams($rawPage);
        $parsedPage = $queryParams['page'];

        try {
            // Call service to get applications history
            [$applications, $meta] = $this->jobService->getApplicationsHistory($currentUserId, $parsedPage);

            // Generate pagination component
            $paginationComponent = PaginationComponent::renderPagination($meta, $req->getUri());

            // Add data to pass to the view
            $data['applications'] = $applications;
            $data['meta'] = $meta;
            $data['paginationComponent'] = $paginationComponent;
        } catch (Exception $e) {
            // TODO: Render Internal server error
            $dataError = [
                'statusCode' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
    
            $res->renderError($dataError);
        }

        // Render the page
        $res->renderPage($viewPathFromPages, $data);
    }

    private function parseHistoryQueryParams(?string $rawPage)
    {
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
            'page' => $page
        ];
    }
}
