<?php

namespace Drupal\brightedge\Service;

use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageManagerInterface;

class BrightedgeService implements BrightedgeServiceInterface {

  protected $config;
  protected $database;
  protected $langaugeManager;

  public function __construct($config, Connection $database, LanguageManagerInterface $languageManager) {
    $this->config = $config->get('brightedge.settings');
    $this->languageConfig = $config->get('brightedge.locales');
    $this->database = $database;
    $this->languageManager = $languageManager;
  }

}
?>

