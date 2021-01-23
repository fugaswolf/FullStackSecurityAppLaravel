<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // validate the request
        // file types & file size (10 MB)
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,jpg,gif,bmp,png', 'max:10240']
        ]); 

        // get the image
        $image = $request->file('image');
        $image_path = $image->getPathName();


        // get the original file name and replace any spaces with '_' + timestamp prefix
        // de timestamp zal de verwarring tussen 2 afbeeldingen met dezelfde filename vermijden
        // Business Cards.png = timestamp()_business_cards.png
        $filename = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
        
        // de afbeelding wordt opgeslagen in een tijdelijke uploads folder (temp)
        // eens de user de afbeelding aanvult met bijhorende data (titel, beschrijving, tags, etc)
        // dan pas zal de afbeelding gepublished kunnen worden, na het publishen zal de file naar een andere uploads folder verplaatst worden
        // hiermee probeer ik de gepublished & unpublished designs van elkaar te scheiden.
        $temp = $image->storeAs('uploads/original', $filename, 'temp');

        // create the database record for the design
        $design = auth()->user()->designs()->create([
             'image' => $filename,
             'disk' => config('site.upload_disk')
        ]);

        
        // dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));
        
        return response()->json($design, 200);

    }
}
