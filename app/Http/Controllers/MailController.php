<?php

namespace App\Http\Controllers;

use Mail;
use Illuminate\Http\Request;
use App\Mail\MailModel;
use TransportException;
use Exception;
use App\Models\User;

class MailController extends Controller
{

    function getRandomStringRandomInt($length = 16)
    {
        $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($stringSpace, '8bit') - 1;
        for ($i = 0; $i < $length; ++ $i) {
            $pieces[] = $stringSpace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
    function send(Request $request) {

        $missingVariables = [];
        $requiredEnvVariables = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
        ];
    
        foreach ($requiredEnvVariables as $envVar) {
            if (empty(env($envVar))) {
                $missingVariables[] = $envVar;
            }
        }
    
        if (empty($missingVariables)) {

            $mailData = [
                'token' => $this->getRandomStringRandomInt(),
                'name' => $request->name,
                'email' => $request->email,
            ];

            $id = User::select('id')->where('email',$request->email);
            
            $user = User::find($id);
            if($user){
            $user->password = $mailData['token'];
            $user->save();}
            else {
                $status = 'Error!';
                $message = 'The email you entered is not registered';
                $request->session()->flash('status', $status);
                $request->session()->flash('message', $message);
                return redirect('/login');
            }
            
            try {
                Mail::to($request->email)->send(new MailModel($mailData));
                $status = 'Success!';
                $message = $request->name . ', an email has been sent to ' . $request->email;
            } catch (TransportException $e) {
                $status = 'Error!';
                $message = 'SMTP connection error occurred during the email sending process to ' . $request->email;
            } catch (Exception $e) {
                $status = 'Error!';
                $message = 'An unhandled exception occurred during the email sending process to ' . $request->email;
            }

        } else {
            $status = 'Error!';
            $message = 'The SMTP server cannot be reached due to missing environment variables:';
        }

        $request->session()->flash('status', $status);
        $request->session()->flash('message', $message);
        $request->session()->flash('details', $missingVariables);
        return redirect('/homepage');
    }



}
