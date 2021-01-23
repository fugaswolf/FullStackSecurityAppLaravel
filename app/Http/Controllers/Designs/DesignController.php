<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{

    public function update(Request $request, $id)
    {

        $design = Design::find($id);

        //kan de user die request stuurt dit design updaten?
        $this->authorize('update', $design);


        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
        ]);
        

        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title), // hello world => hello-world 
            'is_live' => ! $design->upload_successful ? false : $request->is_live
        ]);

        // apply the tags
        $design->retag($request->tags);
        
        return new DesignResource($design);
    }

    public function destroy($id)
    {
        
        $design = Design::findOrFail($id);

        //kan de user die request stuurt dit design deleten?
        $this->authorize('delete', $design);

        // alle files deleten die gelinkt zijn met deze record
        foreach(['thumbnail', 'large', 'original'] as $size){
            // nagaan of de files bestaan
            if(Storage::disk($design->disk)->exists("uploads/designs/{$size}/".$design->image)){
                // files verwijderen
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/".$design->image);
            }
        }
        // record verwijderen uit de db
        $design->delete();
        return response()->json(['message' => 'Record deleted'], 200);

    }

}
