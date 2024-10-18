<?php

namespace src\controllers;

use Exception;
use src\core\{Response, Request};
use src\dao\{LocationType, JobType};
use src\exceptions\BadRequestHttpException;
use src\exceptions\HttpExceptionFactory;
use src\services\{CompanyService, UserService};
use src\utils\{UserSession, Validator};

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
                <script src="/scripts/company/form-job.js" defer></script>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
                <link rel="stylesheet" href="/styles/company/form-job.css" />                
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
                'attachments' => ['requiredFile', "files" => ['maxSize' => 1024 * 1024, 'allowedTypes' => ['image/jpeg', 'image/png']]]
            ];

            $validator = new Validator();
            $isValid = $validator->validate($req->getBody(), $rules);
            if (!$isValid) {
                $data['errorFields'] = $validator->getErrorFields();
                $data['fields'] = $req->getBody();
                $this->renderPage($viewPathFromPages, $data);
                return;
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
                // Render error view
                throw $e;
                return;
            } catch (Exception $e) {
                // Internal server error
                throw $e;
                return;
            }

            // Redirect to company jobs list page
            $res->redirect('/company/jobs');
        }
    }

    /**
     * Render and handle the edit job page (GET & POST)
     *  Note: PUT is not a valid HTML form method (https://stackoverflow.com/questions/8054165/using-put-method-in-html-form)
     * I.S. user authenticated & authorized as company (from middleware)
     * F.S. render the edit job page
     */
    public function renderAndHandleEditJob(Request $req, Response $res): void {}


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
                return;
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
                return;
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
