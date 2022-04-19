<?php

class PDFController
{
    public static function generateUserPDF(){
        include __DIR__.'/../models/User.php';

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token)) reportMissingParam("token");

        $token = $json->token;
        
        try {
            $user = new User();
            $user->constructFromToken($token);
        } catch (Exception $e) {
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    header('Location: /error/401'); exit();
                    break;
                default:
                    header('Location: /error/500'); exit();
                    break;
            }
            die();
        }

        $lname = $json->lastname;
        $fname = $json->firstname;
        $bdate = $json->birthdate;
        $phone = $json->phone;
        $email = $json->email;

        require __DIR__.'/../../lib/pdf/fpdf.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(50, 10, 'Nom', 1, 0);
        $pdf->Cell(140, 10, $lname, 1, 1);

        $pdf->Output();
    }

    public static function generateOrderPDF(){
        //
    }

}