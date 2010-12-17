<?php
// $Id: $

/**
 * @file
 * Admin page callbacks for the docebo module.
 */

/**
 * Menu callback; displays the docebo administration page.
 */
function docebo_admin_display() { 
  drupal_theme_initialize();
  return drupal_get_form('docebo_admin_form');
}


function docebo_admin_form($form, &$form_state, $edit = array('docebo_url' => '')) {

	$edit['docebo_url']=variable_get('docebo_url');
	$edit['docebo_user']=variable_get('docebo_user');
	$edit['docebo_pass']=variable_get('docebo_pass');
	$edit['docebo_use_ssl']=variable_get('docebo_use_ssl');

	$form['title'] = array('#type' => 'textfield',
		'#title' => t('Docebo Url'),
		'#default_value' => $edit['docebo_url'],
		'#maxlength' => 64,
		'#required' => TRUE,
	);
	
	$form['docebo_user'] = array('#type' => 'textfield',
		'#title' => t('Docebo user (sso)'),
		'#default_value' => $edit['docebo_user'],
		'#maxlength' => 64,
		'#required' => TRUE,
	);
	
	$form['docebo_pass'] = array('#type' => 'password',
		'#title' => t('Docebo user password (sso)'),
		'#default_value' => $edit['docebo_pass'],
		'#maxlength' => 64,
		'#required' => false,
	);
	
	$form['use_ssl'] = array('#type' => 'checkbox',
		'#title' => t('Use SSL'),
		'#default_value' => $edit['docebo_use_ssl'],
		'#maxlength' => 64,
		'#required' => false,
	);
	
	$form['export_user'] = array('#type' => 'link',
		'#title' => t('Sync users from Drupal to Docebo'),
		'#href' => 'admin/config/system/docebo_users',
		'#target' => '_blank',
		'#required' => false,
	);

	$form['actions'] = array('#type' => 'actions');
	$form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Save'));

	return $form;
}


function docebo_admin_form_validate($form, &$form_state) {
 
}

/**
 * Process docebo_form form submissions.
 *
 * @todo Add delete confirmation dialog.
 */
function docebo_admin_form_submit($form, &$form_state) {

	variable_set('docebo_url', $form_state['values']['title']);
	variable_set('docebo_user', $form_state['values']['docebo_user']);
	variable_set('docebo_pass', $form_state['values']['docebo_pass']);
	variable_set('docebo_use_ssl', $form_state['values']['use_ssl']);

	watchdog('docebo', 'Settings saved.', array(), WATCHDOG_NOTICE, l(t('view'), 'admin/config/system/docebo'));
	drupal_set_message(t('Settings saved.', array()));
}


function docebo_users_admin_display() { 
	$result ='';
	
	if (isset($_POST['do_export'])) { // export / (master->slave) sync users from drupal to docebo
	
		$query = db_select('users'); //->extend('PagerDefault');
		$query->fields('users', array('uid'));
		$uids = $query
			->execute()
			->fetchCol();
		$users =user_load_multiple($uids);
		
		$userdata=array();
		$i =0;
		foreach($users as $user_info) {
			if ($user_info->uid > 0) {
				$userdata['user_'.$i.'[ext_user]']=$user_info->uid;
				$userdata['user_'.$i.'[ext_user_type]']='drupal';
				$userdata['user_'.$i.'[userid]']=$user_info->name;
				$userdata['user_'.$i.'[email]']=$user_info->mail;

				$i++;
			}
		}

		if (!empty($userdata)) {
			$req_res =docebo_request('importextusers', $userdata);
			// file_put_contents('log3.txt', var_export($req_res, true));
		}
		
		watchdog('docebo', 'Drupal users has been synchronized with the ones matching in Docebo.', array(), WATCHDOG_NOTICE, l(t('view'), 'admin/config/system/docebo_users'));
		drupal_set_message(t('Drupal users has been synchronized with the ones matching in Docebo.', array()));
	}

	$result.='<p>'.t('You can synchronize your Docebo users with the one from you Drupal installation. Clicking "Sync users from Drupal to Docebo" will "link" the accounts in Drupal with the correspoinding ones (having the same username) in your Docebo installation.').'</p>';  
	
	$result.=drupal_render(drupal_get_form('docebo_users_admin_form'), true);  //echo $result;  die();
	
	return $result;
}


function docebo_users_admin_form($form, &$form_state, $edit = array('docebo_url' => '')) {

	$edit['docebo_url']=variable_get('docebo_url');
	$edit['docebo_user']=variable_get('docebo_user');
	$edit['docebo_pass']=variable_get('docebo_pass');
	$edit['docebo_use_ssl']=variable_get('docebo_use_ssl');

	$form['do_export'] = array('#type' => 'hidden',
		'#value' => 1,
		'#required' => false,
	);

	$form['actions'] = array('#type' => 'actions');
	$form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Sync users from Drupal to Docebo'));

	return $form;
}