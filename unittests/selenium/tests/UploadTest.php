<?php

require_once 'unittests/selenium/SeleniumTest.php';

class UploadTest extends SeleniumTest {

    protected $start_url_path = '?s=upload';

    protected $pageContainerClass = 'fs-transfer';

    public function openPage() {
        $this->setupAuthenticated();

        $this->setUserPage();

        $this->navigateToPage($this->start_url_path);

        $pageBlock = $this->elemsByClassName($this->pageContainerClass);

        $isPageLoaded = !empty($pageBlock) && count($pageBlock) > 0;

        $this->assertEquals(
            true,
            $isPageLoaded,
            'Upload page loaded'
        );
    }

    public function startUpload() {
        $this->setupAuthenticated();

        $this->setUserPage();

        $this->navigateToPage($this->start_url_path);

        $pageBlock = $this->elemsByClassName($this->pageContainerClass);

        $isPageLoaded = !empty($pageBlock) && count($pageBlock) > 0;

        $this->waitForCSS(".fs-transfer__droparea .fs-button")->click();

        sleep(2);
    }

    public function addFiles() {
        $fp1 = $this->addFile( "124bytes.txt" );
        $fp2 = $this->addFile( "125bytes.txt" );
    }

    public function nextStep() {
        $this->waitForCSS("#fs-transfer__next-step")->click();
    }

    public function cleanAll() {
        $this->waitForCSS(".fs-transfer__clear-all")->click();
    }

    public function setupTransferByEmail() {
        $this->ensureTransferByEmail();

        $this->uploadPageStage2ShowAdvancedOptions();

        $this->ensureOptions( array(
            'add_me_to_recipients' => false,
            'email_me_on_expire' => true,
            'email_daily_statistics' => true,
            'email_me_copies' => false,
            'email_upload_complete' => true,
            'email_download_complete' => true,
            'enable_recipient_email_download_complete' => false
        ));

        $recipients = array('usera@filetestertest.test', 'userb@filetestertest.test', 'userc@filetestertest.test');
        $subject = 'testSubject_' . rand(0, 100);
        $content = 'testContent_' . rand(0, 100);

        $this->sendMessageToRecipients($recipients, $subject, $content);
    }

    public function confirmUpload() {
        $this->waitForCSS("#fs-transfer__confirm")->click();

        $this->waitForUploadCompleteDialog( false );

        // if no time outs from above we assert ok
        $this->assertTrue(true);
    }

    public function testUpload()
    {
        extract($this->getKeyBindings());

        $this->openPage();
        $this->startUpload();
        $this->addFiles();
        $this->nextStep();
        $this->setupTransferByEmail();
        $this->confirmUpload();
    }
}
