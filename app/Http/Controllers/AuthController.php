<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    use HttpResponses;

    // Register
    public function register(RegisterUserRequest $request)
{
    $request->validated();

    $status = $request->role == 'admin' ? 'active' : 'pending';

    // Generate a 6-digit verification code
    $verificationCode = mt_rand(100000, 999999);

    // Create the user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'role' => $request->role,
        'status' => $status,
        'password' => Hash::make($request->password),
        'verification_code' => $verificationCode,
        'verification_expires_at' => Carbon::now('Africa/Lagos')->addMinutes(30), // Use the correct time zone
    ]);

    // Send the verification email
    Mail::to($user->email)->send(new VerifyEmail($user, $verificationCode));

    return $this->successResponse([
        'message' => 'Registration was successful. Check your email for verification.',
        'user' => $user,
    ]);
}

    // Verify Email
    public function verifyEmail(Request $request)
{

    // Fetch the user with the given email and verification code
    $user = User::where('email', $request->email)
                ->where('verification_code', $request->verification_code)
                ->first();

    if (!$user) {
               return $this->errorResponse('Invalid verification code.', [], 400);
    }



    // Check if the verification code has expired
    if ($user->verification_expires_at < Carbon::now('Africa/Lagos')) {
        return $this->errorResponse('Verification code has expired.', [], 400);
    }

    // Update email verification
    $user->email_verified_at = now();
    $user->verification_code = null;
    $user->verification_expires_at = null;
    $user->save();

    return $this->successResponse([], 'Email verified successfully.');
}

    // Resend Verification Code
    public function resendVerification(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Generate a new verification code
    $verificationCode = rand(100000, 999999); // Generate a random 6-digit code
    $user->verification_code = $verificationCode;
    $user->verification_expires_at = Carbon::now()->addMinutes(30); // Set expiration time to 30 minutes
    $user->save();

    // Send verification email with new code
    Mail::to($user->email)->send(new VerifyEmail($user, $verificationCode));

    return response()->json(['message' => 'A new verification code has been sent to your email']);
}


    // Login
    public function login(LoginUserRequest $request)
    {
        $request->validated();

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid login details', [], 401);
        }

        $user = User::where('email', $request->email)->first();

        // Check if the email is verified
        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('Please verify your email before logging in.', [], 403);
        }

        return $this->successResponse([
            'user' => $user,
            'token' => $user->createToken('authToken' . $user->id)->plainTextToken,
        ]);
    }
}
