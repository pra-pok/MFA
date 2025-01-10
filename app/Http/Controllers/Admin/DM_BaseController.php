<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App;
use Illuminate\Support\Facades\File;

class DM_BaseController extends Controller
{
    protected $base_route;
    protected $view_path;
    protected $panel;
    protected $folder;

    public function __construct()
    {

    }
    /**
     * loads the default view for every model
     *
     * call from child controller
     *
     *
     */
    protected function loadView($view_path)
    {
        // Using Closure based composers...
        View::composer($view_path, function ($view) {
            $view->with('flash_message_path', 'admin.common.flash_messages');
            $view->with('_base_route', $this->base_route);
            $view->with('_view_path', $this->view_path);
            $view->with('_panel', $this->panel);
            $view->with('folder', property_exists($this, 'folder') ? $this->folder : '');
        });
        return $view_path;
    }

    /**
     * To upload single file
     *
     * call from child controller
     *
     */
    protected function uploadFile($folder_path, $image_prefix_path, $name, $request)
    {
        if ($request->hasFile($name)) {
            $file = $request->file($name);
            $file_name = time() . '_' . rand() . '_' . $file->getClientOriginalName();
            //    $file_extension = $file->getClientOriginalExtension();
            $file->move($folder_path, $file_name);
            $file_path = $image_prefix_path . $file_name;
            return $file_path;
        }
        return false;
    }
    /**
     * Method to upload Images
     *
     * call from child controller
     *
     */
    protected function uploadImage(Request $request, $folder_path_image, $prefix_path_image, $name, $image_width = '', $image_height = '')
    {
        if ($request->hasFile($name)) {
            $this->createFolder($folder_path_image);
            $file = $request->file($name);
            $file_name = time() . '_' . rand() . '_' . $file->getClientOriginalName();
            //    $file_extension = $file->getClientOriginalExtension();
            if (isset($image_height) && isset($image_width)) {
                $file_resize = Image::make($file->getRealPath());
                $file_resize->resize($image_width, $image_height);
                $file_resize->save($folder_path_image . $file_name);
            } else {
                $file->move($folder_path_image, $file_name);
            }
            $file_path = $prefix_path_image . $file_name;
            return $file_path;
        }
        return false;
    }
    /**
     * upload Multiple Files
     * call from child controller
     */
    protected function uploadMultipleFiles(Request $request, $folder_path_file, $prefix_path_file, $name)
    {
        if ($request->hasFile($name)) {
            $this->createFolder($folder_path_file);
            $files = $request->file($name);
            foreach ($files as $file) {
                $file_name = time() . '_' . rand() . '_' . $file->getClientOriginalName();
                //    $file_extension = $file->getClientOriginalExtension();
                $file->move($folder_path_file, $file_name);
                $files_path[] = $prefix_path_file . $file_name;
            }
            return $files_path;
        }
        return false;
    }

    /**
     * if folder does not exist then create new and give permission
     *  */
    protected function createFolder($path)
    {
        if (!file_exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
    }

    /** Parse the json data for the menu
     * Used for nestable
     */
    protected function parseJsonArray($jsonArray, $parentID = Null)
    {
        $return = array();
        foreach ($jsonArray as $subArray) {
            $returnSubSubArray = array();
            if (isset($subArray->children)) {
                $returnSubSubArray = $this->parseJsonArray($subArray->children, $subArray->id);
            }
            $return[] = array('id' => $subArray->id, 'parentID' => $parentID);
            $return = array_merge($return, $returnSubSubArray);
        }
        return $return;
    }

    protected function uniqueParseJsonArray($jsonArray, $parentID = Null)
    {
        $return = array();
        foreach ($jsonArray as $subArray) {
            $returnSubSubArray = array();
            if (isset($subArray->children)) {
                $returnSubSubArray = $this->parseJsonArray($subArray->children, $subArray->id);
            }
            $return[] = array('id' => $subArray->id, 'unique' => $subArray->unique, 'parentID' => $parentID);
            $return = array_merge($return, $returnSubSubArray);
        }
        return $return;
    }


    public function uploadMultiples($request, $folder_path, $prefix, $field){
        $files = $request->file($field);
        $uploadedFiles = [];

        foreach($files as $file){
            $filename = $prefix.'_'.time().'_'.$file->getClientOriginalName();
            $file->move(public_path($folder_path), $filename);
            $uploadedFiles[] = $filename;
        }

        return $uploadedFiles;
    }
    protected function uploadMultipleImages(Request $request, $folder_path_image, $prefix_path_image, $name)
    {
        if ($request->hasFile($name)) {
            $this->createFolder($folder_path_image);
            $files = $request->file($name);
            foreach ($files as $file) {
                $file_name = time() . '_' . rand() . '_' . $file->getClientOriginalName();
                //    $file_extension = $file->getClientOriginalExtension();
                $file->move($folder_path_image, $file_name);
                $files_path[] = $prefix_path_image . $file_name;
            }
            return $files_path;
        }
        return false;
    }
}
