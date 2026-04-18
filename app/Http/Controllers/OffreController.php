<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreationRequestOffre;
use App\Models\Offre;
use Illuminate\Http\Request;

class OffreController extends Controller
{public function index(Request $request)
{
    $query = Offre::query()->where('actif', true);

    if ($request->filled('localisation')) {
        $query->where('localisation', $request->localisation);
    }

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    $offres = $query->orderBy('created_at', 'desc')->paginate(10);
    if ($offres->isEmpty()) {
   response()->json(['message' => 'No offers found'], 200);
}
    return response()->json(['offers' => $offres], 200);
}
    public function detail($id){
        $offre= Offre::find($id);
            if (!$offre) {
        return response()->json(['message' => 'Offer not found'], 404);
    }
      return response()->json(['offer' => $offre], 200);

    }
    public function creation(CreationRequestOffre $request){
        $user_id=auth('api')->user()->id;
        $validedata= $request->validated();
        $validedata['user_id']=$user_id;
        $offre=Offre::create( $validedata);
       return response()->json(['message' => 'Offer created', 'offer' => $offre], 201);

    }
    public function update(Request $request,$id){
      $offre=offre::find($id);
      if (!$offre) {
         return response()->json(['message' => 'Offer not found'], 404);
      }
      $user_id=auth('api')->user()->id;
      if($user_id!=$offre->user_id){
        return response()->json(['message' => 'Not allowed'], 403);
      }
     $validat = $request->validate([
        'titre' => 'sometimes|string',
        'description' => 'sometimes|string',
        'localisation' => 'nullable|string',
        'type' => 'nullable|string'
      ]);
   
      
    $offre->update($validat);
    return response()->json(['message' => 'Offer updated', 'offer' => $offre], 200);

 
}
    public function destroy($id){
     $offre=offre::find($id);
      if (!$offre) {
       return response()->json(['message' => 'Offer not found'], 404);
      }
      $user_id=auth('api')->user()->id;
      if($user_id!=$offre->user_id){
    return response()->json(['message' => 'Not allowed'], 403);
    }
    $offre->delete();
    return response()->json(['message' => 'Offer deleted'], 200);


  }
}