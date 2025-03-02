<?php

namespace App\Command;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Todo;
use Pimcore\Model\DataObject\User;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportTodosCommand extends AbstractCommand
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
    }

    protected function configure()
    {
        $this
            ->setName('command:import-todos')
            ->setDescription('Importuje zadania z adresu https://jsonplaceholder.typicode.com/todos');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $response = $this->httpClient->request('GET', 'https://jsonplaceholder.typicode.com/todos');

            $this->parseTodos(json_decode($response->getContent(), true));
            $output->writeln('<info>Import zakończony</info>');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<info>Wystąpił błąd z pobraniem zawartości</info>');
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return self::FAILURE;
        }
    }

    /**
     * @param array $todoContent
     * @return void
     * @throws \Exception
     */
    private function parseTodos(array $todoContent): void
    {
        foreach ($todoContent as $data) {
            // Sprawdź, czy istnieje folder 'Users'
            $parent = Folder::getByPath('/Users');

            if (!$parent) {
                $parent = new Folder();
                $parent->setKey('Users');
                $parent->setParentId(1); // umieść w katalogu głównym
                $parent->save();
            }

            // Pobierz użytkownika po ścieżce
            $user = User::getByPath('/Users/user_' . $data['userId']);

            if (!$user) {
                $user = new User();
                $user->setKey('user_' . $data['userId']);
                $user->setParentId($parent->getId()); // ID folderu 'Users'

                $user->setUserId($data['userId']);
                $user->save();
            }

            // Sprawdź i utwórz folder 'Todos'
            $todoFolder = Folder::getByPath('/Todo');
            if (!$todoFolder) {
                $todoFolder = new Folder();
                $todoFolder->setKey('Todo');
                $todoFolder->setParentId(1);
                $todoFolder->save();
            }

            // Pobierz lub utwórz zadanie
            $todo = Todo::getByPath('/Todo/todo_' . $data['id']);
            if (!$todo) {
                $todo = new Todo();
                $todo->setKey('todo_' . $data['id']);
                $todo->setParent($todoFolder);
            }

            $todo->setTitle($data['title']);
            $todo->setCompleted($data['completed']);
            $user->setUserId($user->getId());
            $todo->save();
        }
    }
}
