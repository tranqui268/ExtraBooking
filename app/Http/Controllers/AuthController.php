<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Rules\SQLInjectionValidate;
use App\Services\OtpService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $customerRepo;
    protected $employeeRepo;
    protected $otpService;

    public function __construct(
        CustomerRepositoryInterface $customerRepo, 
        EmployeeRepositoryInterface $employeeRepo,
        OtpService $otpService
    ){
        $this->customerRepo = $customerRepo;
        $this->employeeRepo = $employeeRepo;
        $this->otpService = $otpService;   
    }

    public function index(Request $request){
        return view('auth.login');
    }

    public function showRegister(){
        return view('auth.register');
    }

    public function showLoginOtp(){
        return view('auth.loginOtp');
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
        ],[
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email phải đúng định dạng',
            'password.required' => 'Mật khẩu là bắt buộc'
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
            domain: null,
            secure: false,
            httpOnly: true,
            sameSite: 'Lax'
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

    public function sendOtp(Request $request){
        $validator = Validator::make($request->all(),[
            'phone' => ['required','regex:/^(0|\+84)[0-9]{9}$/','exists:customers,phone']
        ],[
            'phone.required' => 'Số điện thoại là bắt buộc',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.exists' => 'Số điện thoại không tồn tại'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],422);
        }

        $phone = $request->phone;

        try {
            $otp = $this->otpService->generateOtp($phone, 'login');

            return response()->json([
                'success' => true,
                'message' => 'Mã OTP đã được gửi đến số điện thoại của bạn',
                'expires_in' => 300
            ]); 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi OTP'
            ], 500);
        }

    }

    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp_code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->otpService->verifyOtp(
            $request->phone,
            $request->otp_code,
            'login'
        );

        if (!$result['success']) {
            return response()->json($result,400);
        }

        $phone = $request->phone;
        $user = User::whereHas('customer', function ($query) use ($phone){
            $query->where('phone',$phone);
        })->first();

        $token = $user->createToken('auth-token')->plainTextToken;
        $tokenTtl = 60;

        $cookie = Cookie::make(
            name: 'auth_token',
            value: $token,
            minutes: $tokenTtl,
            path: '/',
            domain: null,
            secure: false,
            httpOnly: true,
            sameSite: 'Lax'
        );

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200)->withCookie($cookie);
    }

    public function resendOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $otp = $this->otpService->generateOtp($request->phone,'login');

            return response()->json([
                'success' => true,
                'message' => 'Mã OTP mới đã được gửi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi lại OTP'
            ], 500);
        }
    }
}


