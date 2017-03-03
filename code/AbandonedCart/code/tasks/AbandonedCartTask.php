<?php

/**
 * Class AbandonedCartTask
 */
class AbandonedCartTask implements CronTask
{
    protected $title = 'Abandoned Cart Follow-up';
    protected $description = 'Sends email reminders to users who left a cart abandoned';

    private static $default_header = '';
    private static $default_footer = '';

    protected $header_image = '';
    protected $footer_image = '';
    protected $email_subject = 'You left something behind!';
    protected $email_intro = '';
    protected $email_footer = '';
    protected $email_reply_to = '';

    protected $br;
    protected $hr;

    public function __construct()
    {
        /** =========================================
         * @var AbandonedCartConfig|SiteConfig $siteConfig
         * ========================================*/

        if (Director::is_cli()) {
            $this->br = PHP_EOL;
            $this->hr = '---' . PHP_EOL;
        } else {
            $this->br = '<br>';
            $this->hr = '<hr>';
        }

        /** -----------------------------------------
         * Set up email defaults
         * ----------------------------------------*/

        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->CartEmailHeaderImage() && $siteConfig->CartEmailHeaderImage()->exists()) {
            $headerImage = $siteConfig->CartEmailHeaderImage();
            $this->header_image = $headerImage->Fill(600, 360)->getAbsoluteURL();
        } else {
            $this->header_image = Config::inst()->get('AbandonedCartTask', 'default_header ');
        }

        if ($siteConfig->CartEmailFooterImage() && $siteConfig->CartEmailFooterImage()->exists()) {
            $footerImage = $siteConfig->CartEmailFooterImage();
            $this->footer_image = $footerImage->Fill(600, 360)->getAbsoluteURL();
        } else {
            $this->footer_image = Config::inst()->get('AbandonedCartTask', 'default_footer ');
        }

        if ($siteConfig->CartEmailSubject) {
            $this->email_subject = $siteConfig->CartEmailSubject;
        }

        if ($siteConfig->CartEmailContent) {
            $this->email_intro = $siteConfig->dbObject('CartEmailContent')->forTemplate();
        }

        if ($siteConfig->CartEmailFooterContent) {
            $this->email_footer = $siteConfig->dbObject('CartEmailFooterContent')->forTemplate();
        }

        if ($siteConfig->CartEmailReplyTo) {
            $this->email_reply_to = $siteConfig->CartEmailReplyTo;
        } else {
            $this->email_reply_to = Config::inst()->get('Email', 'admin_email');
        }
    }

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function process()
    {
        if (SiteConfig::current_site_config()->EnableAbandonedCart) {

            /** =========================================
             * @var SignUpMessage $message
             * ========================================*/

            $query  = "SELECT NOW() as `now`";
            $result = DB::query($query);
            $now    = $result->value();

            echo sprintf('Starting task at %s', $now) . $this->br;

            /** -----------------------------------------
             * Get the latest messages
             * ----------------------------------------*/

            // Get messages that have status of New that have a cart
            $cartMessages = SignUpMessage::get()
                ->filter(array(
                    'CartID:GreaterThan' => 0,
                    'Status' => 'New'
                ))
                ->where('"SignUpMessage"."Created" < DATE_SUB(NOW(), INTERVAL 3 HOUR)')
                ->sort('Created DESC');

            if ($cartMessages && $cartMessages->exists()) {

                echo 'Found pending abandoned carts.' . $this->br;
                echo $this->hr;

                // We don't want to spam
                $processedEmails = array();

                /** -----------------------------------------
                 * Loop through messages
                 * ----------------------------------------*/

                foreach ($cartMessages as $message) {
                    // If it is more than 3 hours old
                    $email = $message->Email;

                    if (!in_array($email, $processedEmails)) {
                        echo sprintf('Processing message: %s <%s>', $message->Name, $email) . $this->br;

                        // Send the email
                        $this->sendReminderEmail(array(
                            'Name' => $message->Name,
                            'Email' => $email,
                            'Order' => $message->Cart()
                        ));

                        // Save to our processed array - this covers old messages
                        $processedEmails[] = $email;

                    } else {
                        echo sprintf('Email already processed: <%s>', $email) . $this->br;
                    }

                    $message->setField('Status', 'Processed');
                    $message->write();

                    echo $this->hr;
                }
            } else {
                echo 'No abandoned carts found.' . $this->br;
            }
        } else {
            echo 'Not enabled.' . $this->br;
        }
    }

    private function sendReminderEmail($data)
    {
        /** =========================================
         * @var Email $email
         * @var SiteTree $shippingPage
         * @var Order $order
         * @var DataList $items
         * @var OrderItem $orderItem
         * ========================================*/

        $email = Email::create($this->email_reply_to, $data['Email'], $this->email_subject);

        $shippingPage = SiteTree::get_one('ShippingPage');

        if ($shippingPage && $shippingPage->exists()) {
            $link = $shippingPage->AbsoluteLink();
        } else {
            $link = Controller::join_links(Director::absoluteBaseURL(), 'cart');
        }

        $data = array_merge($data, array(
            'HeaderImage' => $this->header_image,
            'FooterImage' => $this->footer_image,
            'Subject' => $this->email_subject,
            'IntroContent' => $this->email_intro,
            'FooterContent' => $this->email_footer,
            'CartLink' => $link
        ));

        if (isset($data['Order'])) {
            $order = $data['Order'];
            $items = $order->Items();
            $products = array();
            foreach ($items as $orderItem) {
                $products[] = $orderItem->TableTitle();
            }
            $data['IntroContent'] = str_replace('[cart_items]', implode(', ', $products), $data['IntroContent']);
        }

        $email->setTemplate('AbandonedCartEmail')
            ->populateTemplate($data)
            ->send();
    }

    /**
     * Return a string for a CRON expression
     *
     * @return string
     */
    public function getSchedule()
    {
        return "*/15 * * * *";
    }
}
