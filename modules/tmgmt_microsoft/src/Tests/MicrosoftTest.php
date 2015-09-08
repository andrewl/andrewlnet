<?php
/**
 * @file
 * Contains \Drupal\tmgmt_microsoft\Tests\MicrosoftTest.
 */

namespace Drupal\tmgmt_microsoft\Tests;
use Drupal\tmgmt\Tests\TMGMTTestBase;
use Drupal\Core\Url;

/**
 * Basic tests for the Microsoft translator.
 *
 * @group tmgmt_microsoft
 */
class MicrosoftTest extends TMGMTTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('tmgmt_microsoft', 'tmgmt_microsoft_test');

  /**
   * Tests basic API methods of the plugin.
   */
  protected function testMicrosoft() {
    $this->addLanguage('de');
    $translator = $this->createTranslator([
      'plugin' => 'microsoft',
      'settings' => [
        'url' => URL::fromUri('base://tmgmt_microsoft_mock/v2/Http.svc', array('absolute' => TRUE))->toString(),
      ],
    ]);

    $job = $this->createJob();
    $job->translator = $translator->id();
    $item = $job->addItem('test_source', 'test', '1');
    $item->save();

    $this->assertFalse($job->isTranslatable(), 'Check if the translator is not available at this point because we did not define the API parameters.');

    // Save a wrong client ID key.
    $translator->setSetting('client_id', 'wrong client_id');
    $translator->setSetting('client_secret', 'wrong client_secret');
    $translator->save();

    $translator = $job->getTranslator();
    $languages = $translator->getSupportedTargetLanguages('en');
    $this->assertTrue(empty($languages), t('We can not get the languages using wrong api parameters.'));

    // Save a correct client ID.
    $translator->setSetting('client_id', 'correct client_id');
    $translator->setSetting('client_secret', 'correct client_secret');
    $translator->save();

    // Make sure the translator returns the correct supported target languages.
    $translator->clearLanguageCache();
    $languages = $translator->getSupportedTargetLanguages('en');
    $this->assertTrue(isset($languages['de']));
    $this->assertTrue(isset($languages['es']));
    $this->assertTrue(isset($languages['it']));
    $this->assertTrue(isset($languages['zh-hans']));
    $this->assertTrue(isset($languages['zh-hant']));
    $this->assertFalse(isset($languages['zh-CHS']));
    $this->assertFalse(isset($languages['zh-CHT']));
    $this->assertFalse(isset($languages['en']));

    $this->assertTrue($job->canRequestTranslation());

    $job->requestTranslation();

    // Now it should be needs review.
    foreach ($job->getItems() as $item) {
      $this->assertTrue($item->isNeedsReview());
    }

    $items = $job->getItems();
    $item = end($items);
    $data = $item->getData();
    $this->assertEqual('Hallo Welt', $data['dummy']['deep_nesting']['#translation']['#text']);
  }
}
