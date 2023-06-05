<?php

namespace App\Http\Controllers\Registro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception as GlobalException;
use Illuminate\Support\Facades\Hash;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\Auth;

class RegistroController extends Controller
{



    public function login(LoginRequest $request){
        $user = User::where('email',$request['email'])->first();
            if( !$user || !Hash::check($request['password'],$user->password)){

                return response()->json([
                    'res' => false,
                    'mensaje' => "Datos incorrectos",
                   
                ]);
            }
            $token = $user->createToken($request['email'])->plainTextToken;

            return response()->json([
                'res' =>true,
                'token' => $token
            ],201);
    }

    public function register(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('Token')->plainTextToken;
        $email = $request->email;
        $mail = new PHPMailer(true);
     
        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            echo("paso");
            $mail->isSMTP();
            echo("paso");
            $mail->Host       = 'smtp.gmail.com';
            echo("paso");
            $mail->SMTPAuth   = true;
            echo("paso");
            $mail->Username   = 'dilker72@gmail.com';
            echo("paso");
            $mail->Password   = 'opfexbzzwbbagutj';
            echo("paso");
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            echo("paso");
            $mail->Port       = 465;
            echo("paso");
            $mail->setFrom('dilker72@gmail.com', 'Denuncias Santa Cruz');
            echo("paso");
            $mail->addAddress($email);
            echo("paso");
            $mail->isHTML(true);
            echo("paso");
            $mail->Subject = 'Verificacion de Email';
            echo("paso");
            $mail->Body    = "Su codigo de verificacion es para el sistema de Denuncias es :  ". "44456";
            echo("pasoulti");
            $mail->send();
            echo("paso");
        } catch (Exception $e) {
            
            return response()->json([
                'res' => false,
                'mensaje' => 'Ocurrio Un Problemas con los datos',
                'status' => 500,
                
            ],500);

        }

        return response()->json(['token' => $token, 'user'=> $user], 200);
        
    }
    public function logout(){
        $user = Auth::where('id',Auth::user()->id)->first();
        $user->tokens()->delete();
        return response([
            'message'=>'Token eliminado'
        ]);  
    }
}