<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Rules\SQLInjectionValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    protected $customerRepo;
    protected $employeeRepo;

    public function __construct(CustomerRepositoryInterface $customerRepo, EmployeeRepositoryInterface $employeeRepo){
        $this->customerRepo = $customerRepo;
        $this->employeeRepo = $employeeRepo;
    }

    public function index(Request $request){
        return view('auth.login');
    }

    public function showRegister(){
        return view('auth.register');
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new SQLInjectionValidate],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'name' => ['required', 'string', 'max:100', new SQLInjectionValidate],
            'phone' => ['required', 'regex:/^0\d{9}$/', new SQLInjectionValidate],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Log::info('Created user', ['user' => $user]);

        $customer = null;

        if ($user) {
            $customer = $this->customerRepo->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $request->input('name'),
                'phone' => $request->input('phone')
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $cookie = Cookie::make(
            name: 'auth_token',
            value: $token,
            minutes: 60,
            path: '/',
            domain: null,
            secure: env('APP_ENV') === 'production',
            httpOnly: true,
            sameSite: 'Lax'
        );

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'customer' => $customer,
            'token' => $token,
        ], 201)->withCookie($cookie);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $remember = $request->boolean('remember');
        $tokenTtl = $remember ? 60 * 24 *30 : 60;

        $cookie = Cookie::make(
            name: 'auth_token',
            value: $token,
            minutes: $tokenTtl,
            path: '/',
            domain: '127.0.0.1',
            secure: env('APP_ENV') === 'production',
            httpOnly: true,
            sameSite: 'Strict'
        );
       

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200)->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $cookie = Cookie::forget('auth_token','/','127.0.0.1');

        return response()->json(['message' => 'Logged out successfully'], 200)->withCookie($cookie);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userData = [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $profile = null;

        switch ($user->role){
            case Role::USER :
                $profile = $this->customerRepo->getCustomerByUserId($userData['id']);
                break;            
            case Role::EMPLOYEE :
                $profile = $this->employeeRepo->getEmployeeByUserId($userData['id']);
                Log::debug('Employee profile result', ['profile' => $profile]);
                break;
            
            default :
                break;
        }

        return response()->json([
            'user' => $userData,
            'profile' => $profile
        ], 200);
    }
}


