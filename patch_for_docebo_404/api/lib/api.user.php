<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(_base_.'/api/lib/lib.api.php');

class User_API extends API {

	protected function _getBranch($like, $parent = false, $lang_code = false) {
		if (!$like) return false;
		$query = "SELECT oct.idOrg FROM %adm_org_chart as oc JOIN %adm_org_chart_tree as oct "
			." ON (oct.idOrg = oc.id_dir) WHERE oc.translation LIKE '".$like."'";
		if ($lang_code !== false) { //TO DO: check if lang_code is valid
			$query .= " AND oc.lang_code = '".$lang_code."'";
		}
		if ($parent !== false) {
			$query .= " AND oct.idParent = ".(int)$parent;
		}
		$res = $this->db->query($query);
		if ($this->db->num_rows($res) > 0) {
			list($output) = $this->db->fetch_row($res);
			return $output;
		} else
			return false;
	}

	public function checkUserIdst($idst) {
		$output = false;
		if (is_numeric($idst)) {
			$res = $this->db->query("SELECT * FROM %adm_user WHERE idst=".$idst);
			$output = ($this->db->num_rows($res) > 0);
		}
		return $output;
	}

	public function createUser($params, $userdata) {
		
		if (defined("_API_DEBUG") && _API_DEBUG) { file_put_contents('create_user.txt', "\n\n----------------\n\n".print_r($params, true)." || ".print_r($userdata, true), FILE_APPEND); }

		$set_idst =(isset($params['idst']) ? $params['idst'] : false);

		if (!isset($userdata['userid'])) return false;

		$id_user = $this->aclManager->registerUser(
			$userdata['userid'],
			(isset($userdata['firstname']) ? $userdata['firstname'] : '' ),
			(isset($userdata['lastname']) ? $userdata['lastname'] : ''),
			(isset($userdata['password']) ? $userdata['password'] : ''),
			(isset($userdata['email']) ? $userdata['email'] : ''),
			'',
			(isset($userdata['signature']) ? $userdata['signature'] : ''),
			false, // alredy_encripted
			$set_idst,
			(isset($userdata['pwd_expire_at']) ? $userdata['pwd_expire_at'] : '')
		);

		if(!$id_user)  {
			if (defined("_API_DEBUG") && _API_DEBUG) { file_put_contents('create_user.txt', "?!: ".var_export($id_user, true), FILE_APPEND); }
			return false;			
		}

		if ($id_user) {
			if (!isset($userdata['role'])) {
				$level = ADMIN_GROUP_USER;
			} else {
				switch ($userdata['role']) {
					case 'godadmin': $level = ADMIN_GROUP_GODADMIN;
					case 'admin': $level = ADMIN_GROUP_ADMIN;
					default: $level = ADMIN_GROUP_USER;
				}
			}

			//subscribe to std groups
			$group = $this->aclManager->getGroupST($level);//'/framework/level/user');
			$this->aclManager->addToGroup($group, $id_user);
			$group = $this->aclManager->getGroupST('/oc_0');
			$this->aclManager->addToGroup($group, $id_user);
			$group = $this->aclManager->getGroupST('/ocd_0');
			$this->aclManager->addToGroup($group, $id_user);

			if (isset($userdata['language'])) {
				Docebo::user()->preferences->setLanguage($userdata['language']);
			}
						

			//check if some additional fields have been set
			$okcustom = true;
			if(isset($userdata['_customfields'])) {
				require_once(_base_.'/lib/lib.field.php');
				$fields =& $userdata['_customfields'];
				if (count($fields)>0) {
					$fl = new FieldList();
					$okcustom = $fl->storeDirectFieldsForUser($id_user, $fields);
				}
			}			
			

     	$entities = array();
			if (isset($userdata['orgchart'])) {

				$branches = explode(";", $userdata['orgchart']);
				if (is_array($branches)) {
					foreach ($branches as $branch) {
						$idOrg = $this->_getBranch($branch);

						if ($idOrg !== false) {
							$oc = $this->aclManager->getGroupST('/oc_'.$idOrg);
							$ocd = $this->aclManager->getGroupST('/ocd_'.$idOrg);
							$this->aclManager->addToGroup($oc, $id_user);
							$this->aclManager->addToGroup($ocd, $id_user);
							$entities[$oc] = $oc;
							$entities[$ocd] = $ocd;
						}
					}
				}
			}
			
			$enrollrules = new EnrollrulesAlms();
			$enrollrules->newRules('_NEW_USER',
				array($id_user),
				$userdata['language'],
				0, // idOrg
				(!empty($entities) ? $entities : false)
			);			


			// save external user data:
			if ($params['ext_not_found'] && !empty($params['ext_user']) && !empty($params['ext_user_type'])) {
				$pref_path ='ext.user.'.$params['ext_user_type'];
				$pref_val ='ext_user_'.$params['ext_user_type']."_".(int)$params['ext_user'];
				
				$pref =new UserPreferencesDb();
				$pref->assignUserValue($id_user, $pref_path, $pref_val);
				if (defined("_API_DEBUG") && _API_DEBUG) { file_put_contents('create_user.txt', print_r($id_user, true)." || ".print_r($pref_path, true)." || ".print_r($pref_val, true), FILE_APPEND); }
			}
			else {
				if (defined("_API_DEBUG") && _API_DEBUG) { file_put_contents('create_user.txt', "??: \n\n".print_r($params, true), FILE_APPEND); }
			}

		}

		return $id_user;
	}

	public function updateUser($id_user, &$userdata) {
		
		$acl_man = new DoceboACLManager();
		$output = array();
		$res = $this->aclManager->updateUser(
			$id_user,
			(isset($userdata['userid']) ? $userdata['userid'] :  false),
			(isset($userdata['firstname']) ? $userdata['firstname'] :  false),
			(isset($userdata['lastname']) ? $userdata['lastname'] :  false),
			(isset($userdata['password']) ? $userdata['password'] :  false),
			(isset($userdata['email']) ? $userdata['email'] :  false),
			false,
			(isset($userdata['signature']) ? $userdata['signature'] :  false),
			(isset($userdata['lastenter']) ? $userdata['lastenter'] :  false),
			(isset($userdata['valid']) ? $userdata['valid'] :  false)
		);

		//additional fields
		$okcustom = true;
		if (isset($userdata['_customfields']) && $res) {
			require_once(_base_.'/lib/lib.field.php');
			$fields =& $userdata['_customfields'];
			if(count($fields) > 0) {
				$fl = new FieldList();
				$okcustom = $fl->storeDirectFieldsForUser($id_user, $fields);
			}
		}
		return $id_user;
	}

	public function getCustomFields($lang_code=false, $indexes=false) {

		require_once(_base_.'/lib/lib.field.php');
		$output = array();
		$fl = new FieldList();
		$fields = $fl->getFlatAllFields(false, false, $lang_code);
		foreach ($fields as $key=>$val) {

			if ($indexes)
				$output[$key] = $val;
			else
				$output[]=array('id'=>$key, 'name'=>$val);
		}
		return $output;
	}

	/**
	 * Return all the info about the user
	 * @param <int> $id_user the idst of the user
	 */
	private function getUserDetails($id_user) {
		require_once(_adm_.'/lib/lib.field.php');

		$user_data = $this->aclManager->getUser($id_user, false);
		$output = array();
		if (!$user_data) {
			$output['success'] = false;
			$output['message'] = 'Invalid user ID: '.$id_user.'.';
			$output['details'] = false;
		} else {
			$user_details = array(
				'idst' => $user_data[ACL_INFO_IDST],
				'userid' => $this->aclManager->relativeId($user_data[ACL_INFO_USERID]),
				'firstname' => $user_data[ACL_INFO_FIRSTNAME],
				'lastname' => $user_data[ACL_INFO_LASTNAME],
				//'password' => $user_data[ACL_INFO_PASS],
				'email' => $user_data[ACL_INFO_EMAIL],
				//'avatar' => $user_data[ACL_INFO_AVATAR],
				'signature' => $user_data[ACL_INFO_SIGNATURE],
				'valid' => $user_data[ACL_INFO_VALID],
				'pwd_expire_at' => $user_data[ACL_INFO_PWD_EXPIRE_AT],
				'register_date' => $user_data[ACL_INFO_REGISTER_DATE],
				'last_enter' => $user_data[ACL_INFO_LASTENTER]
			);

			$field_man = new FieldList();
			$field_data = $field_man->getFieldsAndValueFromUser($id_user, false, true);

			$fields = array();
			foreach($field_data as $field_id => $value) {
				$fields[] = array('id'=>$field_id, 'name'=>$value[0], 'value'=>$value[1]);
			}

			$user_details['custom_fields'] = $fields;

			$output['success'] = true;
			$output['message'] = '';
			$output['details'] = $user_details;
		}
		return $output;
	}

	/**
	 * Return the complete user list
	 */
	private function getUsersList() {
		$output = array();
		$query = "SELECT idst, userid, firstname, lastname FROM %adm_user WHERE idst<>".$this->aclManager->getAnonymousId()." ORDER BY userid";
		$res = $this->db->query($query);
		if ($res) {
			$output['success'] = true;
			$output['users_list'] = array();
			while($row = $this->db->fetch_assoc($res)) {
				$output['users_list'][]=array(
					'userid' => $this->aclManager->relativeId($row['userid']),
					'idst' => $row['idst'],
					'firstname' => $row['firstname'],
					'lastname' => $row['lastname']
				);
			}
		} else {
			$output['success'] = false;
		}
		return $output;
	}

	/**
	 * Delete a user
	 * @param <type> $id_user delete the user
	 */
	private function deleteUser($id_user) {
		$output = array();
		if ($this->aclManager->deleteUser($id_user)) {
			$output = array('success'=>true, 'message'=>'User #'.$id_user.' has been deleted.');
		} else {
			$output = array('success'=>false, 'message'=>'Error: unable to delete user #'.$id_user.'.');
		}
		return $output;
	}


	public function getMyCourses($id_user, $params=false) {
		require_once(_lms_.'/lib/lib.course.php');
		$output =array();		

		$output['success']=true;


		$search =array('cu.iduser = :id_user');
		$search_params =array(':id_user' => $id_user);

		
		if (!empty($params['filter'])) {
			switch ($params['filter']) {
				case 'completed': {
					$search[]='cu.status = :status';
					$search_params[':status']=_CUS_END;
				} break;
				case 'notcompleted': {
					$search[]='cu.status >= :status_from';
					$search_params[':status_from']=_CUS_SUBSCRIBED;
					$search[]='cu.status < :status_to';
					$search_params[':status_to']=_CUS_END;
				} break;
				case 'notstarted': {
					$search[]='cu.status = :status';
					$search_params[':status']=_CUS_SUBSCRIBED;
				} break;
			}
		}


		$model = new CourseLms();
		$course_list = $model->findAll($search, $search_params);

		//check courses accessibility
		$keys = array_keys($course_list);
		for ($i=0; $i<count($keys); $i++) {
			$course_list[$keys[$i]]['can_enter'] = Man_Course::canEnterCourse($course_list[$keys[$i]]);
		}

		//$output['log']=var_export($course_list, true);

		foreach($course_list as $key=>$course_info) {
			$output[]['course_info']=array(
				'course_id'=>$course_info['idCourse'],
				'course_name'=>str_replace('&', '&amp;', $course_info['name']),
				'course_description'=>str_replace('&', '&amp;', $course_info['description']),
				'course_link'=>Get::sett('url')._folder_lms_.'/index.php?modname=course&amp;op=aula&amp;idCourse='.$course_info['idCourse'],
			);
		}

		return $output;
	}


	public function KbSearch($id_user, $params) {
		require_once(_lms_.'/lib/lib.course.php');
		$output =array();

		$output['success']=true;

		$filter_text = (!empty($params['search']) ? $params['search'] : "");
		$course_filter = (!empty($params['course_filter']) ? (int)$params['course_filter'] : -1);
		$start_index = (!empty($params['start_index']) ? (int)$params['start_index'] : false);
		$results = (!empty($params['results']) ? (int)$params['results'] : false);

		//TODO: call getSearchFilter()

		$kb_model = new KbAlms();
		$sf =$kb_model->getSearchFilter($id_user, $filter_text, $course_filter);

		$res_arr = $kb_model->getResources(0, $start_index, $results, false,
			false, $sf['where'], $sf['search'], false, true, $sf['show_what']);


		foreach($res_arr["data"] as $key=>$content_info) {
			$output[]['content_info']=$content_info;
		}


		return $output;
	}


	public function importExternalUsers($userdata, $from_email=false) {
		$output =array('success'=>true);

		$i =0;
		foreach($userdata as $user_info) {
			$pref_path ='ext.user.'.$user_info['ext_user_type'];
			$pref_val ='ext_user_'.$user_info['ext_user_type']."_".(int)$user_info['ext_user'];

			$users =$this->aclManager->getUsersBySetting($pref_path, $pref_val);

			// if the user is not yet in sync..
			if (count($users) <= 0) {
				if (!$from_email) {
					// we search for the user from the userid:
					$user =$this->aclManager->getUser(false, $user_info['userid']);
				}
				else { // we search for the user from e-mail:
					$user =$this->aclManager->getUserByEmail($user_info['email']);
				}
				// if found, we link the account to the external one:
				if ($user) {
					$pref =new UserPreferencesDb();
					$pref->assignUserValue($user[ACL_INFO_IDST], $pref_path, $pref_val);
					$output['sync_'.$i]=$user_info['userid'];
					$i++;
				}
			}

		}

		return $output;
	}


	public function importExternalUsersFromEmail($userdata) {
		return $this->importExternalUsers($userdata, true);
	}

	
	public function call($name, $params) {
		$output = false;

		// Loads user information according to the external user data provided:
		$params =$this->checkExternalUser($params, $_POST);

		if (!empty($params[0]) && !isset($params['idst'])) {
			$params['idst']=$params[0]; //params[0] should contain user idst
		}

		switch ($name) {
			case 'userslist': {
				$list = $this->getUsersList();
				if ($list['success'])
					$output = array('success'=>true, 'list'=>$list['users_list']);
				else
					$output = array('success'=>false);
			} break;

			case 'userdetails': {
				if (count($params)>0 && !isset($params['ext_not_found'])) { //params[0] should contain user id

					if (is_numeric($params['idst'])) {
						$res = $this->getUserDetails($params['idst']);
						if (!$res) {
							$output = array('success'=>false, 'message'=>"Error: unable to retrieve user details.");
						}else{
							$output = array('success'=>true, 'details'=>$res['details']);
						}
					} else {
						$output = array('success'=>false, 'message'=>'Invalid passed parameter.');
					}
				} else {
					$output = array('success'=>false, 'message'=>'No parameter provided.');
				}
			} break;

			case 'customfields': {
				$tmp_lang = false; //if not specified, use default language
				if (isset($params['language'])) { $tmp_lang = $params['language']; } //check if a language has been specified
				$res = $this->getCustomFields($tmp_lang);
				if ($res != false) {
					$output = array('success'=>true, 'custom_fields'=>$res);
				} else {
					$output = array('success'=>false, 'message'=>'Error: unable to retrieve custom fields.');
				}
			} break;

			case 'createuser': {
				$res = $this->createUser($params, $_POST);
				if ($res != false) {
					$output = array('success'=>true, 'idst'=>$res);
				} else {
					$output = array('success'=>false, 'message'=>'Error: unable to create new user.');
				}
			} break;

			case 'userdetails': {
				if (!isset($params['ext_not_found'])) {
					$output = $this->getUserDetails($params['idst']);
				}
			} break;

			case 'updateuser': {
				if (count($params)>0 && !isset($params['ext_not_found'])) { //params[0] should contain user id
					$res = $this->updateUser($params['idst'], $_POST);
					$output = array('success'=>true);
				} else {
					$output = array('success'=>false, 'message'=>'Error: user id to update has not been specified.');
				}
			} break;

			case 'deleteuser': {
				if (count($params)>0 && !isset($params['ext_not_found'])) { //params[0] should contain user id
					$output = $this->deleteUser($params['idst'], $_POST);
				} else {
					$output = array('success'=>false, 'message'=>'Error: user id to update has not been specified.');
				}
			} break;

			case 'userdetailsbyuserid': {
				$acl_man = new DoceboACLManager();
				$idst = $acl_man->getUserST($params['userid']);
				if (!$idst) {
					$output = array('success'=>false, 'message'=>'Error: invalid userid: '.$params['userid'].'.');
				} else {
					$output = $this->getUserDetails($idst);
				}
			} break;

			case 'updateuserbyuserid': {
				if (count($params)>0) { //params[0] should contain user id
					$acl_man = new DoceboACLManager();
					$idst = $acl_man->getUserST($params['userid']);
					if (!$idst) {
						$output = array('success'=>false, 'message'=>'Error: invalid userid: '.$params['userid'].'.');
					} else {
						$res = $this->updateUser($idst, $_POST);
						$output = array('success'=>true);
					}
				} else {
					$output = array('success'=>false, 'message'=>'Error: user id to update has not been specified.');
				}
			} break;


			case 'mycourses': {
				if (!isset($params['ext_not_found'])) {
					$output = $this->getMyCourses($params['idst'], $_POST);
				}
			} break;


			case 'kbsearch': {
				if (!isset($params['ext_not_found'])) {
					$output = $this->KbSearch($params['idst'], $_POST);
				}
			} break;


			case 'importextusers': {
				$output = $this->importExternalUsers($_POST);
			} break;

			case 'importextusersfromemail': {
				$output = $this->importExternalUsersFromEmail($_POST);
			} break;


			default: $output = parent::call($name, $_POST);
		}
		return $output;
	}

}
