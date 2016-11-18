<?php

namespace OCA\Owncollab\Controller;

use OCA\Owncollab\Helper;
use OCA\Owncollab\Db\Connect;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\Template;

class ApiController extends Controller
{

    /** @var string $userId
     * current auth user id */
    private $userId;

    /** @var string $userIdAPI
     * user id which accesses by API */
    private $userIdAPI;

    /** @var bool $isAdmin
     * true if current auth user consists into admin group */
    private $isAdmin;

    /** @var \OC_L10N $l10n
     * languages translations */
    private $l10n;

    /** @var Connect $connect
     * instance working with database */
    private $connect;
    /**
     * @var string $client
     */
    private $client;


    /**
     * ApiController constructor.
     * @param string $appName
     * @param IRequest $request
     * @param $userId
     * @param $isAdmin
     * @param \OC_L10N $l10n
     * @param Connect $connect
     */
    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        $isAdmin,
        $l10n,
        Connect $connect
    )
    {
        parent::__construct($appName, $request);

        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
        $this->l10n = $l10n;
        $this->connect = $connect;
        $this->client = 'client';
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index()
    {
        $key = Helper::post('key');
        $data = Helper::post('data', false);
        $this->userIdAPI = Helper::post('uid');

        if (method_exists($this, $key))
            return $this->$key($data);
        else
            return new DataResponse([
                'access' => 'deny',
                'errorinfo' => 'API method not exists',
            ]);
    }

    /**
     * @NoCSRFRequired
     * @param $data
     * @return DataResponse
     */
    public function saveall($data)
    {

        if (!$this->isAdmin) exit;

        if(empty($data['form']))
            return false;

        $data = json_decode($data['form'], true);
        $params = [
            'data' => $data,
        ];


        $project_data = $this->connect->project_data()->getData($this->client);

        if ($project_data) {
            $this->connect->project_data()->update($data, $this->client);
        } else {
            $this->connect->project_data()->insert($data, $this->client);
        }


        return new DataResponse($params);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    //TODO: Використовувати метод з застосуванням засобів безпеки
    public function getlogo()
    {


        $userfiles = \OCA\Files\Helper::getFiles('ProjectLogo');
        foreach ($userfiles as $f => $file) {
            $userfiles[$f] = \OCA\Files\Helper::formatFileInfo($file);
            $userfiles[$f]['mtime'] = $userfiles[$f]['mtime'] / 1000;
        }

        $params = [
            'user' => $this->userId,
            'files' => $userfiles,
            'error' => null,
            'errorinfo' => '',
        ];

        return new DataResponse($params);
    }

    public function logoBaseEncode()
    {
        $data = Helper::post('data', false);
        $src = $data['logo_src'];
        $params = [
            'src' => Helper::prevImg($src),
            'error' => null,
            'errorinfo' => '',
        ];
        return new DataResponse($params);
    }


}