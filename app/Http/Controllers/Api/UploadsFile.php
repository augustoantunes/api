<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;


trait UploadsFile
{
	/*
        Objective:Save a file in the current request to a given path if set
        return the hash name of the file or false if there was an error saving the file
    */
    function uploadFile(Request $request, $fieldName, $pathToSave = 'public/files'){
        if ($request->file($fieldName)->isValid()) {
            return $request->{$fieldName}->store($pathToSave) ? $request->{$fieldName}->hashName() : false;
        }
        return false;
    }
}
