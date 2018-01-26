<?php

namespace Drupal\be_ixf_drupal\Factory;
include __DIR__ . '../../../vendor/autoload.php';


use GuzzleHttp\ClientInterface;
use BrightEdge\BEIXFClient;
use BrightEdge\BEIXFClientInterface;

class BrightEdgeFactory {

  public static function createRequest($config, ClientInterface $client) {

    $be_config = $config->get('brightedge.settings');

    $be_ixf_config = array(
        BEIXFClient::$CAPSULE_MODE_CONFIG => $be_config->get('capsule_mode'),
        BEIXFClient::$ACCOUNT_ID_CONFIG => $be_config->get('account_id'),
        BEIXFClient::$API_ENDPOINT_CONFIG => $be_config->get('api_endpoint'),
    );

    $be_ixf_config['defer.redirect'] = "true";
    $be_ixf = new BEIXFClient($be_ixf_config);
    return $be_ixf;
  }

}

