<?php

namespace Drupal\Tests\rsvplist\Functional\Controller;

use Drupal\Tests\BrowserTestBase;

/**
 * The simple test class.
 *
 * @group rsvplist
 */
class ReportControllerTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = ['rsvplist'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * @covers ::report
   */
  public function testReport() {
    // Create user.
    $account = $this->drupalCreateUser(['view rsvplist']);
    $this->drupalLogin($account);
    $this->drupalGet('admin/reports/rsvplist');
    $this->assertSession()->statusCodeEquals(403);

    $account = $this->drupalCreateUser(['view rsvplist'], NULL, TRUE);
    $this->drupalLogin($account);
    $this->drupalGet('admin/reports/rsvplist');
    $this->assertSession()->statusCodeEquals(200);

    $account = $this->drupalCreateUser(['access rsvplist report']);
    $this->drupalLogin($account);
    $this->drupalGet('admin/reports/rsvplist');
    $this->assertSession()->statusCodeEquals(200);
  }

}
