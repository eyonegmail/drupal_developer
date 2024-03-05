<?php

namespace Drupal\rsvplist\Form;

use Drupal\Component\Datetime\Time;
use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RSVP list create.
 */
class RSVPForm extends FormBase {

  /**
   * The current route object.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The email validator object.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The time.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $time;

  /**
   * Cconstructs a RSVPForm object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The RouteMatch.
   * @param \Drupal\Component\Utility\EmailValidatorInterface $emailValidator
   *   The email validator.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Component\Datetime\Time $time
   *   The time.
   */
  public function __construct(RouteMatchInterface $routeMatch, EmailValidatorInterface $emailValidator, Connection $database, MessengerInterface $messenger, AccountProxyInterface $currentUser, Time $time) {
    $this->routeMatch = $routeMatch;
    $this->emailValidator = $emailValidator;
    $this->database = $database;
    $this->messenger = $messenger;
    $this->currentUser = $currentUser;
    $this->time = $time;
  }

  /**
   * Create services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('email.validator'),
      $container->get('database'),
      $container->get('messenger'),
      $container->get('current_user'),
      $container->get('datetime.time'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Attempt to get the fully loaded node object of the viewed page.
    // $node = \Drupal::routeMatch()->getParameter('node');
    $node = $this->routeMatch->getParameter('node');
    // Some pages may not be nodes though and $node will be NULL on those pages.
    // If a node was loaded, get the node id.
    if (!(is_null($node))) {
      $nid = $node->id();
    }
    else {
      // If a node could not be loaded, default to 0.
      $nid = 0;
    }
    // Establish the $form render array. It has an email text field,
    // a submit button, and a hidden field containing the node ID.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email address'),
      '#size' => '',
      '#description' => $this->t("We will send update to the email address you provide."),
      '#required' => TRUE,
    ];
    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Special Code'),
      '#size' => '',
      '#description' => $this->t("If you have an special code, please input."),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if (!($this->emailValidator->isValid($value))) {
      $form_state->setErrorByName('email', $this->t('It appears that %mail is not a valid email. Please try again.', ['%mail' => $value]));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // $submitted_email = $form_state->getValue('email');
    // $this->messenger()->addMessage(t("The form is working! You entered @entry.", ['@entry' => $submitted_email]));
    try {
      // Begin Phase 1: initiate variables to save.
      // Get current user ID.
      // $uid = \Drupal::currentUser()->id();
      $uid = $this->currentUser->id();
      // Demonstration for how to load a full user object of the current user.
      // This $full_user variable is not needed for this code,
      // but is shown for demonstration purposes.
      // $full_user = User::load(\Drupal::currentUser()->id());
      // Obtain values as entered into the Form.
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');
      $code = $form_state->getValue('code');
      // $current_time = \Drupal::time()->getRequestTime();
      $current_time = $this->time->getRequestTime();
      // End Phase 1.
      // Begin Phase 2: Save the values to the database.
      // Start to build a query builder object $query.
      // https://www.drupal.org/docs/10/api/database-api/insert-queries
      // $query = \Drupal::database()->insert('rsvplist');
      $query = $this->database->insert('rsvplist');
      // Specify the fields that the query will insert into.
      $query->fields([
        'uid',
        'nid',
        'mail',
        'code',
        'created',
      ]);
      // Set the values of the fields we selected.
      // Note that they must be in the same order as we defined them
      // in the $query->fields([...]) above.
      $query->values([
        $uid,
        $nid,
        $email,
        $code,
        $current_time,
      ]);
      // Execute the query!
      // Drupal handles the exact syntax of the query automatically!
      $query->execute();
      // End Phase 2.
      // Begin Phase 3: Display a success message.
      // Provide the form submitter a nice message.
      // \Drupal::messenger()->addMessage(
      $this->messenger->addMessage(
        $this->t('Thank you for your RSVP, you are on the list for the event!')
      );
      // End Phase 3.
    }
    catch (\Throwable $th) {
      // \Drupal::messenger()->addError(
      $this->messenger->addError(
        $this->t('Unable to save RSVP settings at this time due to database error. Please try again.')
      );
    }
  }

}
