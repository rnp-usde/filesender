<?php

require_once 'unittests/selenium/SeleniumTest.php';

class SettingsTest extends SeleniumTest {

    protected $start_url_path = '?s=user';

    protected $pageContainerClass = 'fs-settings';

    public function openPage() {
        $this->setupAuthenticated();

        $this->setUserPage();

        $this->navigateToPage($this->start_url_path);

        $pageBlock = $this->elemsByClassName($this->pageContainerClass);

        $isPageLoaded = !empty($pageBlock) && count($pageBlock) > 0;

        $this->assertEquals(
            true,
            $isPageLoaded,
            'UserPreferences page loaded'
        );
    }

    private function changeLanguage($lang)
    {
        $this->waitForCSS(".fs-select #user_lang")->click();
        sleep(2);

        $this->waitForCSS( ".fs-select #user_lang option[value='".$lang."']" )->click();

        $this->waitForCSS( "#save-preferences" )->click();
    }

    private function activatePreviousTransferSettings()
    {
        $this->waitForCSS("#previous-settings")->click();

        $this->waitForCSS( "#save-preferences" )->click();

        $elements = $this->elements($this->using('css selector')->value('#previous-settings:checked'));
        $count = count($elements);
        $this->assertTrue($count > 0);
    }

    private function activateSaveRecipientsSettings() {
        $this->waitForCSS("#save-recipients-emails")->click();

        $this->waitForCSS( "#save-preferences" )->click();

        $elements = $this->elements($this->using('css selector')->value('#save-recipients-emails:checked'));
        $count = count($elements);
        $this->assertTrue($count > 0);
    }

    private function getSelectLabel()
    {
        return $this->byCssSelector(".fs-select > label")->text();
    }

    public function testSettings()
    {
        extract($this->getKeyBindings());

        $this->openPage();

        $this->changeLanguage( 'pt-br');
        $this->assertContains('Idioma', $this->getSelectLabel());

        $this->selectLangauge('en-us');
        $this->assertContains('Language', $this->getSelectLabel());

        $this->activatePreviousTransferSettings();

        $this->activateSaveRecipientsSettings();

    }
}
