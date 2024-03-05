<?php

namespace Drupal\rsvplist;

use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\Entity\Node;

/**
 * Determine if the service is available.
 */
class EnablerService {

  /**
   * Constructs a ReportController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(
    protected Connection $database,
    protected MessengerInterface $messenger
  ) {
  }

  /**
   * Checks if an individual node is RSVP enabled.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Current Node.
   *
   * @return bool
   *   whether or not the node is enabled for the RSVP functionality.
   */
  public function isEnabled(Node &$node) {
    if ($node->isNew()) {
      return FALSE;
    }
    try {
      $select = $this->database->select('rsvplist_enabled', 're');
      $select->fields('re', ['nid']);
      $select->condition('nid', $node->id());
      $results = $select->execute();
      return !(empty($results->fetchCol()));
    }
    catch (\Throwable $th) {
      $this->messenger->addError($this->t('Unable to determine RSVP settings at this time. Please try again.'));
      return NULL;
    }
  }

  /**
   * Sets an individual node to be RSVP enabled.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node.
   *
   * @throws Exception
   */
  public function setEnabled(Node $node) {
    try {
      if (!($this->isEnabled($node))) {
        $insert = $this->database->insert('rsvplist_enabled');
        $insert->fields(['nid']);
        $insert->values([$node->id()]);
        $insert->execute();
      }
    }
    catch (\Throwable $th) {
      $this->messenger->addError($this->t('Unable to save RSVP settings at this time. Please try again.'));
    }
  }

  /**
   * Deletes RSVP enabled settings for an individual node.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Current node Object.
   */
  public function delEnabled(Node $node) {
    try {
      $delete = $this->database->delete('rsvplist_enabled');
      $delete->condition('nid', $node->id());
      $delete->execute();
    }
    catch (\Throwable $th) {
      $this->messenger->addError($this->t('Unable to save RSVP settings at this time. Please try again.'));
    }
  }

}
