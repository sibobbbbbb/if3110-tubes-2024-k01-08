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
use src\services\{CompanyService, UserService};
use src\utils\{UserSession, Validator};
use src\views\components\PaginationComponent;

class CompanyController extends Controller
{
    // Dependency injection
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Render and handle the create job page (GET & POST)
     * I.S. user authenticated & authorized as company (from middleware)
     * F.S. render the create lowongan page
     */
    public function renderAndHandleCreateJob(Request $req, Response $res): void
    {
        $viewPathFromPages = 'company/jobs/create/index.php';
        $currentUserId = UserSession::getUserId();

        // Base data to pass to the view
        $title = 'LinkInPurry | Create Job';
        $description = 'Create a new job';
        $additionalTags = <<<HTML
                <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js" defer></script>
                <script src="/scripts/quill-editor.js" defer></script>
                <script src="/scripts/company/create-job-form.js" defer></script>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
                <link rel="stylesheet" href="/styles/company/job-form.css" />                
                <link rel="stylesheet" href="/styles/quill-editor.css">
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        if ($req->getMethod() == "GET") {
            // GET
            // render
            $this->renderPage($viewPathFromPages, $data);
        } else if ($req->getMethod() == "POST") {
            // POST

            // Validate request body
            $rules = [
                'position' => ['required', "max" => 128],
                'description' => ['required', "max" => 2048],
                'job-type' => ['required', "enum" => JobType::getValues()],
                'location-type' => ['required', "enum" => LocationType::getValues()],
                'attachments' => ['requiredFile', "files" => ['maxSize' => 5 * 1024 * 1024, 'allowedTypes' => ['image/jpeg', 'image/png']]]
            ];

            $validator = new Validator();
            $isValid = $validator->validate($req->getBody(), $rules);
            if (!$isValid) {
                $data['errorFields'] = $validator->getErrorFields();
                $data['fields'] = $req->getBody();
                $this->renderPage($viewPathFromPages, $data);
            }

            // Create job
            try {
                $position = $req->getBody()['position'];
                $description = $req->getBody()['description'];
                $jobType = JobType::fromString($req->getBody()['job-type']);
                $locationType = LocationType::fromString($req->getBody()['location-type']);
                $attachments = $req->getBody()['attachments'];

                $this->companyService->createJob($currentUserId, $position, $description, $jobType, $locationType, $attachments);
            } catch (HttpExceptionFactory $e) {
                // TODO: Render error view
                throw $e;
                return;
            } catch (Exception $e) {
                // TODO: Render Internal server error
                throw $e;
                return;
            }

            // Redirect to company jobs list page
            $res->redirect('/company/jobs');
        }
    }

    /**
     * Render and handle the company jobs list page
     * I.S. user authenticated & authorized as company (from middleware)
     * F.S. render the company jobs list page
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
    public function renderCompanyJobs(Request $req, Response $res): void
    {
        $viewPathFromPages = 'company/jobs/index.php';
        $currentUserId = UserSession::getUserId();

        // Base data to pass to the view
        $title = 'LinkInPurry | Company Jobs';
        $description = 'List of jobs posted by your company';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/company/job-list.css" />
                <script src="/scripts/company/job-list.js" defer></script>
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
        $queryParams = $this->parseCompanyJobQueryParams($rawIsOpens, $rawJobTypes, $rawLocationTypes, $rawCreatedAtFrom, $rawCreatedAtTo, $rawSearch, $rawSortCreatedAt, $rawPage);
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
            [$jobs, $meta] = $this->companyService->getCompanyJobs($currentUserId, $parsedIsOpens, $parsedJobTypes, $parsedLocationTypes, $parsedCreatedAtFrom, $parsedCreatedAtTo, $parsedSearch, $parsedSortCreatedAt, $parsedPage);

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

    private function parseCompanyJobQueryParams(
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

        // Is opem
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

    /**
     * Render and handle the edit job page (GET & POST)
     *  Note: PUT is not a valid HTML form method (https://stackoverflow.com/questions/8054165/using-put-method-in-html-form)
     * I.S. user authenticated & authorized as company (from middleware)
     * F.S. render the edit job page
     */
    public function renderAndHandleEditJob(Request $req, Response $res): void
    {
        $viewPathFromPages = 'company/jobs/[jobId]/edit/index.php';
        $currentUserId = UserSession::getUserId();
        $currentJobId = $req->getPathParams("jobId");

        // Base data to pass to the view
        $title = 'LinkInPurry | Edit Job';
        $description = 'Edit job';
        $additionalTags = <<<HTML
                <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js" defer></script>
                <script src="/scripts/quill-editor.js" defer></script>
                <script src="/scripts/company/edit-job-form.js" defer></script>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
                <link rel="stylesheet" href="/styles/company/job-form.css" />                
                <link rel="stylesheet" href="/styles/quill-editor.css">
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags,
            'currentJobId' => $currentJobId
        ];

        // Get initial data
        $jobDetail = $this->companyService->getJobDetail($currentJobId);

        if ($req->getMethod() == "GET") {
            // GET
            $data['fields'] = [
                'position' => $jobDetail->getPosition(),
                'description' => $jobDetail->getDescription(),
                'is-open' => $jobDetail->getIsOpen(),
                'job-type' => $jobDetail->getJobType()->value,
                'location-type' => $jobDetail->getLocationType()->value,
                'attachments' => $jobDetail->getAttachments(),
            ];

            // Render
            $this->renderPage($viewPathFromPages, $data);
        } else if ($req->getMethod() == "POST") {
            // POST
            // Validate request body
            $rules = [
                'position' => ['required', "max" => 128],
                'description' => ['required', "max" => 2048],
                'is-open' => ['required', 'boolean'],
                'job-type' => ['required', "enum" => JobType::getValues()],
                'location-type' => ['required', "enum" => LocationType::getValues()],
                'attachments' => ['optional', "files" => ['maxSize' => 5 * 1024 * 1024, 'allowedTypes' => ['image/jpeg', 'image/png']]]
            ];

            $validator = new Validator();
            $isValid = $validator->validate($req->getBody(), $rules);
            if (!$isValid) {
                $data['errorFields'] = $validator->getErrorFields();
                // Keep user input for position, description, job-type, location-type
                $data['fields'] = $req->getBody();
                $data['fields']['attachments'] = $jobDetail->getAttachments();

                $this->renderPage($viewPathFromPages, $data);
            }

            // Edit job
            try {
                $position = $req->getBody()['position'];
                $description = $req->getBody()['description'];
                $isOpen = (bool)$req->getBody()['is-open'];
                $jobType = JobType::fromString($req->getBody()['job-type']);
                $locationType = LocationType::fromString($req->getBody()['location-type']);
                $attachments = $req->getBody()['attachments'];

                $this->companyService->editJob($currentUserId, $currentJobId, $position, $description, $isOpen, $jobType, $locationType, $attachments);
            } catch (HttpExceptionFactory $e) {
                // TODO: Render error view
                throw $e;
                return;
            } catch (Exception $e) {
                // TODO: Render Internal server error
                throw $e;
                return;
            }

            // Reset form state (redirect to the same page)
            $res->redirect("/company/jobs/$currentJobId/edit");
        }
    }


    /**
     * Delete a job
     */
    public function handleDeleteJob(Request $req, Response $res): void {}


    /**
     * Handle delete job attachment
     * company/jobs/attachment/[attachmentId]
     */
    public function handleDeleteJobAttachment(Request $req, Response $res): void
    {
        error_log("Delete job attachment");
        $attachmentId = $req->getPathParams('attachmentId');
        $currentUserId = UserSession::getUserId();

        // Delete attachment
        try {
            $this->companyService->deleteJobAttachment($currentUserId, $attachmentId);
        } catch (BaseHttpException $e) {
            // Http exception
            $responseDto = DtoFactory::createErrorDto($e->getMessage());
            $res->json($e->getCode(), $responseDto);
        } catch (Exception $e) {
            // Treat as internal server error
            $responseDto = DtoFactory::createErrorDto("An error occurred while deleting job attachment");
            $res->json(500, $responseDto);
        }

        // Success
        $responseDto = DtoFactory::createSuccessDto("Job attachment deleted successfully");
        $res->json(200, $responseDto);
    }


    /**
     * Render and handle the update company page (GET & POST)
     * Note: PUT is not a valid HTML form method (https://stackoverflow.com/questions/8054165/using-put-method-in-html-form)
     * I.S. user authenticated & authorized as company (from middleware)
     * F.S. render the update company page
     */
    public function renderAndHandleUpdateCompany(Request $req, Response $res): void
    {
        $viewPathFromPages = 'company/profile/index.php';
        $currentUserId = UserSession::getUserId();

        // Base data to pass to the view
        $title = 'LinkInPurry | Company Profile';
        $description = 'Update your company profile';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/company/profile.css" />
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        if ($req->getMethod() == "GET") {
            // GET
            // Get initial data
            $companyDetail = $this->companyService->getCompanyProfile($currentUserId);
            $data['fields'] = [
                'name' => $companyDetail->getName(),
                'location' => $companyDetail->getLocation(),
                'about' => $companyDetail->getAbout()
            ];

            // render
            $this->renderPage($viewPathFromPages, $data);
        } else if ($req->getMethod() == "POST") {
            // PUT
            // Validate request body
            $rules = [
                'name' => ['required', "max" => 128],
                'about' => ['required', "max" => 512],
                'location' => ['required', "max" => 128],
            ];
            $validator = new Validator();
            $isValid = $validator->validate($req->getBody(), $rules);
            if (!$isValid) {
                $data['errorFields'] = $validator->getErrorFields();
                $data['fields'] = $req->getBody();
                $this->renderPage($viewPathFromPages, $data);
            }

            // Update company profile
            try {
                $name = $req->getBody()['name'];
                $location = $req->getBody()['location'];
                $about = $req->getBody()['about'];

                $this->companyService->updateCompanyProfile($currentUserId, $name, $location, $about);
            } catch (BadRequestHttpException $e) {
                // Invalid update
                $message = $e->getMessage();
                $data['errorFields'] = [
                    'name' => [$message],
                    'about' => [$message],
                    'location' => [$message],
                ];
                $data['fields'] = $req->getBody();
                $this->renderPage($viewPathFromPages, $data);
            } catch (Exception $e) {
                // Internal server error
                // TODO: render error view
            }

            // Success
            // Reset form state (redirect to the same page)
            $res->redirect('/company/profile');
        }
    }
}
