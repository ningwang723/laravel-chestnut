<?php

namespace Chestnut\Dashboard\Uploader;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Uploader
{
    protected $directory;

    public static function registerRoute($router)
    {
        $router->group(['prefix' => 'api', 'middleware' => 'auth:chestnut'], function ($router) {
            $router
                ->post("editor_upload", static::class . "@" . "editorUpload");

            $router
                ->post("upload_files", static::class . "@" . "uploadFiles");

            $router
                ->delete("delete_files", static::class . "@" . "deleteFiles");
        });
    }

    public function editorUpload(Request $request)
    {
        $files = $request->files;

        $response = [];

        foreach ($files as $name => $file) {
            $response[$name] = asset($request->file($name)->store(config("chestnut.dashboard.upload_storage")));
        }

        return ['code' => 200, "message" => 'Upload successed.', "data" => $response];
    }

    public function uploadFiles(Request $request)
    {
        $file = $request->file("file");

        $response = $file->getMimeType() . "@" . asset($file->store(config("chestnut.dashboard.upload_storage")));

        return ['code' => 200, "message" => 'Upload successed.', "data" => $response];
    }

    public function deleteFiles(Request $request)
    {
        $url = $request->url;

        $url = str_replace($request->root() . "/", "", $url);

        Storage::delete($url);

        return ['code' => 200, "message" => "删除成功"];
    }
}
