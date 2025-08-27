<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    public function index()
    {
        return view('admin.file-manager');
    }

    public function getFiles(Request $request)
    {
        $path = $request->get('path', '');
        
        try {
            $files = [];
            $directories = [];
            
            // Get all files and directories in the current path
            $allFiles = Storage::disk('public')->files($path);
            $allDirectories = Storage::disk('public')->directories($path);
            
            // Process directories
            foreach ($allDirectories as $dir) {
                $dirName = basename($dir);
                $directories[] = [
                    'name' => $dirName,
                    'type' => 'directory',
                    'path' => $dir,
                    'size' => '-',
                    'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($dir))
                ];
            }
            
            // Process files
            foreach ($allFiles as $file) {
                $fileName = basename($file);
                $fileSize = $this->formatBytes(Storage::disk('public')->size($file));
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                
                $files[] = [
                    'name' => $fileName,
                    'type' => 'file',
                    'extension' => $extension,
                    'path' => $file,
                    'size' => $fileSize,
                    'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file)),
                    'url' => asset('storage/' . $file)
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'directories' => $directories,
                    'files' => $files,
                    'currentPath' => $path
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading files: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'path' => 'nullable|string'
        ]);

        try {
            $file = $request->file('file');
            $path = $request->get('path', '');
            
            // Debug logging
            Log::info('File upload debug', [
                'original_filename' => $file->getClientOriginalName(),
                'path_parameter' => $path,
                'path_empty' => empty($path),
                'path_length' => strlen($path)
            ]);
            
            $fileName = $file->getClientOriginalName();
            $filePath = $path ? $path . '/' . $fileName : $fileName;
            
            Log::info('File paths calculated', [
                'fileName' => $fileName,
                'filePath' => $filePath,
                'target_path' => $path
            ]);
            
            // Check if file already exists
            if (Storage::disk('public')->exists($filePath)) {
                $fileName = $this->generateUniqueFileName($fileName, $path);
                $filePath = $path ? $path . '/' . $fileName : $fileName;
                
                Log::info('File exists, generated unique name', [
                    'new_fileName' => $fileName,
                    'new_filePath' => $filePath
                ]);
            }
            
            // Use putFileAs with the correct path
            if (empty($path)) {
                // Upload to root of public storage
                $result = Storage::disk('public')->putFileAs('', $file, $fileName);
            } else {
                // Upload to specific subdirectory
                $result = Storage::disk('public')->putFileAs($path, $file, $fileName);
            }
            
            Log::info('File upload result', [
                'result' => $result,
                'final_path' => $path,
                'final_filename' => $fileName,
                'empty_path' => empty($path)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'fileName' => $fileName
            ]);
            
        } catch (\Exception $e) {
            Log::error('File upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string'
        ]);

        try {
            $folderName = $request->get('name');
            $path = $request->get('path', '');
            
            $folderPath = $path ? $path . '/' . $folderName : $folderName;
            
            // Check if folder already exists
            if (Storage::disk('public')->exists($folderPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder already exists'
                ], 400);
            }
            
            Storage::disk('public')->makeDirectory($folderPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteItem(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'type' => 'required|in:file,directory'
        ]);

        try {
            $path = $request->get('path');
            $type = $request->get('type');
            
            if ($type === 'file') {
                Storage::disk('public')->delete($path);
            } else {
                Storage::disk('public')->deleteDirectory($path);
            }
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile(Request $request)
    {
        $path = $request->get('path');
        
        try {
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'File not found');
            }
            
            $file = Storage::disk('public')->get($path);
            $fileName = basename($path);
            
            return Response::make($file, 200, [
                'Content-Type' => mime_content_type(Storage::disk('public')->path($path)),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
            
        } catch (\Exception $e) {
            abort(500, 'Download failed: ' . $e->getMessage());
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        if ($size == 0) return '0 B';
        
        $power = floor(log($size, 1024));
        
        return round($size / pow(1024, $power), $precision) . ' ' . $units[$power];
    }

    private function generateUniqueFileName($fileName, $path)
    {
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $counter = 1;
        
        do {
            $newFileName = $name . '_' . $counter . '.' . $extension;
            $fullPath = $path ? $path . '/' . $newFileName : $newFileName;
            $counter++;
        } while (Storage::disk('public')->exists($fullPath));
        
        return $newFileName;
    }
}
