<?php
/**
 * ownCloud - owncollab
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author ownCollab Team <info@owncollab.com>
 * @copyright ownCollab Team 2015
 */


namespace OCA\Owncollab\Controller;

use OCA\Owncollab\Helper;
use OCA\Owncollab\Db\Connect;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IURLGenerator;


\OCP\App::checkAppEnabled('owncollab');

class MainController extends Controller
{

    /** @var string $userId
     * current auth user id  */
    private $userId;
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
     * @var string $client_group
     */
    private $client_group;

    /** @var IURLGenerator */
    private $urlGenerator;

    /**
     * MainController constructor.
     * @param string $appName
     * @param IRequest $request
     * @param $userId
     * @param $isAdmin
     * @param \OC\L10N\L10N $l10n
     * @param Connect $connect
     */
    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        $isAdmin,
        $l10n,
        Connect $connect,
        IURLGenerator $urlGenerator
    )
    {
        parent::__construct($appName, $request);
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;
        $this->l10n = $l10n;
        $this->connect = $connect;
        $this->urlGenerator = $urlGenerator;
        $this->client = 'client';
        $this->client_group = 'client';
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index()
    {
        $name = $this->connect->users->getUser($this->client);

        $groups = $this->connect->groups->getGroups();

        $check = $this->connect->group_user()->check($this->client_group,$this->client);

        $text_error = '<ul class="ul">';
        $flag = true;
        if (!$name) {
            $flag = false;
            $text_error .= "<li>create the user 'client'</li>";
        }
        if(!in_array($this->client_group, $groups)){
            $flag = false;
            $text_error .= "<li>create the group 'client'</li>";
        }
        if(!$check){
            $flag = false;
            $text_error .= "<li>add the 'client' user into group 'client'</li>";
        }
        $text_error .= '</ul>';

        $sd = [
            'flag' => $flag,
            'error' => $text_error
        ];
        unset($text_error);


        $client_name = $this->client;
        if ($name['displayname']) {
            $client_name = $name['displayname'];
        }

        $project_data = $this->connect->project_data()->getData($this->client);
        if (!$project_data) {
            $project_data = [];
        }


        $d = $this->connect->task()->get_project();


        $statistic = [];
        $statistic['tasks_finished'] = $this->connect->task()->getCountFinishedTasks();
        $statistic['all_tasks'] = $this->connect->task()->getCountAllTasks();
        $talks = $this->connect->talks->get();
        $partTalks = 0;
        function rec_search($this_id, &$arr_subscribers, $talks)
        {

            for ($j = 0; $j < count($talks); $j++) {
                if ($talks[$j]['rid'] == $this_id) {
                    $arr_subscribers = array_merge($arr_subscribers, explode(",", $talks[$j]['subscribers']));
                    array_push($arr_subscribers, $talks[$j]['author']);
                    rec_search($talks[$j]['id'], $arr_subscribers, $talks);

                }
            }
        }

        for ($i = 0; $i < count($talks); $i++) {
            if ($talks[$i]['rid'] == 0) {
                $arr_subscribers = [];
                $this_id = $talks[$i]['id'];
                $arr_subscribers = array_merge($arr_subscribers, explode(",", $talks[$i]['subscribers']));
                array_push($arr_subscribers, $talks[$i]['author']);
                rec_search($this_id, $arr_subscribers, $talks);

                if (in_array($this->userId, $arr_subscribers)) {
                    $partTalks++;
                }
            }


        }
        $statistic['participating_talks'] = $partTalks;
        $statistic['all_talks'] = count($this->connect->talks->getAllTalks());
        $statistic['users'] = count($this->connect->users->getAllUsers());
        $allActivity = $this->connect->activity->get();
        $countCreatedFiles = 0;
        $countDeletedFiles = 0;
        for ($i = 0; $i < count($allActivity); $i++) {
            if ($allActivity[$i]['type'] == "file_created"
                && $allActivity[$i]['app'] == "files"
                && $allActivity[$i]['subject'] == "created_self"
                && !empty($allActivity[$i]['subjectparams'])
                && $allActivity[$i]['object_type'] == "files"
            ) {
                $countCreatedFiles++;
            }
        }
        for ($i = 0; $i < count($allActivity); $i++) {
            if ($allActivity[$i]['type'] == "file_deleted"
                && $allActivity[$i]['app'] == "files"
                && $allActivity[$i]['subject'] == "deleted_self"
                && !empty($allActivity[$i]['subjectparams'])
                && $allActivity[$i]['object_type'] == "files"
            ) {
                $countDeletedFiles++;
            }
        }
        $statistic['all_files'] = $countCreatedFiles - $countDeletedFiles;
        $statistic['all_groups'] = count($this->connect->groups->get());
        $statistic['tasks_progress'] = 100 * $statistic['tasks_finished'] / $statistic['all_tasks'];
        $statistic['talks_progress'] = 100 * $statistic['participating_talks'] / $statistic['all_talks'];
        $projectName = $this->connect->task()->getById(1);
        $project_data['client_name'] = $client_name;
        $project_data['start_date'] = substr($d['start_date'], 0, -3);
        $project_data['end_date'] = substr($d['end_date'], 0, -3);

        $disabled = $this->isAdmin === true ? false : 'disabled';
        $params = [
            'sub_url' => '',
            'body_url' => '',
            'current_user' => $this->userId,
            'current_val' => $project_data,
            'statistic' => $statistic,
            'projectName' => $projectName['text'],
            'disabled' => $disabled,
            'sd' => $sd
        ];

        $params['sub_url'] = urldecode("Help me working in my project " . $projectName['text']);
        $params['body_url'] = urldecode("Hi there, currently I am working on the project " . $projectName['text'] . " and I will need some help with it. Please let me know, if you have resources to join the project.");


        return new TemplateResponse($this->appName, 'main', $params);
    }


}