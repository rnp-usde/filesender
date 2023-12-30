<?php

require_once 'unittests/selenium/SeleniumTest.php';

class MyInvitationsTest extends SeleniumTest {

    protected $start_url_path = '?s=guests';

    protected $pageContainerClass = 'fs-invitations';

    public function openPage() {
        $this->setupAuthenticated();

        $this->setUserPage();

        $this->navigateToPage($this->start_url_path);

        $pageBlock = $this->elemsByClassName($this->pageContainerClass);

        $isPageLoaded = !empty($pageBlock) && count($pageBlock) > 0;

        $this->assertEquals(
            true,
            $isPageLoaded,
            'My Invitations page loaded'
        );
    }

    private function listInvitations() {
        $elements = $this->elements($this->using('css selector')->value('.fs-invitations__list'));
        $count = count($elements);
        $this->assertTrue($count > 0);
    }

    public function testMyTransfers()
    {
        extract($this->getKeyBindings());

        $this->openPage();

        $this->listInvitations();
    }
}
