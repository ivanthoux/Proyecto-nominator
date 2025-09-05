<?php defined('BASEPATH') or exit('No direct script access allowed');
class Migrate extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->input->is_cli_request()) {
      show_error('You don\'t have permission');
      return;
    }
  }

  public function index()
  {
    $this->load->library('migration');
    $migraton = $this->migration->latest();
    if (!$migraton) {
      // if ($this->migration->current() === FALSE) {
      echo $this->migration->error_string();
    } else {
      echo "Migration done\n";
    }
  }

  public function generate($name = false)
  {
    if (!$name) {
      echo "Please define migrate name" . PHP_EOL;
      return;
    }

    if (!preg_match('/^[a-z_]+$/i', $name)) {
      if (strlen($name) < 4) {
        echo "Migration mas be at least 4 characters long" . PHP_EOL;
        return;
      }
      echo "Wrong migration name, allowed characters: a-z and _\nExample: first_migration" . PHP_EOL;
      return;
    }

    $timestamp = date('YmdHis');
    $fileName = $timestamp . '_' . $name . '_' . $timestamp . '.php';
    try {
      $folderPath = APPPATH . 'migrations';
      if (!is_dir($folderPath)) {
        try {
          mkdir($folderPath);
        } catch (Exception $e) {
          echo "Error: \n" . $e->getMessage() . PHP_EOL;
        }
      }
      $filePath = APPPATH . 'migrations/' . $fileName;
      if (file_exists($filePath)) {
        echo "File allredy exists:\n" . $filePath . PHP_EOL;
        return;
      }
      $data['className'] = ucfirst($name) . '_' . $timestamp;
      $template = $this->load->view('migrations/migration_class_template', $data, true);
      try {
        $file = fopen($filePath, 'w');
        $content = "<?php \n" . $template;
        fwrite($file, $content);
        fclose($file);
      } catch (Exception $e) {
        echo "Error: \n" . $e->getMessage() . PHP_EOL;
      }
      echo "Migration creaated succesfully:\nLocation:" . $filePath . PHP_EOL;
    } catch (Exception $e) {
      echo "Can't create migration file:\nErro:" . $e->getMessage() . PHP_EOL;
    }
  }
}
