<?php

namespace src\controllers;

use Exception;
use src\core\Request;
use src\core\Response;
use src\exceptions\BadRequestHttpException;
use src\services\UserService;
use src\utils\UserSession;
use src\utils\Validator;

class CompanyController extends Controller
{
    // Dependency injection
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
        $linkTag = <<<HTML
                <link rel="stylesheet" href="/styles/company/profile.css" />
            HTML;
        $additionalTags = [$linkTag];
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        if ($req->getMethod() == "GET") {
            // GET
            // Get initial data
            $companyDetail = $this->userService->getCompanyProfile($currentUserId);
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

                $this->userService->updateCompanyProfile($currentUserId, $name, $location, $about);
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
