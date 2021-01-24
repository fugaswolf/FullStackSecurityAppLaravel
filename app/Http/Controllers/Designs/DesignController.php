<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\{
    IsLive,
    LatestFirst,
    ForUser,
    EagerLoad
};

class DesignController extends Controller
{

    protected $designs;
    
    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new ForUser(1),
            new EagerLoad(['user', 'comments'])
        ])->all();
        return DesignResource::collection($designs);
    }


    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }


    public function update(Request $request, $id)
    {

        $design = $this->designs->find($id);

        //kan de user die request stuurt dit design updaten?
        $this->authorize('update', $design);


        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
        ]);
        

        $design = $this->designs->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title), // hello world => hello-world 
            'is_live' => ! $design->upload_successful ? false : $request->is_live
        ]);

        // apply the tags
        $this->designs->applyTags($id, $request->tags);
        
        return new DesignResource($design);
    }

    public function destroy($id)
    {
        
        $design = $this->designs->find($id);

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
        $this->designs->delete($id);
        return response()->json(['message' => 'Record deleted'], 200);

    }

    public function like($id)
    {
        $total = $this->designs->like($id);
        return response()->json([
            'message' => 'Successful',
            'total' => $total
        ], 200);
    }

    public function checkIfUserHasLiked($designId)
    {
        $isLiked = $this->designs->isLikedByUser($designId);
        return response()->json(['liked' => $isLiked], 200);
    }

}
