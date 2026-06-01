<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * មុខងារសម្រាប់ Login និងបង្កើត Token
     */
    public function store(LoginRequest $request)
    {
        // ១. ផ្ទៀងផ្ទាត់អ៊ីមែល និងលេខសម្ងាត់
        $request->authenticate();

        // ២. ទាញយកទិន្នន័យ User ដែលបាន Login ត្រូវ
        $user = $request->user();

        // ៣. បង្កើត Token ថ្មីសម្រាប់គាត់
        $token = $user->createToken('API_TOKEN')->plainTextToken;

        // ៤. បញ្ជូន Token នោះត្រលប់ទៅឱ្យ Frontend (ឬ Postman)
        return response()->json([
            'message' => 'ចូលប្រព័ន្ធជោគជ័យ!',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * មុខងារសម្រាប់ Logout (លុប Token ចោល)
     */
    public function destroy(Request $request)
    {
        // លុប Token ដែលកំពុងប្រើនេះចោលពីក្នុង Database
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'អ្នកបានចាកចេញពីប្រព័ន្ធដោយជោគជ័យ!'
        ]);
    }
}