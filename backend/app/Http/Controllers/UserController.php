<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ១. ទាញយកបញ្ជី User ទាំងអស់
    public function index()
    {
        // ប្រើ Paginate ដើម្បីកុំឱ្យធ្ងន់ Server ពេលមាន User ច្រើន
        $users = User::latest()->paginate(10);
        return response()->json($users);
    }

    // ២. កែប្រែសិទ្ធិ User (ឧ. ពី user ធម្មតា ទៅ admin)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return response()->json(['message' => 'កែប្រែសិទ្ធិអ្នកប្រើប្រាស់ជោគជ័យ!', 'user' => $user]);
    }

    // ៣. លុប User ចោល
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // មិនអនុញ្ញាតឱ្យ Admin លុបខ្លួនឯងចោលទេ
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'អ្នកមិនអាចលុបគណនីខ្លួនឯងបានទេ!'], 400);
        }

        $user->delete();
        return response()->json(['message' => 'លុបអ្នកប្រើប្រាស់ជោគជ័យ!']);
    }
}