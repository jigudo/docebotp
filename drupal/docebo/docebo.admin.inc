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

	$form['title'] = array('#type' => 'textfield',
		'#title' => t('Docebo Url'),
		'#default_value' => $edit['docebo_url'],
		'#maxlength' => 64,
		'#required' => TRUE,
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
	print_r($form);

	watchdog('docebo', 'Settings saved.', array(), WATCHDOG_NOTICE, l(t('view'), 'admin/config/system/docebo'));
	drupal_set_message(t('Settings saved.', array()));
}
