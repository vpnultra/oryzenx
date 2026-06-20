<?php
/**
 * Email Sending Functions
 */

class EmailManager {
    private $from_email;
    private $from_name;

    public function __construct() {
        $this->from_email = ADMIN_EMAIL;
        $this->from_name = SITE_NAME;
    }

    public function sendWelcomeEmail($to_email, $user_name) {
        $subject = 'Welcome to ' . SITE_NAME;
        $message = $this->getWelcomeTemplate($user_name);
        return $this->send($to_email, $subject, $message);
    }

    public function sendOfferNotification($to_email, $domain_name, $offer_price) {
        $subject = 'New Offer on Your Domain - ' . $domain_name;
        $message = $this->getOfferTemplate($domain_name, $offer_price);
        return $this->send($to_email, $subject, $message);
    }

    public function sendPaymentConfirmation($to_email, $order_id, $amount) {
        $subject = 'Payment Confirmation - Order #' . $order_id;
        $message = $this->getPaymentTemplate($order_id, $amount);
        return $this->send($to_email, $subject, $message);
    }

    public function sendContactReply($to_email, $subject, $message) {
        $html_message = "<h2>Message from " . SITE_NAME . "</h2>";
        $html_message .= "<p>" . nl2br(htmlspecialchars($message)) . "</p>";
        return $this->send($to_email, $subject, $html_message);
    }

    private function send($to_email, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">" . "\r\n";

        return mail($to_email, $subject, $message, $headers);
    }

    private function getWelcomeTemplate($user_name) {
        return "<html><body>
            <h2>Welcome to " . SITE_NAME . "!</h2>
            <p>Hello " . htmlspecialchars($user_name) . ",</p>
            <p>Thank you for registering. You can now explore our marketplace and bid on premium domains.</p>
            <p>Happy bidding!</p>
            <p>Best regards,<br>" . SITE_NAME . " Team</p>
        </body></html>";
    }

    private function getOfferTemplate($domain_name, $offer_price) {
        return "<html><body>
            <h2>New Offer Received!</h2>
            <p>You have received a new offer on your domain <strong>" . htmlspecialchars($domain_name) . "</strong></p>
            <p><strong>Offer Price:</strong> \$" . number_format($offer_price, 2) . "</p>
            <p>Please log in to your account to review and respond to the offer.</p>
            <p>Best regards,<br>" . SITE_NAME . " Team</p>
        </body></html>";
    }

    private function getPaymentTemplate($order_id, $amount) {
        return "<html><body>
            <h2>Payment Received!</h2>
            <p>We have received your payment.</p>
            <p><strong>Order ID:</strong> " . htmlspecialchars($order_id) . "</p>
            <p><strong>Amount:</strong> \$" . number_format($amount, 2) . "</p>
            <p>Your payment is currently pending verification. You will be notified once it's approved.</p>
            <p>Best regards,<br>" . SITE_NAME . " Team</p>
        </body></html>";
    }
}
?>