<?php

require_once 'unittests/selenium/SeleniumTest.php';

class MyTransfersTest extends SeleniumTest {

    protected $start_url_path = '?s=transfers';

    protected $pageContainerClass = 'fs-my-transfers';

    public function openPage() {
        $this->setupAuthenticated();

        $this->setUserPage();

        $this->navigateToPage($this->start_url_path);

        $pageBlock = $this->elemsByClassName($this->pageContainerClass);

        $isPageLoaded = !empty($pageBlock) && count($pageBlock) > 0;

        $this->assertEquals(
            $isPageLoaded,
            $isPageLoaded ? 'UserPreferences page loaded' : 'UserPreferences page not loaded'
        );
    }

    private function listActiveTransfers() {
        $elements = $this->elements($this->using('css selector')->value('.fs-my-transfers__active-transfers'));
        $count = count($elements);
        $this->assertTrue($count > 0);
    }

    private function listExpiredTransfers() {
        $elements = $this->elements($this->using('css selector')->value('.fs-my-transfers__expired-transfers'));
        $count = count($elements);
        $this->assertTrue($count > 0);
    }

    public function testMyTransfers()
    {
        extract($this->getKeyBindings());

        $this->openPage();

        $this->listActiveTransfers();

        $this->listExpiredTransfers();
    }
}
