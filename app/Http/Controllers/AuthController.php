<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\UpdatePasswordRequest;

// test back from sarah essam
// test1
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

   
     public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            // 'email' => 'required|email',
            // 'password' => 'required|string|min:6',
            'name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Email or Password doesn\'t exist'], 401);
        }
       
     DB::table('student')->where('studentId', '=',$request['name'])->update(array('loginToken'=>$token));
     DB::table('professor')->where('professorId', '=',$request['name'])->update(array('loginToken'=>$token));
     DB::table('users')->where('name', '=',$request['name'])->update(array('loginToken'=>$token));
     DB::table('ta')->where('TAId', '=',$request['name'])->update(array('loginToken'=>$token));
            return $this->createNewToken($token);
      
     
    
    }
    
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            // 'email' => 'required|string|email|max:100|unique:users',
            // 'password' => 'required|string|min:6',
            'email' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    

    // ******************************************************************
    public function getStudentInfo($token)
    {
        $StudentInfo = DB::table('student')->where('loginToken', '=', $token)->get();
        return $StudentInfo;
      
    }

    public function getProfessorInfo($token)
    {
        $ProfessorInfo = DB::table('professor')->where('loginToken', '=', $token)->get();
        return $ProfessorInfo;
     
    }

    public function getTaInfo($token)
    {
        $TaInfo = DB::table('ta')->where('loginToken', '=', $token)->get();
        return $TaInfo;
        
    }
    public function getAdminInfo($token)
    {
        $ProfessorInfo = DB::table('users')->where('loginToken', '=', $token)->get();
        return $ProfessorInfo;
     
    }

    public function getUserType($token)
    {
        $userType = DB::table('users')->where('loginToken', '=', $token)->get('Type');
        return $userType;
        
    }
    public function getUserInfo($token)
    {
        $TAs=DB::table('ta')->join('users','ta.userID','=','users.id')
        ->select('ta.userID as id','ta.TAName AS name','users.Type','ta.TAId as logginUserID')
        ->where('users.loginToken', '=', $token)->
        get();
       
        $students = DB::table('student')->join('users','student.userID','=','users.id')
    ->select('student.userID as id','student.studentName AS name','users.Type','student.studentId as logginUserID')
    ->where('users.loginToken', '=', $token)->
    get();

    $professors = DB::table('professor')->join('users','professor.userID','=','users.id')
    ->select('professor.userID as id','professor.professorName AS name','users.Type','professor.professorId as logginUserID')
    ->where('users.loginToken', '=', $token)->
    get();


    $userInformation=$TAs->concat($students)
    ->concat($professors)
    ->sortBy('name')
    ->values();
        return $userInformation;
        
    }
    
}