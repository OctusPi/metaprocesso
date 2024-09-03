<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class Attachments extends Controller
{
    public function __construct()
    {
        parent::__construct(Attachment::class, User::MOD_ATTACHMENT['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['origin', 'protocol'],
            ['updated_at']
        );
    }

    public function save(Request $request)
    {
        $upload = new Uploads($request, ['file' => ['nullable' => false]]);

        if ($request->id) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->file);
        }

        return $this->base_save($request, $upload->mergeUploads([]));
    }


    public function download(Request $request)
    {
        $attachment = Data::findOne(new Attachment(), ['id' => $request->id]);
        if ($attachment && $attachment->file) {
            return response()->download(
                storage_path("uploads/$attachment->file"),
                "$attachment->protocol.pdf"
            );
        }

        return response()->json(Notify::warning('Arquivo Indisponível'), 404);
    }
}
