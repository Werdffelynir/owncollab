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

class MainController extends Controller {

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

    /** @var IURLGenerator */
    private $urlGenerator;

    /**
     * MainController constructor.
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
		\OC_L10N $l10n,
		Connect $connect,
        IURLGenerator $urlGenerator
    ){
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->isAdmin = $isAdmin;
		$this->l10n = $l10n;
		$this->connect = $connect;
		$this->urlGenerator = $urlGenerator;
	}


	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {

// mostly for the home storage's free space
		//$dirInfo = \OC\Files\Filesystem::getFileInfo('/', false);
		//$storageInfo = \OC_Helper::getStorageInfo('/', $dirInfo);


		//$dirCont = \OC\Files\Filesystem::getMountManager()->getAll(); //getDirectoryContent('/');
		//$dir = \OCA\Files\Helper::getFiles('/');
        //$files = \OCA\Files\Helper::populateTags($dir);
//        $files = \OCA\Files\Helper::getFiles('/');
//        foreach($files as $file){
//            var_dump(\OCA\Files\Helper::formatFileInfo($file));
//        }

		//$navItems = \OCA\Files\App::getNavigationManager()->getAll();

		//var_dump($navItems);
		//var_dump($dir);
		//var_dump($files);


		//die;




		$result = $this->connect->project()->get();
		if(!$result){
			$result=[];
		}
		$statistic = [];
		$statistic['tasks_finished'] = $this->connect->task()->getCountFinishedTasks();
		$statistic['all_tasks'] = $this->connect->task()->getCountAllTasks();
		$talks = $this->connect->talks->get();
		$partTalks = 0;
		function rec_search($this_id, &$arr_subscribers, $talks){
			
			for($j=0;$j<count($talks); $j++){
				if($talks[$j]['rid']==$this_id){
					$arr_subscribers = array_merge($arr_subscribers,explode(",", $talks[$j]['subscribers']));
					array_push($arr_subscribers,$talks[$j]['author']);
					rec_search($talks[$j]['id'], $arr_subscribers,$talks);
					
				}
			}
		}
		for($i=0;$i<count($talks);$i++){
			if($talks[$i]['rid']==0){
				$arr_subscribers=[];
				$this_id = $talks[$i]['id'];
				$arr_subscribers = array_merge($arr_subscribers,explode(",", $talks[$i]['subscribers']));
				array_push($arr_subscribers,$talks[$i]['author']);
				rec_search($this_id, $arr_subscribers,$talks);
				
				if(in_array($this->userId ,$arr_subscribers)){
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
		for($i=0;$i<count($allActivity); $i++){
			if($allActivity[$i]['type']=="file_created"
				&& $allActivity[$i]['app']=="files"
				&& $allActivity[$i]['subject']=="created_self"
			    && !empty($allActivity[$i]['subjectparams'])
				&& $allActivity[$i]['object_type'] == "files"){
				$countCreatedFiles++;
			}
		}
		for($i=0;$i<count($allActivity); $i++){
			if($allActivity[$i]['type']=="file_deleted"
				&& $allActivity[$i]['app']=="files"
				&& $allActivity[$i]['subject']=="deleted_self"
			    && !empty($allActivity[$i]['subjectparams'])
				&& $allActivity[$i]['object_type'] == "files"){
				$countDeletedFiles++;
			}
		}
		$statistic['all_files'] =  $countCreatedFiles - $countDeletedFiles;
		$statistic['all_groups'] = count($this->connect->groups->get());
		$statistic['tasks_progress'] = 100*$statistic['tasks_finished']/$statistic['all_tasks'];
		$statistic['talks_progress'] = 100*$statistic['participating_talks']/$statistic['all_talks'];
		$projectName = $this->connect->task()->getById(1);
		$result['logo_src'] = Helper::prevImg($result['logo']);
		$params = [
			'sub_url' => '',
			'body_url' => '',
			'current_user' => $this->userId,
			'current_val' =>$result,
			'statistic' =>$statistic,
			'projectName' =>$projectName['text']
		];
		
		
		$params['sub_url'] = urldecode("Help me working in my project ".$projectName['text']);
		$params['body_url'] = urldecode("Hi there, currently I am working on the project ".$projectName['text']." and I will need some help with it. Please let me know, if you have resources to join the project.");




		return new TemplateResponse($this->appName, 'main', $params);
	}
	

}