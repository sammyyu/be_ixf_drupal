<?php

/**
 * @file
 * Contains \Drupal\be_ixf_drupal\EventSubscriber\RedirectHTTPHeaders.
 * @see https://github.com/chapter-three/http_response_headers
 */

namespace Drupal\be_ixf_drupal\EventSubscriber;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides RedirectHTTPHeaders.
 */
class RedirectHTTPHeaders implements EventSubscriberInterface {

  /**
   * Sets extra HTTP headers.
   */
  public function onRespond(FilterResponseEvent $event) {
    if (!$event->isMasterRequest()) {
      return;
    }
    

    $response = $event->getResponse();
/*
    $param = "X-Syu";
    $value = "blahz5";
    $response->headers->set($param, $value);
*/

    $be_ixf_client = \Drupal::service("brightedge.request");
    if ($be_ixf_client->hasRedirectNode()) {
      $redirect_info = $be_ixf_client->getRedirectNodeInfo();
      if ($redirect_info != null) {
        $status_code = $redirect_info[0];
        $location = $redirect_info[1];
        $response->setStatusCode($status_code);
        $response->headers->set("Location", $location);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onRespond'];
    return $events;
  }

}
