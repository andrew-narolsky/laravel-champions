<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'module' => 'required|string|max:20',
            'module_id' => 'nullable|integer',
        ]);

        $existing = Attachment::where('module', $request->module)
            ->where('module_id', $request->module_id)
            ->first();

        if ($existing && $existing->path) {
            Storage::disk('public')->delete($existing->path);
        }

        $file = $request->file('file');
        $path = $file->storeAs('uploads/' . $request->module, $file->getClientOriginalName(), 'public');

        $attachment = Attachment::updateOrCreate(
            [
                'module' => $request->module,
                'module_id' => $request->module_id,
            ],
            [
                'filename' => $file->getClientOriginalName(),
                'path' => 'uploads/' . $request->module,
                'ext' => $file->getClientOriginalExtension(),
                'type' => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'file',
                'size' => $file->getSize(),
            ]
        );

        return response()->json([
            'success' => true,
            'id' => $attachment->id,
            'url' => asset($path),
        ]);
    }

    public function destroy(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->path . '/' . rawurlencode($attachment->filename));
        $attachment->delete();

        return response()->json(['success' => true]);
    }
}
