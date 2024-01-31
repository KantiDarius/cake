<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Validation\Validator;
use Cake\View\JsonView;
use Firebase\JWT\JWT;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'register']);
    }

    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    public function login() {
        $this->request->allowMethod('post');
        $result = $this->Authentication->getResult();

        if (!$result->isValid()) {
            $this->response = $this->response->withStatus(401);
            $message = __('Invalid User');

            $this->set(compact('message'));
            $this->viewBuilder()->setOption('serialize', ['message']);

            return;
        }

        $user = $result->getData();
        $privateKey = file_get_contents(CONFIG . '/jwt.key');
        $payload = [
            'sub' => $user->id,
            'exp' => time() + 60*60
        ];
        $token = JWT::encode($payload, $privateKey, 'RS256');
        $this->set(compact('user', 'token'));
        $this->viewBuilder()->setOption('serialize', ['user', 'token']);
    }

    public function register()
    {
        $this->request->allowMethod('post');
        $user = $this->Users->newEmptyEntity();
        $message = __('The user could not be saved. Please, try again.');
        $errors = [];
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($user->hasErrors()) {
                $errors = $user->getErrors();
            }
            if ($this->Users->save($user)) {
                $message = __('The user has been saved.');
            }
        }
        $this->set(compact('user', 'message', 'errors'));
        $this->viewBuilder()->setOption('serialize', ['user', 'message', 'errors']);
    }

    public function update() {
        $this->request->allowMethod('put');
        $userId = $this->Authentication->getIdentityData('id');
        if (!$userId) {
            throw new UnauthorizedException(__('Invalid user'));
        }
        $user = $this->Users->get($userId);

        $data = $this->request->getData();
        $data = array_diff_key($data, ['email', 'password']);
        $user = $this->Users->patchEntity($user, $data);

        if ($user->hasErrors()) {
            $message = 'Validate false!';
            $errors = $user->getErrors();
            $this->set(compact('errors', 'message'));
            $this->viewBuilder()->setOption('serialize', ['errors', 'message']);
            return;
        }

        if ($this->Users->save($user)) {
            $this->set(compact('user'));
            $this->viewBuilder()->setOption('serialize', ['user']);
        } else {
            throw new BadRequestException(__('Update failed'));
        }
    }

    public function changePassword() {
        $this->request->allowMethod('put');
        $userId = $this->Authentication->getIdentityData('id');
        if (!$userId) {
            throw new UnauthorizedException(__('Invalid user'));
        }
        $user = $this->Users->get($userId);

        $validator = new Validator();
        $validator
            ->requirePresence(['old_password', 'new_password', 'new_password_confirmation'])
            ->notEmptyString('old_password', __('Old password is required'))
            ->notEmptyString('new_password', __('New password is required'))
            ->notEmptyString('new_password_confirmation', __('New password confirmation is required'))
            ->add('new_password_confirmation', 'custom', [
                'rule' => function ($value, $context) {
                    return isset($context['data']['new_password']) && $value === $context['data']['new_password'];
                },
                'message' => __('Passwords do not match'),
            ])
            ->add('old_password', 'custom', [
                'rule' => function ($value) use($user) {
                    return $this->Users->checkPassword($user, $value);
                },
                'message' => __('Old password is incorrect'),
            ]);

        $errors = $validator->validate($this->request->getData());
        if ($errors) {
            $message = 'Validate false!';
            $this->set(compact('errors', 'message'));
            $this->viewBuilder()->setOption('serialize', ['errors', 'message']);
            return;
        }

        $newPassword = $this->request->getData('new_password');
        $user->password = $newPassword;
        if ($this->Users->save($user)) {
            $this->set(compact('user'));
            $this->viewBuilder()->setOption('serialize', ['user']);
        } else {
            throw new BadRequestException(__('Password change failed'));
        }
    }
}
