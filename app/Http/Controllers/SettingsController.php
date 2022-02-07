<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use GeoJson\Geometry\Point;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {

$user=auth()->user();
$this->validate($request,[
    'tagline'=>['required'],
    'name'=>['required'],
    'about'=>['required','string','min:20'],
    'latitude'=>['required','numeric','min:-90','max:90'],
    'longitude'=>['required','numeric','min:-90','max:90'],

]);


$user->update([
    'name'=>$request->name,
    'latitude'=>$request->latitude,
    'longitude'=>$request->longitude,
    'available_to_hire'=>$request->available_to_hire,
    'about'=>$request->about,
    'tagline'=>$request->tagline,
]);

return new UserResource($user);
    }
 public function updatePassword(Request $request)
{
    $this->validate($request,[
        'current_password'=>['required',new MatchOldPassword],
        'password'=>['required','confirmed','min:6', new CheckSamePassword]
    ]);

    $request->user()->update([
        'password'=>bcrypt('$request->password')
    ]);

    return response()->json(['message'=>'Password updates'],200);
}
}
