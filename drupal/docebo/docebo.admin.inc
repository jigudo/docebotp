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
