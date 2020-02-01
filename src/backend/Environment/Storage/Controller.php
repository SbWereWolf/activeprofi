<?php

namespace Environment\Storage;


use BusinessLogic\Generator\Manager;
use DataStorage\Basis\DataSource;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

class Controller extends \Environment\Basis\Controller
{
    private $install = '';
    private $unmount = '';

    const INSTALL_SQLITE = "
CREATE TABLE task
(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    date INTEGER NOT NULL,
    author TEXT NOT NULL,
    status TEXT NOT NULL,
    description TEXT
);
CREATE INDEX task_title_index ON task (title);
CREATE INDEX task_description_index ON task (description);
CREATE INDEX task_author_index ON task (author);
    ";
    const UNMOUNT_SQLITE = '
DROP TABLE IF EXISTS task ;
DROP INDEX IF EXISTS task_title_index;
DROP INDEX IF EXISTS task_description_index;
DROP INDEX IF EXISTS task_author_index;

VACUUM;
';

    public function __construct(Request $request, Response $response, array $parametersInPath, DataSource $dataPath)
    {
        parent::__construct($request, $response, $parametersInPath, $dataPath);

        switch (DBMS) {
            case SQLITE:
                $this->setInstall(self::INSTALL_SQLITE);
                $this->setUnmount(self::UNMOUNT_SQLITE);
                break;
        }
    }

    public function process(): Response
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $isPost = $request->isPost();
        if ($isPost) {
            $response = $this->create();
        }
        $isDelete = $request->isDelete();
        if ($isDelete) {
            $response = $this->delete();
        }

        return $response;
    }

    private function create(): Response
    {
        $response = $this->executeCommand($this->getInstall());
        $status = $response->getStatusCode();

        $isSuccess = ($status == StatusCode::HTTP_CREATED);
        if($isSuccess){
            $isSuccess = (new Manager($this->getDataPath()))->process();
        }

        if(!$isSuccess){
            $response = $response->withStatus(StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    private function executeCommand($requestText):Response
    {
        $dataSource = $this->getDataPath();
        $db = new \PDO(
            $dataSource->getDsn(),
            $dataSource->getUsername(),
            $dataSource->getPasswd(),
            $dataSource->getOptions());

        $isSuccess = $db->exec($requestText) !== false;

        $isDelete = $this->getRequest()->isDelete();
        $isPost = $this->getRequest()->isPost();
        $response = $this->getResponse();
        if ($isSuccess && $isPost) {
            $response = $response->withStatus(StatusCode::HTTP_CREATED);
        }
        if ($isSuccess && $isDelete) {
            $response = $response->withStatus(StatusCode::HTTP_NO_CONTENT);
        }
        if (!$isSuccess) {
            $response = $response->withStatus(StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    private function delete(): Response
    {
        $response = $this->executeCommand($this->getUnmount());

        return $response;
    }

    private function setInstall(string $install): Controller
    {
        $this->install = $install;
        return $this;
    }

    private function getInstall(): string
    {
        return $this->install;
    }

    private function setUnmount(string $unmount): Controller
    {
        $this->unmount = $unmount;
        return $this;
    }

    private function getUnmount(): string
    {
        return $this->unmount;
    }
}
