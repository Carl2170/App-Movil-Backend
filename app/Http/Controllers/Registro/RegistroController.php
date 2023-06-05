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
use Carbon\Carbon;

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

        $codigo = rand(100000, 999999);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tiempo_codigo' => Carbon::now(),
            'codigo_de_verificacion' =>$codigo
        ]);

        $email = $request->email;
        $mail = new PHPMailer(true);
     
        try {

            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'dilker72@gmail.com';                     //SMTP username
            $mail->Password   = 'opfexbzzwbbagutj';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you 
            $mail->setFrom('dilker72@gmail.com', 'Denuncias Santa Cruz');
            $mail->addAddress($email);     //Add a recipient
          
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Verificacion de Email';
            $mail->Body    = "Su codigo de verificacion es :  ". $codigo;
            $mail->send();

            return response()->json([
                'res' => True,
                'mensaje' => 'Verifica Tu email'
            ]);
            
        } catch (Exception $e) {
            
            return response()->json([
                'res' => false,
                'mensaje' => 'Ocurrio Un Problemas con los datos',
                'status' => 500,
                
            ],500);

        }

    
        
    }
    public function logout(){
        $user = Auth::where('id',Auth::user()->id)->first();
        $user->tokens()->delete();
        return response([
            'message'=>'Token eliminado'
        ]);  
    }




    public function verificar(Request $request){
        $user = User::where('email',$request['email'])->first();
       
        $horaActual = Carbon::now();
        $minutos = $horaActual->diffInMinutes($user->tiempo_codigo);
           
        if($minutos<=1){
                if($request['codigo'] == $user->codigo_de_verificacion){
                    $user->verificacion_email=1;
                    $user->estado=1;
                    $user->save();
                    return response()->json([
                        'res' => True,
                        'mensaje' => 'Verificacion Exitosa',
                        'tiempo' => $minutos,
                        'hora'=>$horaActual,
                        'chima'=>$user->tiempo_codigo
                    ]);
                }
                else{
                    return response()->json([
                        'res' => False,
                        'mensaje' => 'Codigo de Verificacion incorrecto'
                    
                    ]);
                }
            }
        
        return response()->json([
            'res' => False,
            'mensaje' => 'Tiempo de verificacion excedido',
            'tiempo' => $minutos,
            'hora'=>$horaActual,
            'chima'=>$user->tiempo_codigo
        ]);

    }



    public function reenviar(Request $request){
        $user = User::where('email',$request['email'])->first();
        $nuevoCodigo =rand(100000, 999999);

        $user->codigo_de_verificacion=$nuevoCodigo;
        $user->tiempo_codigo=Carbon::now();
        $user->save();

        $email = $request->email;
        $mail = new PHPMailer(true);
     
        try {

            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dilker72@gmail.com';
            $mail->Password   = 'opfexbzzwbbagutj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->setFrom('dilker72@gmail.com', 'Denuncias Santa Cruz');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Verificacion de Email';
            $mail->Body    = "Su codigo de verificacion es : ".$nuevoCodigo;
            $mail->send();

            return response()->json([
                'res' => True,
                'mensaje' => "Codigo de verificacion Reenviado",
            ]);
        
        } catch (Exception $e) {
            
            return response()->json([
                'res' => false,
                'mensaje' => 'Ocurrio Un Problemas con los datos',
                'status' => 500,
                
            ],500);

        }
    }
}