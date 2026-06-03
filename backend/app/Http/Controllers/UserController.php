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
            'role' => 'required|in:admin,user,driver',
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
    public function updateProfile(Request $request){
        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
            'address' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // បន្ថែម validation សម្រាប់រូបភាព
        ]);

        $data = [
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ];

        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->has('address')) {
            $data['address'] = $request->address;
        }

        if ($request->hasFile('avatar')) {
            // លុបរូបចាស់បើមាន
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            // រក្សាទុករូបថ្មី
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return response()->json(['message' => 'បន្ថែមព័ត៌មានអ្នកប្រើប្រាស់ជោគជ័យ!', 'user' => $user]);
    }
}