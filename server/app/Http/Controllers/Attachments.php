<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Common;
use App\Utils\Notify;
use App\Utils\Uploads;
use Illuminate\Http\Request;

class Attachments extends Controller
{
    public function __construct()
    {
        parent::__construct(Attachment::class, true, Common::MOD_ATTACHMENT['module']);
    }

    public function list()
    {
        return $this->baseList(['origin', 'protocol'], ['updated_at']);
    }

    public function save(Request $request)
    {
        $upload = new Uploads($request, ['file' => ['nullable' => false]]);
        $values = $upload->mergeUploads($request->all());

        return $this->baseSave($values);
    }


    public function download(Request $request)
    {
        $attachment = Attachment::findOrFail($request->id);
        $fileName = $attachment->protocol . '-' . $attachment->id;
        if ($attachment && $attachment->file) {
            return response()->download(storage_path('uploads' . '/' . $attachment->file), $fileName . '.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }
}
