<?php

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * Allows the profile to alter the site configuration form.
 */
function biolife_form_install_configure_form_alter(&$form, $form_state) {
  // Pre-populate the site name with the server name.
  $form['site_information']['site_name']['#default_value'] = $_SERVER['SERVER_NAME'];
  $form['#submit'][] = 'biolife_form_submit';
}


function biolife_form_submit($form, &$form_state) {
  // Import database biolife file.
  $biolife_file = dirname(__FILE__) . '/biolife.sql';
  $success = import_biolife($biolife_file);

  if (!$success) {
    return;
  }

  // Now re-set the values they filled in during the previous step.
  variable_set('site_name', $form_state['values']['site_name']);
  variable_set('site_mail', $form_state['values']['site_mail']);
  variable_set('date_default_timezone', $form_state['values']['date_default_timezone']);
  variable_set('clean_url', $form_state['values']['clean_url']);
  variable_set('update_status_module', $form_state['values']['update_status_module']);

  // Perform additional clean-up tasks.
  variable_del('file_directory_temp');

  // Replace their username and password and log them in.
  $name = $form_state['values']['account']['name'];
  $pass = $form_state['values']['account']['pass'];
  $mail = $form_state['values']['account']['mail'];
  db_update('users')
      ->fields(array(
        'name' => $name,
        'pass' => user_hash_password($pass),
        'mail' => $mail,
      ))
      ->condition('uid', 1)
      ->execute();
  user_authenticate($name, $pass);

  // Finally, redirect them to the front page to show off what they've done.
  drupal_goto('<front>');
}

/// The rest is copy/paste/modify code from demo module. ///

/**
 * Imports a database biolife file.
 *
 * @see demo_reset().
 */
function import_biolife($filename) {

  global $databases;
  $db_prefix = $databases["default"]["default"]["prefix"];
  
  // Open biolife file.
  if (!file_exists($filename) || !($fp = fopen($filename, 'r'))) {
    drupal_set_message(t('Unable to open biolife file %filename.', array('%filename' => $filename)), 'error');
    return FALSE;
  }

  $options = array(
    'target' => 'default',
    'return' => Database::RETURN_NULL,
    // 'throw_exception' => FALSE,
  );

  // Drop all existing tables.
  foreach (list_tables() as $table) {
    db_drop_table(substr($table,strlen($db_prefix)));
  }

  // Load data from biolife file.
  $success = TRUE;
  $query = '';
  $new_line = TRUE;

  while (!feof($fp)) {
    // Better performance on PHP 5.2.x when leaving out buffer size to
    // fgets().
    $data = fgets($fp);
    if ($data === FALSE) {
      break;
    }
    // Skip empty lines (including lines that start with a comment).
    if ($new_line && ($data == "\n" || !strncmp($data, '--', 2) || !strncmp($data, '#', 1))) {
      continue;
    }

    $query .= $data;
    $len = strlen($data);
    if ($data[$len - 1] == "\n") {
      if ($data[$len - 2] == ';') {
        $query = str_replace('CREATE TABLE IF NOT EXISTS `', 'CREATE TABLE IF NOT EXISTS `'.$db_prefix, $query);
        $query = str_replace('INSERT INTO `', 'INSERT INTO `'.$db_prefix, $query);
		// Reached the end of a query, now execute it.
        $stmt = Database::getConnection($options['target'])->prepare($query);
		if (!$stmt->execute(array(), $options)) {
          if ($verbose) {
            $success = FALSE;
          }
		}
        $query = '';
      }
      $new_line = TRUE;
    }
  }
  fclose($fp);
  

  if (!$success) {
    drupal_set_message(t('Failed importing database from %filename.', array('%filename' => $filename)), 'error');
  }

  return $success;
}

/**
 * Returns a list of tables in the active database.
 *
 * Only returns tables whose prefix matches the configured one (or ones, if
 * there are multiple).
 *
 * @see demo_enum_tables()
 */
function list_tables() {
  global $databases;
  $db_prefix = $databases["default"]["default"]["prefix"];

  $tables = array();

  if (is_array($db_prefix)) {
    // Create a regular expression for table prefix matching.
    $rx = '/^' . implode('|', array_filter($db_prefix)) . '/';
  }
  else if ($db_prefix != '') {
    $rx = '/^' . $db_prefix . '/';
  }

  $tables = db_find_tables('%');

  foreach($tables as $table) {
    if (is_array($db_prefix)) {
      // Check if table name matches a configured prefix.
      if (preg_match($rx, $table, $matches)) {
        $table_prefix = $matches[0];
        $plain_table = substr($table, strlen($table_prefix));
        if ($db_prefix[$plain_table] == $table_prefix || $db_prefix['default'] == $table_prefix) {
          $tables[] = $table;
        }
      }
    }
    else if ($db_prefix != '') {
      if (preg_match($rx, $table)) {
        $tables[] = $table;
      }
    }
    else {
      $tables[] = $table;
    }
  }

  return $tables;
}