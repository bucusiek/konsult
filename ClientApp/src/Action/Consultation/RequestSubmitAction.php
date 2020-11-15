<?php

namespace App\Action\Consultation;

use App\Domain\User\Service\UserAuth;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * Action.
 */
final class RequestSubmitAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserAuth
     */
    private $auth;


    /**
     * @var []
     */
    private $data;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     * @param Session $session The session handler
     * @param UserAuth $auth The user auth
     */
    public function __construct(Responder $responder, Session $session, UserAuth $auth)
    {
        $this->responder = $responder;
        $this->session = $session;
        $this->auth = $auth;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $flash = $this->session->getFlashBag();
        $flash->clear();

        $this->data = (array)$request->getParsedBody();

        if($this->validateForm()){
            $flash->set('success', __('Zgłoszenie zostało przesłane do administratora'));
            $this->postData();
            $this->sendMailToAdmin();
            $response = $response->withStatus(302);
            return $response->withHeader('Location', '../../');

        }
        else{
            $response = $response->withStatus(302);
            return $response->withHeader('Location', '../../request');
        }
    }


    public function validateForm(){
        $validationStatus = true;
        $form = $this->data;

        //check if fields are not empty
        if(empty((string)$form['applicant']) || empty((string)$form['owner']) || empty((string)$form['type']) || empty((string)$form['time']) || empty((string)$form['date']) ) {
            $validationStatus = false;
            $flash = $this->session->getFlashBag();
            $flash->set('error', __('Formularz zawiera błędy. Uzupełnij wszystkie pola'));
        }

        //check is not outdated
        $date = strtotime($form['date']);
        $today = strtotime(date("Y-m-d"));
        if($date <= $today){
            $validationStatus = false;
            $flash = $this->session->getFlashBag();
            $flash->set('error', __('Wprowadzona data jest nieprawidłowa'));
        }

        //check is day is correct with days from config
        $dayOfWeek = date('w', $date);
        $dayOfWeek = $dayOfWeek - 1;
        if($dayOfWeek == -1) $dayOfWeek = 6;

        $days = $this->getDays();
        $daysValidation = false;
        foreach($days as $day){
           if($day['day_of_week'] == ($dayOfWeek)) $daysValidation = true;
        }

        if(!$daysValidation){
            $validationStatus = false;
            $flash = $this->session->getFlashBag();
            $flash->set('error', __('W tym dniu nie odbywają się konsultacje'));
        }

        return $validationStatus;
    }

    public function getDays(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/config");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);


        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }

    public function postData(){
        $request = $this->data;
        $sessionData = $this->session->get('user');
        $token = $sessionData->token;
        $authorization = "Authorization: Bearer " . $token;

        $dateStart = $request['date'] . " " . $request['time'];
        $data = array(
            'Content-Type: application/json',
            $authorization,
            'duration: ' . $request['duration'],
            'id_user: ' .  $request['owner'],
            'id_type: ' .  $request['type'],
            'isAccepted: ' . '0',
            'applicant: ' .  $request['applicant'],
            'dateStart: ' .  $dateStart,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/consultation");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data);

        // execute!
        $response = curl_exec($ch);

        // close the connection, release resources used
        curl_close($ch);

        $jsonResponse = json_decode($response, true);
    }

    public function sendMailToAdmin(){
        $ownerMail = $this->getAdminMail();
        $ownerMail = $ownerMail[0];
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.mailtrap.io';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '62cbfbaf602f48';                     // SMTP username
            $mail->Password   = '1da3f4dc6c6c4a';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('706015bfd7-14b5ba@inbox.mailtrap.io', 'Konsultacje');
            $mail->addAddress($ownerMail['email'], $ownerMail['firstname']);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'System konsultacji - konsultacja wymaga akcji';
            $mail->Body    = '<h1>Nowe zgloszenie!</h1><br/><p>W systemie pojawila sie nowa prosba o konsultacje! Zaloguj sie aby potwierdzic.</p>';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function getAdminMail(){
        $request = $this->data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1111/api/v1/users/" . $request['owner']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);


        $jsonResponse = json_decode($response, true);

        return $jsonResponse['data'];
    }
}
