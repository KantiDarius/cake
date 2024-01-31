<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'view']);
    }

    public function viewClasses(): array
    {
        return [JsonView::class];
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
            'group' => ['Articles.id'],
            'fields' => [
                'Articles.id',
                'Articles.title',
                'Articles.body',
                'Articles.created_at',
                'Articles.updated_at',
                'Users.id',
                'Users.email',
                'Users.created_at',
                'Users.updated_at',
                'likes' => $this->Articles->LikeArticles->find()->func()->count('like_articles.id')
            ],
            'join' => [
                'like_articles' => [
                    'table' => 'like_articles',
                    'type' => 'LEFT',
                    'conditions' => 'Articles.id = like_articles.article_id'
                ]
            ],
            'order' => [
                'Articles.created_at' => 'DESC'
            ]
        ];

        $request = $this->request->getQueryParams();
        if (@$request['title']) {
            $this->paginate['conditions']['Articles.title LIKE '] = '%' . $request['title'] . '%';
        }
        if (@$request['limit']) {
            $this->paginate['limit'] = (int) $request['limit'];
        }

        $articles = $this->paginate($this->Articles);
        $paging = current($this->request->getAttribute('paging'));

        $this->set(compact('articles', 'paging'));
        $this->viewBuilder()->setOption('serialize', ['articles', 'paging']);
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $article = $this->Articles->find()
            ->select([
                'Articles.id',
                'Articles.title',
                'Articles.body',
                'Articles.created_at',
                'Articles.updated_at',
                'Users.id',
                'Users.email',
                'Users.created_at',
                'Users.updated_at',
                'likes' => $this->Articles->LikeArticles->find()->func()->count('LikeArticles.id')
            ])
            ->contain(['Users'])
            ->leftJoinWith('LikeArticles')
            ->where(['Articles.id' => $id])
            ->group(['Articles.id'])
            ->first();

        $this->set(compact('article'));
        $this->viewBuilder()->setOption('serialize', ['article']);
    }

    public function like($id = null)
    {
        $this->request->allowMethod('post');
        $userId = $this->Authentication->getIdentityData('id');
        if (!$userId) {
            throw new UnauthorizedException(__('Invalid user'));
        }
        $likeTable = TableRegistry::getTableLocator()->get('LikeArticles');
        $like = $likeTable->find()
            ->where([
                'user_id' => $userId,
                'article_id' => $id
            ])
            ->first();
        if (!$like) {
            $like = $likeTable->newEntity([
                'user_id' => $userId,
                'article_id' => $id
            ]);
            if (!$likeTable->save($like)) {
                $message = 'Like false!';
                $this->set(compact('message'));
                $this->viewBuilder()->setOption('serialize', ['message']);
                return ;
            }
        }
        return $this->view($id);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $userId = $this->Authentication->getIdentityData('id');
        if (!$userId) {
            throw new UnauthorizedException(__('Invalid user'));
        }
        $article = $this->Articles->newEmptyEntity();
        $message = __('The article could not be saved. Please, try again.');
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            $article->user_id = $userId;

            if ($article->hasErrors()) {
                $errors = $article->getErrors();
                $this->set(compact('message', 'errors'));
                $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
                return;
            }
            if ($this->Articles->save($article)) {
                $message = __('The article has been saved.');
            }
        }
        $this->set(compact('article', 'message'));
        $this->viewBuilder()->setOption('serialize', ['article', 'message']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->request->allowMethod('put');
        $userId = $this->Authentication->getIdentityData('id');
        if (!$userId) {
            throw new UnauthorizedException(__('Invalid user'));
        }
        $article = $this->Articles->get($id);
        if ($article->user_id != $userId) {
            throw new UnauthorizedException(__("Updating other people's article is not allowed!"));
        }

        $data = $this->request->getData();
        $data = array_diff_key($data, ['user_id']);
        $article = $this->Articles->patchEntity($article, $data);
        $message = __('The article could not be saved. Please, try again.');

        if ($article->hasErrors()) {
            $errors = $article->getErrors();
            $this->set(compact('message', 'errors'));
            $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
            return;
        }

        if ($this->Articles->save($article)) {
            $message = __('The article has been saved.');
        }
        $this->set(compact('article', 'article', 'message'));
        $this->viewBuilder()->setOption('serialize', ['article', 'message']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod('delete');
        $userId = $this->Authentication->getIdentityData('id');
        if (!$userId) {
            throw new UnauthorizedException(__('Invalid user'));
        }
        $article = $this->Articles->get($id);
        if ($article->user_id != $userId) {
            throw new UnauthorizedException(__("Delete other people's article is not allowed!"));
        }

        $message = __('The article could not be deleted. Please, try again.');
        if ($this->Articles->delete($article)) {
            $message = __('The article has been deleted.');
        }
        $this->set(compact('article', 'article', 'message'));
        $this->viewBuilder()->setOption('serialize', ['article', 'message']);
    }
}
