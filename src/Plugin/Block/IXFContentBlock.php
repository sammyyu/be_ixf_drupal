<?php

namespace Drupal\be_ixf_drupal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'BrightEdge Foundation Content' Block.
 *
 * @Block(
 *   id = "ixf_content_block",
 *   admin_label = @Translation("BrightEdge Foundation Content Block"),
 *   category = @Translation("BrightEdge Foundation Content Block"),
 * )
 */

class IXFContentBlock extends BlockBase implements BlockPluginInterface {
  const NODE_TYPE_HEAD_STR = "HEADSTR";
  const NODE_TYPE_BODY_STR = "BODYSTR";
  const NODE_TYPE_CLOSE = "CLOSE";

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#description' => $this->t('The type of content block'),
      '#options' => [
        self::NODE_TYPE_HEAD_STR => $this->t('Head'),
        self::NODE_TYPE_BODY_STR => $this->t('Body'),
        self::NODE_TYPE_CLOSE => $this->t('Close'),
      ],
      '#default_value' => isset($config['type']) ? $config['type'] : 'BODYSTR',
    );

    $form['feature_group'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Feature Group Id'),
      '#description' => $this->t('The Id of the content block'),
      '#default_value' => isset($config['feature_group']) ? $config['feature_group'] : '',
    );

//    var_dump($form);

    return $form;
  }


  protected function baseConfigurationDefaults() {
    // not in default drupal see @https://www.drupal.org/project/drupal/issues/2911733
    // make default title not visible
    $defaults = parent::baseConfigurationDefaults();
//    var_dump(count($defaults));
    if (is_array($defaults) && isset($defaults['label_display'])) {
        $defaults['label_display']='';
    }
    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['type'] = $form_state->getValue('type');
    $this->configuration['feature_group'] = $form_state->getValue('feature_group');
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $node_type = $config['type'];
    $node_feature_group = $config['feature_group'];

    $be_ixf_client = \Drupal::service("brightedge.request");
//    $raw_html = "node_type=$node_type, node_feature_group=$node_feature_group"; 
    $raw_html = ""; 
    if ($node_type == self::NODE_TYPE_BODY_STR) {
      if ($node_feature_group == "_body_open") {
        $raw_html .= $be_ixf_client->getBodyOpen();
      } else {
        $raw_html .= $be_ixf_client->getBodyString($node_feature_group);
      }
    } else if ($node_type == self::NODE_TYPE_HEAD_STR) {
      if ($node_feature_group == "_head_open") {
        $raw_html .= $be_ixf_client->getHeadOpen();
      } else {
        $raw_html .= $be_ixf_client->getHeadString($node_feature_group);
      }
    } else {
      $raw_html .= $be_ixf_client->close();
    }
    $build = array(
      '#type' => 'inline_template',
      '#template' => '{{ somecontent | raw }}',
      '#context' => array(
        'somecontent' => $raw_html
      ),
      '#cache' => array()
    );
    // Set the cache data appropriately.
    $build['#cache']['contexts'] = $this->getCacheContexts();
    $build['#cache']['tags'] = $this->getCacheTags();
    $build['#cache']['max-age'] = $this->getCacheMaxAge();
    return $build;
  }
  
  public function getCacheMaxAge() {
    $module_config = \Drupal::config('brightedge.settings');
    $cache_age = 3600;
    if ($module_config->get('block_cache_max_age') != null) {
      $cache_age = intval($module_config->get('block_cache_max_age'));
    }
    return $cache_age;
  }

}
