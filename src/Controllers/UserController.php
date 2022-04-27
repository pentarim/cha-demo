<?php declare(strict_types = 1);

namespace App\Controllers;

use App\Managers\UserManager;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends BaseController
{
    /**
     * UserController constructor.
     *
     * @param ResponseInterface $response
     * @param UserManager $userManager
     */
    public function __construct(protected ResponseInterface $response, private UserManager $userManager)
    {
        parent::__construct($this->response);
    }

    /**
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    public function createAction(ServerRequest $request): ResponseInterface
    {
        $input = $request->getParsedBody();

        $validationErrors = $this->validateAttributes($input);

        if (!empty($validationErrors)) {
            return $this->getResponse(400, \implode("\n", $validationErrors));
        }

        $attributes = [
            'name' => $input['name'],
            'yearOfBirth' => (int)$input['yearOfBirth'],
        ];

        try {
            $userId = $this->userManager->create($attributes);

            if (is_null($userId)) {
                return $this->getResponse(500, 'Something went really wrong, user not created.');
            }

            return $this->getResponse(201, "New user with id '$userId' has been created.");
        } catch (\Exception $e) {
            return $this->getResponse(500, 'Something went really wrong');
        }
    }

    /**
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    public function updateAction(ServerRequest $request): ResponseInterface
    {
        $input = $request->getParsedBody();
        $userId = (int)$request->getAttribute('id');

        $validationErrors = $this->validateAttributes($input);

        if (!empty($validationErrors)) {
            return $this->getResponse(400, \implode("\n", $validationErrors));
        }

        $attributes = [
            'name' => $input['name'],
            'yearOfBirth' => (int)$input['yearOfBirth'],
        ];

        try {
            $user = $this->userManager->get($userId);

            if (is_null($user)) {
                return $this->getResponse(400, 'User not found with given id.');
            }

            $success = $this->userManager->update($userId, $attributes);

            if (!$success) {
                return $this->getResponse(500, 'User not updated');
            }

            return $this->getResponse(200, "User has been updated!");
        } catch (\Exception $e) {
            return $this->getResponse(500, 'Something went really wrong');
        }
    }

    /**
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    public function showAction(ServerRequest $request): ResponseInterface
    {
        $userId = (int)$request->getAttribute('id');

        try {
            $user = $this->userManager->get($userId);

            if (is_null($user)) {
                return $this->getResponse(404, 'User not found.');
            }

            return $this->getResponse(200, "This is {$user['name']}, born in {$user['year_of_birth']}.");
        } catch (\Exception $e) {
            return $this->getResponse(500, 'Something went really wrong');
        }
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function validateAttributes(array $attributes): array
    {
        $validationErrors = [];

        if (!isset($attributes['name']) || strlen($attributes['name']) < 3) {
            $validationErrors['name'] = 'Invalid name, it should contain at least 3 characters.';
        } elseif (isset($attributes['name']) && !preg_match("/^([a-zA-Z' ]+)$/",$attributes['name'])) {
            $validationErrors['name'] = 'Invalid name, contains invalid characters, only letters and spaces are allowed.';
        }

        if (!isset($attributes['yearOfBirth']) || !is_numeric($attributes['yearOfBirth'])) {
            $validationErrors[] = 'Invalid yearOfBirth, should be an integer.';
        }

        return $validationErrors;
    }
}
